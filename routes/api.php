<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\ContactController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts/store', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/show/{contact}', [ContactController::class, 'show'])->name('contacts.show');
    Route::put('/contacts/update/{contact}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/delete/{contact}', [ContactController::class, 'destroy'])->name('contacts.destroy');
});