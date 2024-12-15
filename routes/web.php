<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\EmployeeManagementController;
// use App\Http\Controllers\CAMRGatewayController;
// use App\Http\Controllers\CAMRMeterController;
// use App\Http\Controllers\CAMRBuildingController;
// use App\Http\Controllers\CAMRMeterLocationController;
// use App\Http\Controllers\CAMRGatewayDeviceController;

// use App\Http\Controllers\ReportSettingsController;
// use App\Http\Controllers\SAPReportController;
// use App\Http\Controllers\RAWReportController;
// use App\Http\Controllers\SiteReportController;
// use App\Http\Controllers\SiteAsBuiltController;
// use App\Http\Controllers\OfflineReportController;

// use App\Http\Controllers\ConsumptionReportController;
// use App\Http\Controllers\DemandReportController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSiteAccessController;
// use App\Http\Controllers\CAMRSampleExcel;

// use App\Http\Controllers\CompanyController;
// use App\Http\Controllers\DivisionController;

use App\Http\Controllers\EmailController;

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

/*SAMPLE EXCEL*/
#Route::get('/sample1', [CAMRSampleExcel::class,'sample1'])->name('site')->middleware('isLoggedIn');

/*Login Page*/
Route::get('/',[UserAuthController::class,'login'])->middleware('alreadyLoggedIn');
Route::post('login-user', [UserAuthController::class,'loginUser'])->name('login-user');

/*Reset Password - Unable to Login*/
Route::get('/passwordreset',[UserAuthController::class,'passwordreset'])->name('passwordreset');
Route::post('/reset-password', [EmailController::class, 'sendTemporaryPasswordtoEmail'])->name('sendTemporaryPasswordtoEmail');

/*Logout*/
Route::get('logout', [UserAuthController::class,'logout'])->name('logout-user');

/*Load Site*/
Route::get('/employee', [EmployeeManagementController::class,'employee'])->name('employee')->middleware('isLoggedIn');
Route::get('employee/list', [EmployeeManagementController::class, 'getEmployeeList'])->name('getEmployeeList')->middleware('isLoggedIn');
//Route::get('site/user/list', [EmployeeManagementController::class, 'getSiteForUser'])->name('UserSiteList')->middleware('isLoggedIn');

/*Create/Update Employee*/
Route::post('/submit_employee_information', [EmployeeManagementController::class,'submit_employee_information'])->name('submit_employee_information')->middleware('isLoggedIn');

/*GET Site Info*/
Route::post('/employee_info', [EmployeeManagementController::class, 'employee_info'])->name('EmployeeInformation')->middleware('isLoggedIn');

/*Confirm Delete Site*/
Route::post('/delete_employee_confirmed', [EmployeeManagementController::class, 'delete_employee_confirmed'])->name('DeleteEmployee')->middleware('isLoggedIn');



/*Load User Account List for Admin Only*/
Route::get('/user', [UserController::class,'user'])->name('user')->middleware('isLoggedIn');
/*Get List of User*/
Route::post('user_list', [UserController::class, 'getUserList'])->name('UserList')->middleware('isLoggedIn');
/*Create User*/
Route::post('/create_user_post', [UserController::class,'create_user_post'])->name('create_user_post')->middleware('isLoggedIn');
/*View Site Access*/
Route::get('user_site_access', [UserSiteAccessController::class, 'getUserSiteAccess'])->name('getUserSiteAccess')->middleware('isLoggedIn');
/*Add Site Access*/
Route::post('/add_user_access_post', [UserSiteAccessController::class,'add_user_access_post'])->name('add_user_access_post')->middleware('isLoggedIn');


/*GET User Info*/
Route::post('/user_info', [UserController::class, 'user_info'])->name('user_info')->middleware('isLoggedIn');
/*Update User*/
Route::post('/update_user_post', [UserController::class,'update_user_post'])->name('update_user_post')->middleware('isLoggedIn');
/*Confirm Delete Switch*/
Route::post('/delete_user_confirmed', [UserController::class, 'delete_user_confirmed'])->name('delete_user_confirmed')->middleware('isLoggedIn');
/*Update User Account*/
Route::post('/user_account_post', [UserController::class,'user_account_post'])->name('user_account_post')->middleware('isLoggedIn');
