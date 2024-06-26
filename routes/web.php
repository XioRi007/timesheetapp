<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group((function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard')->middleware('can:view dashboard');
    Route::resource('clients', ClientController::class)->except('show');
    Route::resource('projects', ProjectController::class)->except('show');
    Route::resource('developers', DeveloperController::class)->except('show');
    Route::get('/developers/{developer}/worklogs', [DeveloperController::class, 'worklogs'])
        ->middleware('can:worklogs,developer')
        ->name('developers.worklogs');
    Route::resource('worklogs', WorkLogController::class)->except('show');    
}));


require __DIR__ . '/auth.php';
