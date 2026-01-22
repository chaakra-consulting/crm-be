<?php

namespace App\Http\Controllers;

use App\Models\CompanyBukukas;
use App\Models\Lead;
use App\Models\ProjectBukukas;
use App\Traits\ResponseFactory;
use Illuminate\Http\Request;

use function Termwind\parse;

class LeadController extends Controller
{
    use ResponseFactory;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Return all leads data
        $leads = Lead::with('source')->get();
        $data = [];
        foreach ($leads as $key => $value) {
            $company_data = CompanyBukukas::where('id', '=', $value->company_id)->first();
            $data[] =
                [
                    'id' => $value->id,
                    'name' => $value->name,
                    'email' => $value->email,
                    'profile_pic' => $value->profile_pic,
                    'type' => $value->type,

                    'company_name' => $company_data ? $company_data->name : '-',
                    'company_address' => $company_data ? $company_data->address : '-',

                    'ad_source' => $value->source->name,
                    'instagram' => $value->instagram,
                    'facebook' => $value->facebook,
                    'twitter' => $value->twitter,



                    'phone_number' => $value->phone_number,
                    'status' => $value->status,
                    'created_at' => $value->created_at,
                    'updated_at' => $value->updated_at,
                ];
        }

        return $this->successResponseData("Leads Data", $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $data = array(
            'name' => $input['name'],
            'email' => $input['email'],
            'type' => $input['type'],
            'company_id' => $input['company_id'] == null ? 0 : (int) $input['company_id'],
            'ad_source' => $input['adSourceInput'],
            'instagram' => $input['instagram'],
            'facebook' => $input['facebook'],
            'twitter' => $input['twitter'],
            'phone_number' => $input['phoneNumber'],
        );
        Lead::create($data);
        return $this->successResponse("Lead Created");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lead $lead)
    {
        $input = $request->all();
        $data = array(
            'status' => $input['status'],
            'name' => $input['name'],
            'email' => $input['email'],
            'type' => $input['type'],
            'company_id' => $input['company_id'] == null ? 0 : (int) $input['company_id'],
            'ad_source' => $input['adSourceInput'],
            'instagram' => $input['instagram'],
            'facebook' => $input['facebook'],
            'twitter' => $input['twitter'],
            'phone_number' => $input['phoneNumber'],
        );
        $lead->update($data);
        return $this->successResponse("Lead Updated");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        $lead->delete();
        return $this->successResponse("Lead Deleted");
    }

    function company_lead(Request $request)
    {
        $q = $request->q;
        $company = CompanyBukukas::with('local');
        if ($q != '' && $request->has($q)) {
            $company->where('name', 'LIKE', '%' . $q . '%');
        }
        $company = $company->get();
        return $this->successResponseData("Company Data", $company);
    }

    function status_update(Request $request, Lead $lead)
    {
        $lead->update(['status' => $request->status]);
        return $this->successResponse("Status Updated");
    }
}
