<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyRequest;
use App\Models\Survey;
use App\Services\Helpers;
use App\Traits\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSurveyController extends Controller
{
    use ResponseFactory;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $auth = auth('sanctum')->user();
        $slug = $auth->role_id;
        if ($slug == 7) {
            $survey = Survey::with(['project.bukukas.item'])->where('project_pic', '=', $auth->id)->get();
            return $this->successResponseData("Survey Data PIC", $survey);
        } else {
            $survey = Survey::with(['project.bukukas.item'])->get();
            return $this->successResponseData("Survey Data", $survey);

        }
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
    public function store(SurveyRequest $surveyInsertRequest)
    {
        $validatedData = $surveyInsertRequest->validated();
        DB::beginTransaction();
        try {
            $validatedData['survey_number'] = Helpers::generateSurveyNumber();
            Survey::create($validatedData);
            DB::commit();
            return $this->successResponse("Survey created successfully.", 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalErrorResponse("Error: " . $e->getMessage(), 500);
        }
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
    public function edit(Survey $survey)
    {
        $survey = $survey->with(['project.bukukas.item', 'pic_project'])->find($survey->id);
        return $this->successResponseData("Survey Data", $survey);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SurveyRequest $updateSurveyRequest, Survey $survey)
    {
        $validatedData = $updateSurveyRequest->validated();
        DB::beginTransaction();
        try {
            $survey->update($validatedData);
            DB::commit();
            return $this->successResponse("Survey updated successfully.", 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalErrorResponse("Error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey)
    {
        DB::beginTransaction();
        try {
            $survey->delete();
            DB::commit();
            return $this->successResponse("Survey deleted successfully.", 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalErrorResponse("Error: " . $e->getMessage(), 500);
        }
    }
}
