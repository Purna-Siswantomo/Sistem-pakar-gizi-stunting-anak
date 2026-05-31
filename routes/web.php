<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Screening\ChildController;
use App\Http\Controllers\Screening\KnowledgeBaseController;
use App\Http\Controllers\Screening\ScreeningController;
use App\Http\Controllers\Screening\StaticPageController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/', [StaticPageController::class, 'dashboard'])->name('dashboard');
    Route::get('/children', [ChildController::class, 'index'])->name('children.index');
    Route::get('/children/create', [ChildController::class, 'create'])->name('children.create');
    Route::post('/children', [ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}', [ChildController::class, 'show'])->name('children.show');
    Route::get('/children/{child}/screenings/create', [ScreeningController::class, 'create'])->name('children.screenings.create');
    Route::post('/children/{child}/screenings', [ScreeningController::class, 'store'])->name('children.screenings.store');
    Route::redirect('/screenings/create', '/children')->name('screenings.create');
    Route::get('/screenings', [ScreeningController::class, 'index'])->name('screenings.index');
    Route::get('/screenings/{screening}', [ScreeningController::class, 'show'])->name('screenings.show');
    Route::get('/screenings/{screening}/pdf', [ScreeningController::class, 'exportPdf'])->name('screenings.pdf');
    Route::get('/tentang-sistem', [StaticPageController::class, 'about'])->name('about');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/knowledge-base/rules', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.rules');
    Route::get('/knowledge-base/rules/create', [KnowledgeBaseController::class, 'create'])->name('knowledge-base.rules.create');
    Route::post('/knowledge-base/rules', [KnowledgeBaseController::class, 'store'])->name('knowledge-base.rules.store');
    Route::get('/knowledge-base/rules/{rule}', [KnowledgeBaseController::class, 'show'])->name('knowledge-base.rules.show');
    Route::get('/knowledge-base/rules/{rule}/edit', [KnowledgeBaseController::class, 'edit'])->name('knowledge-base.rules.edit');
    Route::put('/knowledge-base/rules/{rule}', [KnowledgeBaseController::class, 'update'])->name('knowledge-base.rules.update');
    Route::patch('/knowledge-base/rules/{rule}/deactivate', [KnowledgeBaseController::class, 'deactivate'])->name('knowledge-base.rules.deactivate');
});

require __DIR__.'/auth.php';
