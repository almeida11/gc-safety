<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\EmployeesController;
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

    Route::resource('employees', \App\Http\Controllers\EmployeesController::class);

    Route::group([
        'middleware' => 'moders'
    ],function(){
        /*Route::get('/users', [UsersController::class, 'index'])->name('users');
        Route::get('/user/create', [UsersController::class, 'create'])->name('users.create');*/
    });
});


