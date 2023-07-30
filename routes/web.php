<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\ResponsibilitiesController;
use App\Http\Controllers\SectorsController;
use App\Http\Controllers\DocumentsController;
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

Route::get('/', function () {
    return redirect()->route('login');
})->name('/');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('users', \App\Http\Controllers\UsersController::class);
    
    Route::resource('companies', \App\Http\Controllers\CompaniesController::class);

    Route::resource('{company_id}/employees', \App\Http\Controllers\EmployeesController::class);

    Route::post('{company_id}/employees/editdoc/{employee_id}', [\App\Http\Controllers\EmployeesController::class, 'editdoc'])->name('editdoc');

    Route::resource('{company_id}/responsibilities', \App\Http\Controllers\ResponsibilitiesController::class);

    Route::resource('{company_id}/sectors', \App\Http\Controllers\SectorsController::class);

    Route::resource('{company_id}/documents', \App\Http\Controllers\DocumentsController::class);

    Route::group([
        'middleware' => 'moders'
    ],function(){
        /*Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::get('/user/create', [UsersController::class, 'create'])->name('users.create');*/
    });
});


