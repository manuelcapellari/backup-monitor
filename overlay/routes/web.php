<?php

use App\Http\Controllers\ComputerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\MailAccountController;
use App\Http\Controllers\ParserRuleController;
use App\Http\Controllers\RawEmailController;
use App\Http\Controllers\TenantController;
use App\Http\Middleware\InternalAccessMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware([InternalAccessMiddleware::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/computers/{computer}', [ComputerController::class, 'show'])->name('computers.show');
    Route::get('/debug', [DebugController::class, 'index'])->name('debug.index');

    Route::resource('tenants', TenantController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('mail-accounts', MailAccountController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('raw-emails', RawEmailController::class)->only(['index', 'show']);

    Route::get('parser-rules/test/{parserRule?}', [ParserRuleController::class, 'testForm'])->name('parser-rules.test-form');
    Route::post('parser-rules/test', [ParserRuleController::class, 'testRun'])->name('parser-rules.test-run');
    Route::post('parser-rules/{parserRule}/duplicate', [ParserRuleController::class, 'duplicate'])->name('parser-rules.duplicate');
    Route::post('parser-rules/{parserRule}/toggle', [ParserRuleController::class, 'toggle'])->name('parser-rules.toggle');
    Route::post('parser-rules/{parserRule}/move-up', [ParserRuleController::class, 'moveUp'])->name('parser-rules.move-up');
    Route::post('parser-rules/{parserRule}/move-down', [ParserRuleController::class, 'moveDown'])->name('parser-rules.move-down');
    Route::resource('parser-rules', ParserRuleController::class)->except(['show']);
});

use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
Route::get('/computers/{computer}', [ComputerController::class, 'show'])->name('computers.show');
Route::get('/debug', [DebugController::class, 'index'])->name('debug.index');

Route::resource('tenants', TenantController::class)->only(['index', 'create', 'store', 'edit', 'update']);
Route::resource('mail-accounts', MailAccountController::class)->only(['index', 'create', 'store', 'edit', 'update']);
Route::resource('raw-emails', RawEmailController::class)->only(['index', 'show']);

Route::get('parser-rules/test/{parserRule?}', [ParserRuleController::class, 'testForm'])->name('parser-rules.test-form');
Route::post('parser-rules/test', [ParserRuleController::class, 'testRun'])->name('parser-rules.test-run');
Route::post('parser-rules/{parserRule}/duplicate', [ParserRuleController::class, 'duplicate'])->name('parser-rules.duplicate');
Route::post('parser-rules/{parserRule}/toggle', [ParserRuleController::class, 'toggle'])->name('parser-rules.toggle');
Route::post('parser-rules/{parserRule}/move-up', [ParserRuleController::class, 'moveUp'])->name('parser-rules.move-up');
Route::post('parser-rules/{parserRule}/move-down', [ParserRuleController::class, 'moveDown'])->name('parser-rules.move-down');
Route::resource('parser-rules', ParserRuleController::class)->except(['show']);
