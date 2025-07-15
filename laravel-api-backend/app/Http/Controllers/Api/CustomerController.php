<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        // Validate the request data
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-\'\.]+$/u',        // Unicode letters, spaces, hyphens, apostrophes, dots
                'not_regex:/^\s/',                   // Cannot start with space
                'not_regex:/\s$/',                   // Cannot end with space
                'not_regex:/\s{2,}/',               // No consecutive spaces
                'not_regex:/[\-\'\.]{2,}/',         // No consecutive special characters
            ],
            'reference' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',           // Uppercase letters, numbers, hyphens only
                'unique:customers,reference',       // Must be unique
            ],
            'customer_category_id' => 'required|exists:customer_categories,id',
            'start_date' => 'required|date',
            'description' => 'nullable|string|max:500'
        ], [
            // Custom error messages
            'name.required' => 'Customer name is required.',
            'name.min' => 'Customer name must be at least 2 characters.',
            'name.max' => 'Customer name cannot exceed 100 characters.',
            'name.regex' => 'Customer name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            'name.not_regex' => 'Customer name format is invalid. Please remove leading/trailing spaces or consecutive spaces.',
            
            'reference.required' => 'Customer reference is required.',
            'reference.min' => 'Customer reference must be at least 3 characters.',
            'reference.max' => 'Customer reference cannot exceed 20 characters.',
            'reference.regex' => 'Customer reference can only contain uppercase letters, numbers, and hyphens.',
            'reference.unique' => 'This customer reference already exists. Please choose a different one.',
            
            'customer_category_id.required' => 'Customer category is required.',
            'customer_category_id.exists' => 'Selected category is invalid.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
        ]);

        // Auto-format the data before saving
        $validated['name'] = $this->formatCustomerName($validated['name']);
        $validated['reference'] = strtoupper(trim($validated['reference']));

        $customer = Customer::create($validated);
        
        return response()->json([
            'message' => 'Customer created successfully',
            'data' => $customer->load(['category', 'contacts'])
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
        // Validate the request data
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\pL\s\-\'\.]+$/u',
                'not_regex:/^\s/',
                'not_regex:/\s$/',
                'not_regex:/\s{2,}/',
                'not_regex:/[\-\'\.]{2,}/',
            ],
            'reference' => [
                'required',
                'string',
                'min:3',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('customers')->ignore($customer->id),
            ],
            'customer_category_id' => 'sometimes|required|exists:customer_categories,id',
            'start_date' => 'required|date',
            'description' => 'nullable|string|max:500'
        ], [
            // Same custom error messages as above
            'name.required' => 'Customer name is required.',
            'name.min' => 'Customer name must be at least 2 characters.',
            'name.max' => 'Customer name cannot exceed 100 characters.',
            'name.regex' => 'Customer name can only contain letters, spaces, hyphens, apostrophes, and periods.',
            'name.not_regex' => 'Customer name format is invalid.',
            
            'reference.required' => 'Customer reference is required.',
            'reference.min' => 'Customer reference must be at least 3 characters.',
            'reference.max' => 'Customer reference cannot exceed 20 characters.',
            'reference.regex' => 'Customer reference can only contain uppercase letters, numbers, and hyphens.',
            'reference.unique' => 'This customer reference already exists. Please choose a different one.',

            'customer_category_id.required' => 'Customer category is required.',
            'customer_category_id.exists' => 'Selected category is invalid.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Start date must be a valid date.',
        ]);

        // Auto-format the data before updating
        $validated['name'] = $this->formatCustomerName($validated['name']);
        $validated['reference'] = strtoupper(trim($validated['reference']));

        $customer->update($validated);
        
        return response()->json([
            'message' => 'Customer updated successfully',
            'data' => $customer->load(['category', 'contacts'])
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

    private function formatCustomerName($name)
    {
        // Remove leading/trailing spaces and ensure no consecutive spaces
        $name = preg_replace('/\s+/', ' ', trim($name));
        
        // Capitalize the first letter of each word
        return ucwords(strtolower($name));
    }
}
