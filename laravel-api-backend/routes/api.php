<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\CustomerCategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('api')->group(function () {
    // Define your API routes here
    Route::get('/example', function () {
        return response()->json(['message' => 'This is an example route']);
    });

    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('customer-categories', CustomerCategoryController::class);
}); 

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello, World!']);
});
