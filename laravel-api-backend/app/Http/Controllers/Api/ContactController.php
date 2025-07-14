<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Contact::with('customer');

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        return response()->json([
            'message' => 'Contacts retrieved successfully',
            'data' => $query->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'customer_id' => 'required|exists:customers,id',
        ]);

        $contact = Contact::create($validatedData);

        return response()->json([
            'message' => 'Contact created successfully',
            'data' => $contact,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return response()->json([
            'message' => 'Contact retrieved successfully',
            'data' => $contact->load('customer'),
        ]);
    }    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        $validatedData = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'customer_id' => 'sometimes|required|exists:customers,id',
        ]);

        $contact->update($validatedData);

        return response()->json([
            'message' => 'Contact updated successfully',
            'data' => $contact,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return response()->json([
            'message' => 'Contact deleted successfully',
        ]);
    }
    
}
