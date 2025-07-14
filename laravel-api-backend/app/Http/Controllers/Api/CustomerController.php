<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $query = Customer::with(['category', 'contacts']);

        // Plain text search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('contacts', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        // Cetegory filter functionality
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('customer_category_id', $request->category_id);
        }

        return response()->json([
            'message' => 'Customers retrieved successfully',
            'data' => $query->get(),
        ]);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|unique:customers',
            'customer_category_id' => 'required|exists:customer_categories,id',
            'start_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $customer = Customer::create($validatedData);

        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return response()->json([
            'message' => 'Customer retrieved successfully',
            'data' => $customer->load(['category', 'contacts']),
        ]);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'reference' => 'sometimes|required|string|unique:customers,reference,' . $customer->id,
            'customer_category_id' => 'sometimes|required|exists:customer_categories,id',
            'start_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $customer->update($validatedData);

        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer,
        ]);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            
            return response()->json([
                'message' => 'Customer deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error deleting customer: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Error deleting customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
