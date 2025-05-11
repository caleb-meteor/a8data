<?php


use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\UsageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\PermissionCheck;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', PermissionCheck::class])->group(function () {
    Route::get('menus', [AuthController::class, 'menus']);
    Route::get('user', [AuthController::class, 'user'])->name('auth.users');
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('teams', TeamController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('agents', AgentController::class)->only(['index', 'store', 'update', 'destroy']);
    // 消耗
    Route::resource('usages', UsageController::class)->only(['index', 'store', 'update', 'destroy']);
    // 导入
    Route::post('usages/import', [UsageController::class, 'import']);
    // 财务
    Route::resource('finances', FinanceController::class)->only(['index', 'store', 'update', 'destroy']);
});
