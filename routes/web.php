<?php

use App\Http\Controllers\OutputtingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\RecordingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
Route::get('/queries', [QueryController::class, 'index'])->name('query.index');
Route::get('/queries/create', [QueryController::class, 'create'])->name('query.create');
Route::get('/queries/{id}/edit', [QueryController::class, 'edit'])->name('query.edit');
Route::get('/queries/{id}', [QueryController::class, 'show'])->name('query.show');

Route::post('/queries', [QueryController::class, 'store'])->name('query.store');
Route::put('/queries/{id}', [QueryController::class, 'update'])->name('query.update');
Route::delete('/queries/{id}', [QueryController::class, 'destroy'])->name('query.destroy');
Route::post('/query/verify', [QueryController::class, 'verify'])->name('query.verify');

Route::post('/recordings/execute/{query}', [RecordingController::class, 'recordQueryExecution'])->name('recording-execution');

Route::post('/outputting/store/{query}', [OutputtingController::class, 'store'])->name('outputting.store');
Route::put('/outputting/update/{outputting}', [OutputtingController::class, 'update'])->name('outputting.update');
Route::delete('/outputting/destroy/{outputting}', [OutputtingController::class, 'destroy'])->name('outputting.destroy');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');
//     Route::get('/queries', [QueryController::class, 'index'])->name('query.index');
//     Route::get('/queries/create', [QueryController::class, 'create'])->name('query.create');
//     Route::get('/queries/{id}/edit', [QueryController::class, 'edit'])->name('query.edit');
//     Route::get('/queries/{id}', [QueryController::class, 'show'])->name('query.show');

//     Route::post('/queries', [QueryController::class, 'store'])->name('query.store');
//     Route::put('/queries/{id}', [QueryController::class, 'update'])->name('query.update');
//     Route::delete('/queries/{id}', [QueryController::class, 'destroy'])->name('query.destroy');
//     Route::post('/query/verify', [QueryController::class, 'verify'])->name('query.verify');

//     Route::post('/recordings/execute/{query}', [RecordingController::class, 'recordQueryExecution'])->name('recording-execution');

//     Route::post('/outputting/store/{query}', [OutputtingController::class, 'store'])->name('outputting.store');
//     Route::put('/outputting/update/{outputting}', [OutputtingController::class, 'update'])->name('outputting.update');
//     Route::delete('/outputting/destroy/{outputting}', [OutputtingController::class, 'destroy'])->name('outputting.destroy');
// });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
