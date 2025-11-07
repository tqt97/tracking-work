<?php

use App\Http\Controllers\RoleController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserRoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('roles')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [RoleController::class, 'index']);
});

Route::prefix('users')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [UserRoleController::class, 'index']);
    Route::post('/{user}/assign-role', [UserRoleController::class, 'assignRole']);
});

Route::prefix('roles')->middleware(['auth:sanctum', 'permission:manage-flows'])->group(function () {
    Route::get('/', [RolePermissionController::class, 'index']);
    Route::post('/{role}/sync-permissions', [RolePermissionController::class, 'syncPermissions']);
});
