<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\User\VerificationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
/*
Route::get('/companyemployees/{id}', [CompanyController::class, 'getEmployeesOfCompany']);
Route::post('/companyemployees', [CompanyController::class, 'addEmployee']);
Route::put('/companyemployee/{id}', [CompanyController::class, 'updateEmployee']);
Route::delete('/companyemployee/{id}', [CompanyController::class, 'deleteEmployee']);
Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error - part 2!');
});
*/
Route::POST('/user/verification',[VerificationController::class,'getVerification']);
