<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\DepartmentController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\PaycheckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/api/v1/')->name('api.v1.')->group(function () {
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('employees.paychecks', PaycheckController::class)->only(['index', 'show']);
    Route::post('paychecks', [PaycheckController::class, 'store'])->name('paychecks.store');
});
