<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\SurveyQuestionRequest;
use App\Models\SurveyQuestion;
use App\Traits\ResponseFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSurveyDetailController extends Controller
{
    use ResponseFactory;
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        $survey_question = SurveyQuestion::where('survey_id', $id)->orderBy('order_number', 'asc')->get();
        return $this->successResponseData("Survey Question Data", $survey_question);
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
    public function store(SurveyQuestionRequest $surveyQuestionRequest)
    {
        $validatedData = $surveyQuestionRequest->validated();
        $order_number = SurveyQuestion::where('survey_id', $validatedData['survey_id'])->max('order_number');
        $validatedData['order_number'] = $order_number + 1;
        DB::beginTransaction();
        try {
            SurveyQuestion::create($validatedData);
            DB::commit();
            return $this->successResponse("Survey Question Created Successfully", 201);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SurveyQuestionRequest $surveyQuestionRequest, SurveyQuestion $surveyQuestion)
    {
        $validatedData = $surveyQuestionRequest->validated();
        DB::beginTransaction();
        try {
            $surveyQuestion->update($validatedData);
            DB::commit();
            return $this->successResponse("Survey Question Updated Successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalErrorResponse("Error: " . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SurveyQuestion $surveyQuestion)
    {
        DB::beginTransaction();
        try {
            $surveyQuestion->delete();
            DB::commit();
            return $this->successResponse("Survey Question Deleted Successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->internalErrorResponse("Error: " . $e->getMessage(), 500);
        }
    }

    public function reorderOrder(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.id' => 'required|integer|exists:survey_questions,id',
            'questions.*.order_number' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->questions as $questionData) {
                SurveyQuestion::where('id', $questionData['id'])
                    ->update(['order_number' => $questionData['order_number']]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Question order updated successfully.'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update order. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
