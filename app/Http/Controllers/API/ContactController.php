<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactSocialMedia;
use App\Models\ContactTag;
use App\Models\SocialMedia;
use App\Models\Tag;
use App\Models\User;
use App\Services\Helpers;
use App\Services\Remappers;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\ContactService;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        if ($request->daterange) {
            [$startRaw, $endRaw] = explode(' - ', $request->daterange);
            $startFormatted = Carbon::createFromFormat('m/d/Y', trim($startRaw))->format('d/m/Y');
            $endFormatted   = Carbon::createFromFormat('m/d/Y', trim($endRaw))->format('d/m/Y');

            $start = Carbon::createFromFormat('d/m/Y', $startFormatted)->startOfDay();
            $end   = Carbon::createFromFormat('d/m/Y', $endFormatted)->endOfDay();
        }else{
            $start = null;
            $end = null;
        }

        $owners = $request->owners ? explode(',', $request->owners) : [];
        $tags = $request->tags ? explode(',', $request->tags) : [];
        $company_id = $request->company_id ? $request->company_id : null;
        $company_bukukas_id = $request->company_bukukas_id ? $request->company_bukukas_id : null;
        $no_company = $request->no_company ? $request->no_company : false;

        $contacts = Contact::filterDateRange($start,$end)
                            ->filterNoCompany($no_company)
                            ->filterByCompany($company_id)
                            ->filterByCompanyBukukas($company_bukukas_id)
                            ->filterOwners($owners)
                            ->filterTags($tags)
                            ->where('is_active',true)
                            ->latest()->get();

        $remapper = new Remappers();
        $remapContacts = $remapper->remapContacts($contacts);

        return response()->json($remapContacts);
    }

    public function store(Request $request, ContactService $contactService): JsonResponse
    {
        $tagsFormat = Helpers::tagsStringToArray($request->input('tags'));
        $request->merge(['tags' => $tagsFormat]);

        $validated = $request->validate([
            'company_id' => 'nullable',
            'source_id' => 'nullable|exists:sources,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|unique:users,email|email|max:255',
            'title_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable',
            'phone_number_1' => 'required|numeric',
            'phone_number_2' => 'nullable|numeric',
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|required_with:address|exists:provinces,id',
            'city_id' => 'nullable|required_with:address|exists:cities,id',
            'tags' => 'nullable',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitterx' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        DB::beginTransaction();

        try {

            $contact = $contactService->createContact($validated,$request->file('photo'));

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('contacts', 'public');
                $contact->update(['photo' => $path]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Contact created successfully.',
                'data' => $contact
            ], 201);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create contact.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact): JsonResponse
    {
        return response()->json($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $tagsFormat = Helpers::tagsStringToArray($request->input('tags'));
        $request->merge(['tags' => $tagsFormat]);

        $request->validate([
            'company_id' => 'nullable',
            'source_id' => 'nullable|exists:sources,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'full_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $request->user_id,
            'title_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable',
            'phone_number_1' => 'required|numeric',
            'phone_number_2' => 'nullable|numeric',
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|required_with:address|exists:provinces,id',
            'city_id' => 'nullable|required_with:address|exists:cities,id',
            'tags' => 'nullable',
            'facebook' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'twitterx' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:5120',
        ]);

        DB::beginTransaction();
        try {

            $contact = Contact::with('user')->findOrFail($id);

            $contact->user->update([
                'name' => $request->full_name,
                'email' => $request->email,
            ]);

            $localCompany = $request->company_id ? Company::where('company_bukukas_id', $request->company_id)->first() : null;

            $contact->update([
                'company_id' => $localCompany ? $localCompany->id : null,
                'source_id' => $request->source_id,
                'owner_user_id' => Auth::id(),
                'name' => $request->full_name,
                'title_name' => $request->title_name,
                'date_of_birth' => $request->date_of_birth,
                'phone_number_1' => $request->phone_number_1,
                'phone_number_2' => $request->phone_number_2,
                'address' => $request->address,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
            ]);

            if ($request->hasFile('photo')) {

                if ($contact->photo && Storage::disk('public')->exists($contact->photo)) {
                    Storage::disk('public')->delete($contact->photo);
                }

                $path = $request->file('photo')->store('contacts', 'public');
                $contact->update(['photo' => $path]);
            }

            ContactTag::where('contact_id', $contact->id)->delete();

            if ($request->tags) {
                foreach ($request->tags as $cTag) {

                    $tagName = $cTag['name'];
                    $slug = Str::slug($tagName);

                    $tag = Tag::firstOrCreate(
                        ['slug' => $slug],
                        [
                            'name' => $tagName,
                            'color' => Helpers::tagsColorTextToColor(null),
                        ]
                    );

                    ContactTag::create([
                        'contact_id' => $contact->id,
                        'tag_id' => $tag->id,
                    ]);
                }
            }

            $socmeds = SocialMedia::whereIn('slug', ['facebook','instagram','twitterx','whatsapp'])->get();

            foreach ($socmeds as $socmed) {

                $detail = $request->{$socmed->slug};

                ContactSocialMedia::updateOrCreate(
                    [
                        'contact_id' => $contact->id,
                        'social_media_id' => $socmed->id
                    ],
                    [
                        'detail' => $detail
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'message' => 'Contact updated successfully.',
                'data' => $contact
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update contact.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->update([
            'is_active' => 0,
        ]);

        return response()->json([
            'message' => 'Contact deleted successfully.'
        ]);
    }
}
