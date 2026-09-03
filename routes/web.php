<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return redirect()->route('home');
});


Auth::routes([
    'register' => false,
    'reset'    => true,  
    'verify'   => false,
]);

// Protected Admin Panel Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Companies Resource Routes (index, create, store, show, edit, update, destroy)
    Route::resource('companies', CompanyController::class);

    // Employees Resource Routes (index, create, store, show, edit, update, destroy)
    Route::resource('employees', EmployeeController::class);
});
