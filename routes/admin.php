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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/bind2fa', [AuthController::class, 'bind2fa']);

    Route::get('menus', [AuthController::class, 'menus']);
    Route::get('user', [AuthController::class, 'user'])->name('auth.users');

    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::put('users/{id}/unbind2fa', [UserController::class, 'unbind2fa']);

    Route::resource('teams', TeamController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('teams/export', [TeamController::class, 'export']);

    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('agents', AgentController::class)->only(['index', 'store', 'update', 'destroy']);
    // 消耗
    Route::get('usages/daily', [UsageController::class, 'getDailyUsage'])->name('usages.daily');
    Route::get('usages/export', [UsageController::class, 'export']);
    Route::resource('usages', UsageController::class)->only(['index', 'store', 'update', 'destroy']);
    // 导入
    Route::post('usages/import', [UsageController::class, 'import'])->name('usages.import');
    // 财务
    Route::resource('finances', FinanceController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('finances/import', [FinanceController::class, 'import'])->name('finances.import');
    Route::get('finances/export', [FinanceController::class, 'export']);
});
