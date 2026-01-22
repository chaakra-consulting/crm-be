<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CityController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ProvinceController;
use App\Http\Controllers\API\SourceController;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FileController;
use App\Http\Controllers\LeadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/auth/me', function () {
    $user = auth()->user();
    if (!$user || $user->is_active != 1) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }
    return $user;
})->middleware('auth:sanctum');


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/users/show/{id?}', [UserController::class, 'show'])->name('users.show');
Route::put('/users/activate-account/{id}', [UserController::class, 'activateAccount'])->name('users.activate_account');

Route::get('project/list', [ProjectController::class, 'projectList'])->name('projects.list');

Route::middleware(['auth:sanctum'])->group(function () {

    //cities
    Route::get('/cities', [CityController::class, 'index'])->name('contacts.index');

    //contacts
    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
    Route::post('/contacts/store', [ContactController::class, 'store'])->name('contacts.store');
    Route::get('/contacts/show/{id}', [ContactController::class, 'show'])->name('contacts.show');
    Route::put('/contacts/update/{id}', [ContactController::class, 'update'])->name('contacts.update');
    Route::delete('/contacts/delete/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy');
    Route::post('/contacts/activation-email/{userId}', [ContactController::class, 'sendAccActivationEmail']);

    //companies
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::post('/companies/store', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/show/{id}', [CompanyController::class, 'show'])->name('companies.show');
    Route::put('/companies/update/{id}', [CompanyController::class, 'update'])->name('companies.update');
    Route::delete('/companies/delete/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');

    //file
    Route::get('/stream/{path}', [FileController::class, 'stream'])->where('path', '.*');
    Route::get('/download/{path}', [FileController::class, 'download'])->where('path', '.*');

    //provinces
    Route::get('/provinces', [ProvinceController::class, 'index'])->name('contacts.index');

    //projects
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/detail/{id}', [ProjectController::class, 'detail'])->name('projects.detail');
    Route::post('/projects/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/show/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::put('/projects/update/{id}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/delete/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    //sources
    Route::get('/sources', [SourceController::class, 'index'])->name('sources.index');
    Route::post('/sources/store', [SourceController::class, 'store'])->name('sources.store');
    Route::put('/sources/update/{id}', [SourceController::class, 'update'])->name('sources.update');
    Route::delete('/sources/delete/{id}', [SourceController::class, 'destroy'])->name('sources.destroy');

    //tags
    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags/store', [TagController::class, 'store'])->name('tags.store');
    Route::put('/tags/update/{id}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/delete/{id}', [TagController::class, 'destroy'])->name('tags.destroy');

    //projects
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/detail/{id}', [TicketController::class, 'detail'])->name('tickets.detail');
    Route::post('/tickets/store', [TicketController::class, 'store'])->name('tickets.store');
    Route::post('/tickets/store-message/{id}', [TicketController::class, 'storeMessage'])->name('tickets.store_message');
    Route::put('/tickets/update-approval/{id}', [TicketController::class, 'updateApproval'])->name('tickets.update_approval');
    Route::put('/tickets/update-approval-done/{id}', [TicketController::class, 'updateApprovalDone'])->name('tickets.update_approval_done');
    Route::put('/tickets/update-status/{id}', [TicketController::class, 'updateStatus'])->name('tickets.update_status');

    // Route::get('/tickets/show/{id}', [TicketController::class, 'show'])->name('tickets.show');
    // Route::put('/tickets/update/{id}', [TicketController::class, 'update'])->name('tickets.update');
    // Route::delete('/tickets/delete/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    //users
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/roles', [UserController::class, 'indexRoles'])->name('users.roles');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::put('/users/change-password/{id}', [UserController::class, 'changePassword'])->name('users.change_password');
    Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/index-sdm', [UserController::class, 'indexUserSDM'])->name('users.index_sdm');
    Route::get('/users/index-user-project', [UserController::class, 'indexUserFromUserSDMByBukukasProject'])->name('users.index_from_sdm_by_bukukas_project');
});

//Leads
Route::resource('leads', LeadController::class);
Route::get('ticket/project/{project_id}', [ProjectController::class, 'get_tickets']);
