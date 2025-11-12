<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactTag;
use App\Models\Tag;
use App\Models\User;
use App\Services\Remappers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $contacts = Contact::latest()->get();

        $remapper = new Remappers();
        $remapContacts = $remapper->remapContacts($contacts);

        return response()->json($remapContacts);
        // return response()->json([
        //     'message' => 'Contact created successfully.',
        //     'data' => $contact
        // ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            // 'user_id' => 'required|exists:users,id',
            'company_id' => 'required',
            'source_id' => 'required|exists:sources,id',
            'owner_user_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'title_name' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date|max:255',
            'phone_number_1' => 'required|numeric',
            'phone_number_2' => 'nullable|numeric',
            'address' => 'nullable|string|max:255',
            'province_id' => 'nullable|required_with:address|exists:provinces,id',
            'city_id' => 'nullable|required_with:address|exists:cities,id',
            'tags' => 'nullable|array',
            // 'tags.*' => 'exists:tags,id',
        ]);

        $user = User::create([
            'role_id' => 7,
            // 'username' => strtolower(str_replace(' ', '_', $request->name)) . rand(1000, 9999),
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'is_active' => 0,
        ]);

        $contact = Contact::create([
            'user_id' => $user->id,
            'company_id' => $request->company_id,
            'source_id' => $request->source_id,
            'owner_user_id' => $request->owner_user_id,
            'name' => $request->name,
            'title_name' => $request->title_name,
            'date_of_birth' => $request->date_of_birth,
            'phone_number_1' => $request->phone_number_1,
            'phone_number_2' => $request->phone_number_2,
            'address' => $request->address,
            'province_id' => $request->province_id,
            'city_id' => $request->city_id,
        ]);

        foreach ($request->tags as $cTag) {
            $slug = Str::slug($cTag);

            if($tagExist = Tag::where('slug', $slug)->first()) {
                $tag = $tagExist;
            }else{
                $tag = Tag::create([
                    'name' => $cTag,
                    'slug' => $slug,
                ]);
            }
            
            ContactTag::create([
                'contact_id' => $contact->id,
                'tag_id' => $tag->id,
            ]);

        }

        return response()->json([
            'message' => 'Contact created successfully.',
            'data' => $contact
        ], 201);
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
    public function update(Request $request, Contact $contact): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Contact updated successfully.',
            'data' => $contact
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact): JsonResponse
    {
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully.'
        ]);
    }
}
