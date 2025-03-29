<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\EmployeeManagementController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSiteAccessController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\EmployeeLogsController;
use App\Http\Controllers\DeductionTypeController;
use App\Http\Controllers\EmployeeAllowanceLogsController;
use App\Http\Controllers\EmployeeDeductionLogsController;
use App\Http\Controllers\EmployeeLeaveLogsController;
use App\Http\Controllers\DriversLogsController;

use App\Http\Controllers\PayslipController;

// use App\Http\Controllers\CAMRSampleExcel;

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

/*Start Employee Routes*/
Route::get('/employee', [EmployeeManagementController::class,'employee'])->name('employee')->middleware('isLoggedIn');
Route::get('employee/list', [EmployeeManagementController::class, 'getEmployeeList'])->name('getEmployeeList')->middleware('isLoggedIn');
/*Create/Update Employee*/
Route::post('/submit_employee_information', [EmployeeManagementController::class,'submit_employee_information'])->name('submit_employee_information')->middleware('isLoggedIn');
/*GET Employee Info*/
Route::post('/employee_info', [EmployeeManagementController::class, 'employee_info'])->name('EmployeeInformation')->middleware('isLoggedIn');
/*Confirm Delete Employee*/
Route::post('/delete_employee_confirmed', [EmployeeManagementController::class, 'delete_employee_confirmed'])->name('DeleteEmployee')->middleware('isLoggedIn');
/*End Employee Routes*/

/*Load Branch List*/
Route::get('/branch', [BranchController::class,'branch'])->name('branch')->middleware('isLoggedIn');
Route::get('branch/list', [BranchController::class, 'getBranchList'])->name('getBranchList')->middleware('isLoggedIn');
/*Create or Update Branch*/
Route::post('/submit_branch_post', [BranchController::class,'submit_branch_post'])->name('SubmitBranch')->middleware('isLoggedIn');
/*GET Branch Info*/
Route::post('/branch_info', [BranchController::class, 'branch_info'])->name('BranchInfo')->middleware('isLoggedIn');
/*Confirm Delete Branch*/
Route::post('/delete_branch_confirmed', [BranchController::class, 'delete_branch_confirmed'])->name('DeleteBranch')->middleware('isLoggedIn');

/*Load Department List*/
Route::post('department/list', [DepartmentController::class, 'getDepartmentList'])->name('getDepartmentList')->middleware('isLoggedIn');
/*Create or Update Department*/
Route::post('/submit_department_post', [DepartmentController::class,'submit_department_post'])->name('SubmitDepartment')->middleware('isLoggedIn');
/*GET Department Info*/
Route::post('/department_info', [DepartmentController::class, 'department_info'])->name('DepartmentInfo')->middleware('isLoggedIn');
/*Confirm Delete Department*/
Route::post('/delete_department_confirmed', [DepartmentController::class, 'delete_department_confirmed'])->name('DeleteDepartment')->middleware('isLoggedIn');

/*Load holiday List*/
Route::get('/holiday', [HolidayController::class,'holiday'])->name('holiday')->middleware('isLoggedIn');
Route::get('holiday/list', [HolidayController::class, 'getholidayList'])->name('getholidayList')->middleware('isLoggedIn');
/*Create or Update holiday*/
Route::post('/submit_holiday_post', [HolidayController::class,'submit_holiday_post'])->name('SubmitHoliday')->middleware('isLoggedIn');
/*GET holiday Info*/
Route::post('/holiday_info', [HolidayController::class, 'holiday_info'])->name('HolidayInfo')->middleware('isLoggedIn');
/*Confirm Delete holiday*/
Route::post('/delete_holiday_confirmed', [HolidayController::class, 'delete_holiday_confirmed'])->name('DeleteHoliday')->middleware('isLoggedIn');

/*01/03/2025*/
Route::get('/employee-attendance-logs', [EmployeeLogsController::class,'employee_attendance_logs'])->name('employee_attendance_logs')->middleware('isLoggedIn');
Route::post('/branch-item-select', [BranchController::class,'getBranchList_for_item_selection'])->name('getBranchList_for_selection')->middleware('isLoggedIn');
Route::post('/department-item-select', [DepartmentController::class,'getDepartmentList_for_item_selection'])->name('getDepartmentList_for_selection')->middleware('isLoggedIn');
Route::post('/employee-item-select', [EmployeeManagementController::class,'getEmployeeList_for_item_selection'])->name('getEmployeeList_for_selection')->middleware('isLoggedIn');
/*Regular Logs - January 18, 2024*/
Route::get('employee-regular-logs/list', [EmployeeLogsController::class, 'getEmployeeRegularLogsList'])->name('getEmployeeRegularLogsList')->middleware('isLoggedIn');
Route::post('/submit_employee_regular_logs_information', [EmployeeLogsController::class,'submit_employee_regular_logs_information'])->name('submit_employee_regular_logs_information')->middleware('isLoggedIn');
/*Regular OT Logs - Feb 5, 2024*/
Route::get('employee-regular-ot-logs/list', [EmployeeLogsController::class, 'getEmployeeRegularOTLogsList'])->name('getEmployeeRegularOTLogsList')->middleware('isLoggedIn');
/*Restday OT Logs - Feb 5, 2024*/
Route::get('employee-restday-ot-logs/list', [EmployeeLogsController::class, 'getEmployeeRestDayOTLogsList'])->name('getEmployeeRestDayOTLogsList')->middleware('isLoggedIn');
/*GET Employee Log Info*/
Route::post('/employee_log_info', [EmployeeLogsController::class, 'employee_log_info'])->name('EmployeeLogsInformation')->middleware('isLoggedIn');
/*Confirm Delete Employee Log*/
Route::post('/delete_employee_log_confirmed', [EmployeeLogsController::class, 'delete_employee_log_confirmed'])->name('DeleteEmployeeLog')->middleware('isLoggedIn');

/*Leave - March 25, 2025*/
Route::get('employee-leave-logs/list', [EmployeeLeaveLogsController::class, 'getEmployeeLeaveLogsList'])->name('getEmployeeLeaveLogsList')->middleware('isLoggedIn');
Route::post('/submit_employee_leave_logs_information', [EmployeeLeaveLogsController::class,'submit_employee_leave_logs_information'])->name('submit_employee_leave_logs_information')->middleware('isLoggedIn');
Route::post('/employee_leave_logs_info', [EmployeeLeaveLogsController::class, 'employee_leave_logs_info'])->name('EmployeeLeaveLogsInformation')->middleware('isLoggedIn');
Route::post('/delete_employee_leave_log_confirmed', [EmployeeLeaveLogsController::class, 'delete_employee_leave_log_confirmed'])->name('DeleteEmployeeLeaveLog')->middleware('isLoggedIn');

/*Drivers Logs*/
Route::get('drivers-logs/list', [DriversLogsController::class, 'getDriversLogsList'])->name('getDriversLogsList')->middleware('isLoggedIn');
Route::post('/submit_drivers_logs_information', [DriversLogsController::class,'submit_drivers_logs_information'])->name('submit_drivers_logs_information')->middleware('isLoggedIn');


/*Deduction Type*/
Route::get('/deduction_type', [DeductionTypeController::class,'DeductionType'])->name('deduction_type')->middleware('isLoggedIn');
Route::get('deduction_type/list', [DeductionTypeController::class, 'getDeductionTypeList'])->name('getDeductionTypeList')->middleware('isLoggedIn');
/*Create or Update holiday*/
Route::post('/submit_deduction_type_post', [DeductionTypeController::class,'submit_deduction_type_post'])->name('SubmitDeductionType')->middleware('isLoggedIn');
/*GET holiday Info*/
Route::post('/deduction_type_info', [DeductionTypeController::class, 'deduction_type_info'])->name('DeductionTypeInfo')->middleware('isLoggedIn');
/*Confirm Delete holiday*/
Route::post('/delete_deduction_type_confirmed', [DeductionTypeController::class, 'delete_deduction_type_confirmed'])->name('DeleteDeductionType')->middleware('isLoggedIn');

/*Deduction Logs*/
Route::get('/employee-deduction-logs', [EmployeeDeductionLogsController::class,'employee_deduction_logs'])->name('employee_deduction_logs')->middleware('isLoggedIn');
Route::post('/deduction-item-select', [DeductionTypeController::class,'getDeductionTypeList_for_selection'])->name('getDeductionTypeList_for_selection')->middleware('isLoggedIn');
Route::get('employee-deduction-logs/list', [EmployeeDeductionLogsController::class, 'getEmployeeDeductionLogsList'])->name('getEmployeeDeductionLogsList')->middleware('isLoggedIn');
Route::post('/employee_deduction_info', [EmployeeDeductionLogsController::class, 'employee_deduction_info'])->name('EmployeeDeductionLogsInformation')->middleware('isLoggedIn');
Route::post('/delete_employee_deduction_log_confirmed', [EmployeeDeductionLogsController::class, 'delete_employee_deduction_log_confirmed'])->name('DeleteEmployeeDeductionLog')->middleware('isLoggedIn');
Route::post('/submit_employee_deduction_logs_information', [EmployeeDeductionLogsController::class,'submit_employee_deduction_logs_information'])->name('submit_employee_deduction_logs_information')->middleware('isLoggedIn');

/*Allowance Logs*/
Route::get('/employee-allowance-logs', [EmployeeAllowanceLogsController::class,'employee_allowance_logs'])->name('employee_allowance_logs')->middleware('isLoggedIn');
Route::post('/allowance-item-select', [AllowanceTypeController::class,'getAllowanceTypeList_for_selection'])->name('getAllowanceTypeList_for_selection')->middleware('isLoggedIn');
Route::get('employee-allowance-logs/list', [EmployeeAllowanceLogsController::class, 'getEmployeeAllowanceLogsList'])->name('getEmployeeAllowanceLogsList')->middleware('isLoggedIn');
Route::post('/employee_allowance_info', [EmployeeAllowanceLogsController::class, 'employee_allowance_info'])->name('EmployeeAllowanceLogsInformation')->middleware('isLoggedIn');
Route::post('/delete_employee_allowance_log_confirmed', [EmployeeAllowanceLogsController::class, 'delete_employee_allowance_log_confirmed'])->name('DeleteEmployeeAllowanceLog')->middleware('isLoggedIn');
Route::post('/submit_employee_allowance_logs_information', [EmployeeAllowanceLogsController::class,'submit_employee_allowance_logs_information'])->name('submit_employee_allowance_logs_information')->middleware('isLoggedIn');


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


/*Payslip*/
Route::get('/payslip', [PayslipController::class,'employees_payslip'])->name('payslip')->middleware('isLoggedIn');
