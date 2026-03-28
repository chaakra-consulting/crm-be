<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\OfferRequest;
use App\Models\Offer;
use App\Traits\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends Controller
{
    use ResponseFactory;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $offers = Offer::all();
        return $this->successResponseData("Offer Data", $offers);
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
    public function store(OfferRequest $offerRequest)
    {
        $input = $offerRequest->validated();
        $attachment = $offerRequest->has('attachment') ? $offerRequest->get('attachment') : null;
        try {
            DB::beginTransaction();

            //save to database
            $offer = Offer::create($input);
            if ($attachment) {
                foreach ($attachment as $key => $value) {

                    //save the file to server , rename if needed. 
                    // $offerRequest->file('attachment')[$key]->store('users', 'public');
                    $offer->attachment()->create([
                        'attachment' => $value
                    ]);
                }
            }


            DB::commit();

            //send to email

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->internalErrorResponse($th->getMessage());
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
