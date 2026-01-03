<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyBukukas;
use App\Models\Contact;
use App\Services\ContactService;
use App\Services\Helpers;
use App\Services\Remappers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $companies = CompanyBukukas::where('deleted', 0)->latest()->get();

        $remapper = new Remappers();
        $remapCompanies = $remapper->remapCompanies($companies);

        return response()->json($remapCompanies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ContactService $contactService): JsonResponse
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'npwp'   => 'nullable|string|max:50',
            'address'=> 'nullable|string|max:255',
            'jenis'  => 'required|string',
            'bentuk' => 'nullable|string',
            'memo'   => 'nullable|string',
            'province_id' => 'nullable|required_with:address|exists:provinces,id',
            'city_id' => 'nullable|required_with:address|exists:cities,id',
        ]);

        DB::beginTransaction();

        try {

            if (in_array($request->jenis, ['SWASTA'])) {
                $options = [
                    'jenis' => $request->jenis,
                    'bentuk' => $request->bentuk,
                ];
            } else {
                $options = [
                    'jenis' => $request->jenis,
                ];
            }

            $customers = CompanyBukukas::query()
                ->when(isset($options['jenis']), fn($q)=>$q->where('jenis', $options['jenis']))
                ->when(isset($options['bentuk']), fn($q)=>$q->where('bentuk', $options['bentuk']))
                ->get();

            $jenisCode = Helpers::getJenisCode($request->jenis, $request->bentuk);

            $count = str_pad($customers->count() + 1, 2, '0', STR_PAD_LEFT);

            $code = $jenisCode . '.' . $count;

            $company = CompanyBukukas::create([
                "name"       => $request->name,
                "company_name" => '',
                "npwp"       => $request->npwp ?? '',
                "code"       => $code ?? '',
                "address"    => $request->address ?? '',
                "jenis"      => $request->jenis ?? '',
                "bentuk"     => $request->bentuk ?? '',
                "email"      => $request->email ?? '',
                "contact"    => $request->contact ?? '',
                "gender_contact"    => $request->gender_contact ?? '',
                "memo"       => $request->memo ?? '',
                "deleted"    => 0,
                "created_at" => Carbon::now(),
            ]);

            $local = Company::create([
                "company_bukukas_id" => $company->id,
                "province_id"       => $request->province_id,
                "city_id"       => $request->city_id,
            ]);

              // ===== HANDLE CONTACT BARU =====
            $picContactId = $request->pic_contact_id;

            if ($request->pic_contact_id === 'new') {

                $newContact = $request->input('new_contact');
                $tagsFormat = Helpers::tagsStringToArray($newContact['tags']);
                $newContact['tags'] = $tagsFormat;

                $newContact['company_id'] = $company->id;
                $newContact['source_id']?: null;

                if (empty($newContact['has_address'] == true)) {
                    $newContact['address']     = $request->address;
                    $newContact['province_id'] = $request->province_id;
                    $newContact['city_id']     = $request->city_id;
                }

                $contact = $contactService->createContact(
                    $newContact,
                    $company->id
                );

                $picContactId = $contact->id;
            }else{
                $contactService->setCompanyId($picContactId, $local->id);
            }

            $local->update([
                "pic_contact_id"       => $picContactId,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Company created successfully.',
                'data'    => $company,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal Membuat Data Perusahaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CompanyBukukas $company): JsonResponse
    {
        return response()->json($company);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, ContactService $contactService): JsonResponse
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'npwp'   => 'nullable|string|max:50',
            'address'=> 'nullable|string|max:255',
            'jenis'  => 'required|string',
            'bentuk' => 'nullable|string',
            'memo'   => 'nullable|string',
            'province_id' => 'nullable|required_with:address|exists:provinces,id',
            'city_id' => 'nullable|required_with:address|exists:cities,id',
        ]);

        if ($request->pic_contact_id != 'new')$request->validate(['pic_contact_id' => 'nullable|exists:contacts,id']);

        DB::beginTransaction();

        try {
            $company = Company::where('company_bukukas_id',$id)->first();

            if(!$company){
                $company = Company::create([
                    "company_bukukas_id" => $id,
                ]);
            }
            $companyBukukas = $company->bukukas ?? [];

            $shouldGenerateCode = false;

            if (!$companyBukukas->code) {
                $shouldGenerateCode = true;
            }

            $jenisChanged = $companyBukukas->jenis != $request->jenis;
            $bentukChanged = $companyBukukas->bentuk != $request->bentuk;

            if ($request->jenis == 'SWASTA') {
                if ($jenisChanged || $bentukChanged) {
                    $shouldGenerateCode = true;
                }
            } else {
                if ($jenisChanged) {
                    $shouldGenerateCode = true;
                }
            }

            if ($shouldGenerateCode) {

                if ($request->jenis == 'SWASTA') {
                    $customers = CompanyBukukas::where('jenis', $request->jenis)
                                    ->where('bentuk', $request->bentuk)
                                    ->get();
                } else {
                    // lainnya → hanya
                    $customers = CompanyBukukas::where('jenis', $request->jenis)->get();
                }

                $jenisCode = Helpers::getJenisCode($request->jenis, $request->bentuk);
                $count = str_pad($customers->count() + 1, 2, '0', STR_PAD_LEFT);

                $code = $jenisCode . '.' . $count;
            }

            $companyBukukas->update([
                "name"       => $request->name,
                "company_name" => '',
                "npwp"       => $request->npwp ?? '',
                "code"       => $shouldGenerateCode ? $code : $companyBukukas->code ,
                "address"    => $request->address ?? '',
                "jenis"      => $request->jenis ?? '',
                "bentuk"     => $request->bentuk ?? '',
                "email"      => $request->email ?? '',
                "contact"    => $request->contact ?? '',
                "gender_contact"    => $request->gender_contact ?? '',
                "memo"       => $request->memo ?? '',
                "deleted"    => 0,
            ]);

            // ===== HANDLE CONTACT BARU =====
            $picContactId = $request->pic_contact_id;

            if ($request->pic_contact_id === 'new') {

                $newContact = $request->input('new_contact');
                $tagsFormat = Helpers::tagsStringToArray($newContact['tags']);
                $newContact['tags'] = $tagsFormat;

                $newContact['company_id'] = $companyBukukas->id;
                $newContact['source_id']?: null;

                if (empty($newContact['has_address'] == true)) {
                    $newContact['address']     = $request->address;
                    $newContact['province_id'] = $request->province_id;
                    $newContact['city_id']     = $request->city_id;
                }

                $contact = $contactService->createContact($newContact);

                $picContactId = $contact->id;
            }else{
                $contactService->setCompanyId($picContactId, $company->id);
            }

            $company->update([
                // "company_bukukas_id" => $company->id,
                "pic_contact_id" => $picContactId,
                "province_id" => $request->province_id,
                "city_id" => $request->city_id,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Company updated successfully.',
                'data'    => $company,
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Gagal Mengupdate Data Perusahaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $company = CompanyBukukas::findOrFail($id);
        $company->update([
            "deleted"       => 1,
        ]);

        return response()->json([
            'message' => 'Company deleted successfully.'
        ]);
    }
}
