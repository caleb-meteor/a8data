<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(base_path('routes/admin.php'));

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
