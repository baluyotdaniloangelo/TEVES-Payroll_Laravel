<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CAMRUserAuthController;
use App\Http\Controllers\CAMRSiteController;
use App\Http\Controllers\CAMRGatewayController;
use App\Http\Controllers\CAMRMeterController;
use App\Http\Controllers\CAMRBuildingController;
use App\Http\Controllers\CAMRMeterLocationController;
use App\Http\Controllers\CAMRGatewayDeviceController;

use App\Http\Controllers\ReportSettingsController;
use App\Http\Controllers\SAPReportController;
use App\Http\Controllers\RAWReportController;
use App\Http\Controllers\SiteReportController;
use App\Http\Controllers\SiteAsBuiltController;
use App\Http\Controllers\OfflineReportController;

use App\Http\Controllers\ConsumptionReportController;
use App\Http\Controllers\DemandReportController;

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSiteAccessController;
use App\Http\Controllers\CAMRSampleExcel;

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DivisionController;

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
Route::get('/',[CAMRUserAuthController::class,'login'])->middleware('alreadyLoggedIn');
Route::post('login-user', [CAMRUserAuthController::class,'loginUser'])->name('login-user');

/*Reset Password - Unable to Login*/
Route::get('/passwordreset',[CAMRUserAuthController::class,'passwordreset'])->name('passwordreset');
Route::post('/reset-password', [EmailController::class, 'sendTemporaryPasswordtoEmail'])->name('sendTemporaryPasswordtoEmail');

/*Logout*/
Route::get('logout', [CAMRUserAuthController::class,'logout']);

/*Load Site*/
Route::get('/site', [CAMRSiteController::class,'site'])->name('site')->middleware('isLoggedIn');
Route::get('site/list', [CAMRSiteController::class, 'getSiteForAdmin'])->name('AdminSiteList')->middleware('isLoggedIn');
Route::get('site/user/list', [CAMRSiteController::class, 'getSiteForUser'])->name('UserSiteList')->middleware('isLoggedIn');

/*Create Site*/
Route::post('/create_site_post', [CAMRSiteController::class,'create_site_post'])->name('create_site_post')->middleware('isLoggedIn');

/*Update Site*/
Route::post('/update_site_post', [CAMRSiteController::class,'update_site_post'])->name('update_site_post')->middleware('isLoggedIn');

/*GET Site Info*/
Route::post('/site_info', [CAMRSiteController::class, 'site_info'])->name('site_info')->middleware('isLoggedIn');

/*Confirm Delete Site*/
Route::post('/delete_site_confirmed', [CAMRSiteController::class, 'delete_site_confirmed'])->name('delete_site_confirmed')->middleware('isLoggedIn');

/*Site Dashboard*/
Route::get('/site_details/{siteID}', [CAMRSiteController::class,'site_details_2'])->name('site_details')->middleware('isLoggedIn');
Route::get('/site_details_2/{siteID}', [CAMRSiteController::class,'site_details_2'])->name('site_details_2')->middleware('isLoggedIn');

/*Save Site Current Tab*/
Route::post('/save_site_tab', [CAMRSiteController::class, 'save_site_tab'])->name('save_site_tab')->middleware('isLoggedIn');

/*Load Gateway List Persite*/
Route::get('getGateway/', [CAMRGatewayController::class,'getGateway'])->name('getGateway')->middleware('isLoggedIn');
/*Load Offline Gateway List Persite*/
Route::get('getOfflineGateway/', [CAMRGatewayController::class,'getOfflineGateway'])->name('getOfflineGateway')->middleware('isLoggedIn');


/*Load Gateway List BLD and EE ROOM*/
Route::get('getGatewayPerBLGandEEROOM/', [CAMRGatewayController::class,'getGatewayPerBLGandEEROOM'])->name('getGatewayPerBLGandEEROOM')->middleware('isLoggedIn');
Route::get('ReloadGatewayOption/', [CAMRGatewayController::class,'ReloadGatewayOption'])->name('ReloadGatewayOption')->middleware('isLoggedIn');

/*Create Gateway*/
Route::post('/create_gateway_post', [CAMRGatewayController::class,'create_gateway_post'])->name('create_gateway_post')->middleware('isLoggedIn');

/*Update Gateway*/
Route::post('/update_gateway_post', [CAMRGatewayController::class,'update_gateway_post'])->name('update_gateway_post')->middleware('isLoggedIn');

/*GET Gateway Info*/
Route::post('/gateway_info', [CAMRGatewayController::class, 'gateway_info'])->name('gateway_info')->middleware('isLoggedIn');

/*Confirm Delete Gateway*/
Route::post('/delete_gateway_confirmed', [CAMRGatewayController::class, 'delete_gateway_confirmed'])->name('delete_gateway_confirmed')->middleware('isLoggedIn');

Route::post('/import_meters', [CAMRGatewayController::class, 'import_meters'])->name('import_meters')->middleware('isLoggedIn');

/*Enable CSV Update*/
Route::post('enablecsvUpdate/', [CAMRGatewayController::class,'enablecsvUpdate'])->name('enablecsvUpdate')->middleware('isLoggedIn');

/*Disable CSV Update*/
Route::post('disablecsvUpdate/', [CAMRGatewayController::class,'disablecsvUpdate'])->name('disablecsvUpdate')->middleware('isLoggedIn');

/*Enable Site Code Update*/
Route::post('enablesitecodeUpdate/', [CAMRGatewayController::class,'enablesitecodeUpdate'])->name('enablesitecodeUpdate')->middleware('isLoggedIn');

/*Disable Site Code Update*/
Route::post('disablesitecodeUpdate/', [CAMRGatewayController::class,'disablesitecodeUpdate'])->name('disablesitecodeUpdate')->middleware('isLoggedIn');

/*Enable SSH*/
Route::post('enableSSH/', [CAMRGatewayController::class,'enableSSH'])->name('enableSSH')->middleware('isLoggedIn');

/*Disable SSH*/
Route::post('disableSSH/', [CAMRGatewayController::class,'disableSSH'])->name('disableSSH')->middleware('isLoggedIn');

/*Enable Force Load Profile*/
Route::post('enableLP/', [CAMRGatewayController::class,'enableLP'])->name('enableLP')->middleware('isLoggedIn');

/*Disable Force Load Profile*/
Route::post('disableLP/', [CAMRGatewayController::class,'disableLP'])->name('disableLP')->middleware('isLoggedIn');

/*Load Meter List Persite*/
Route::get('getMeter/', [CAMRMeterController::class,'getMeter'])->name('getMeter')->middleware('isLoggedIn');
/*Load Meter List Gateway*/
Route::get('getMetersPerGateway/', [CAMRMeterController::class,'getMetersPerGateway'])->name('getMetersPerGateway')->middleware('isLoggedIn');
/*Load Offline Meter List Gateway*/
Route::get('getOfflineMeter/', [CAMRMeterController::class,'getOfflineMeter'])->name('getOfflineMeter')->middleware('isLoggedIn');

/*Create Meter*/
Route::post('/create_meter_post', [CAMRMeterController::class,'create_meter_post'])->name('CREATE_METER_INFO')->middleware('isLoggedIn');
/*Update Meter*/
Route::post('/update_meter_post', [CAMRMeterController::class,'update_meter_post'])->name('UPDATE_METER_INFO')->middleware('isLoggedIn');
/*Meter Info*/
Route::post('/meter_info', [CAMRMeterController::class,'meter_info'])->name('MeterInfo')->middleware('isLoggedIn');
/*Delete Meter Info*/
Route::post('/delete_meter_confirmed', [CAMRMeterController::class,'delete_meter_confirmed'])->name('DeleteMeterInfo')->middleware('isLoggedIn');

/*Load Building List Persite*/
Route::get('getBuilding/', [CAMRBuildingController::class,'get_Building'])->name('getBuilding')->middleware('isLoggedIn');
/*Create Building*/
Route::post('/create_building_post', [CAMRBuildingController::class,'create_building_post'])->name('CREATE_BUILDING_INFO')->middleware('isLoggedIn');
/*Update Building*/
Route::post('/update_building_post', [CAMRBuildingController::class,'update_building_post'])->name('UPDATE_BUILDING_INFO')->middleware('isLoggedIn');
/*Meter Building*/
Route::post('/building_info', [CAMRBuildingController::class,'building_info'])->name('BldgInfo')->middleware('isLoggedIn');
/*Delete Building Info*/
Route::post('/delete_building_confirmed', [CAMRBuildingController::class,'delete_building_confirmed'])->name('DeleteBuildingInfo')->middleware('isLoggedIn');

Route::post('get_building_accordion/', [CAMRBuildingController::class,'get_building_accordion'])->name('get_building_accordion')->middleware('isLoggedIn');

/*Load Meter Location List Persite*/
Route::post('getMeterLocation/', [CAMRMeterLocationController::class,'get_MeterLocation'])->name('getMeterLocation')->middleware('isLoggedIn');
/*Create Meter Location*/
Route::post('/create_meter_location_post', [CAMRMeterLocationController::class,'create_meter_location_post'])->name('CREATE_METER_LOCATION_INFO')->middleware('isLoggedIn');
/*Update Meter Location*/
Route::post('/update_meter_location_post', [CAMRMeterLocationController::class,'update_meter_location_post'])->name('UPDATE_METER_LOCATION_INFO')->middleware('isLoggedIn');
/*Meter Meter Location*/
Route::post('/meter_location_info', [CAMRMeterLocationController::class,'meter_location_info'])->name('MeterLocationInfo')->middleware('isLoggedIn');
/*Delete Meter Location Info*/
Route::post('/delete_meter_location_confirmed', [CAMRMeterLocationController::class,'delete_meter_location_confirmed'])->name('DeleteMeterLocationInfo')->middleware('isLoggedIn');

Route::post('/get_ee_room_location_accordion', [CAMRMeterLocationController::class,'get_ee_room_location_accordion'])->name('get_ee_room_location_accordion')->middleware('isLoggedIn');

/*Get Building List*/
Route::post('/generate_building_list', [ReportSettingsController::class,'generate_building_list'])->name('GetBuildingList')->middleware('isLoggedIn');
/*Get Meter List*/
Route::post('/generate_meter_list', [ReportSettingsController::class,'generate_meter_list'])->name('GetMeterList')->middleware('isLoggedIn');

/*SAP Report Generation*/
Route::get('/sap_report/', [SAPReportController::class,'sap_report'])->name('SAPReport')->middleware('isLoggedIn');
/*Generate via Web Page View*/
Route::post('/generate_sap_report', [SAPReportController::class,'generate_sap_report'])->name('generate_sap_report')->middleware('isLoggedIn');
/*Download SAP Directly via Excel*/
Route::get('/generate_sap_report_excel', [SAPReportController::class,'generate_sap_report_excel'])->name('generate_sap_report_excel')->middleware('isLoggedIn');

/*Download Offline Gateway*/
Route::get('/download_offline_gateway', [OfflineReportController::class,'download_offline_gateway'])->name('download_offline_gateway')->middleware('isLoggedIn');
//downloadofflinemeter
/*Download Offline Meter*/
Route::get('/download_offline_meter', [OfflineReportController::class,'download_offline_meter'])->name('download_offline_meter')->middleware('isLoggedIn');

/*RAW Report Generation*/
Route::get('/raw_report/', [RAWReportController::class,'raw_report'])->name('RAWReport')->middleware('isLoggedIn');
/*Generate via Web Page View*/
Route::post('/generate_raw_report', [RAWReportController::class,'generate_raw_report'])->name('generate_raw_report')->middleware('isLoggedIn');
/*Download SAP Directly via Excel*/
Route::get('/generate_raw_report_excel', [RAWReportController::class,'generate_raw_report_excel'])->name('generate_raw_report_excel')->middleware('isLoggedIn');

/*Site Report Generation*/
Route::get('/site_report/', [SiteReportController::class,'site_report'])->name('SiteReport')->middleware('isLoggedIn');
/*Generate via Web Page View*/
Route::post('/generate_site_report', [SiteReportController::class,'generate_site_report'])->name('generate_site_report')->middleware('isLoggedIn');
/*Download SAP Directly via Excel*/
Route::get('/generate_site_report_excel', [SiteReportController::class,'generate_site_report_excel'])->name('generate_site_report_excel')->middleware('isLoggedIn');

/*Download Meter List an Gateway*/
Route::get('/generate_site_as_built_excel', [SiteAsBuiltController::class,'generate_site_as_built_excel'])->name('generate_site_as_built_excel')->middleware('isLoggedIn');


/*Consumption Per Meter*/
Route::get('/consumption_report/', [ConsumptionReportController::class,'consumption_report'])->name('ConsumptionReport')->middleware('isLoggedIn');
/*Generate Meter Consumption Hourly*/
Route::post('/generate_consumption_report/hourly', [ConsumptionReportController::class,'consumption_report_hourly'])->name('consumption_report_hourly')->middleware('isLoggedIn');
/*Generate Meter Consumption Daily*/
Route::post('/generate_consumption_report/daily', [ConsumptionReportController::class,'consumption_report_daily'])->name('consumption_report_daily')->middleware('isLoggedIn');
/*Downlows Meter Consumption Hourly or Daily*/
Route::get('/download_consumption_report', [ConsumptionReportController::class,'download_consumption_report'])->name('download_consumption_report')->middleware('isLoggedIn');

/*Demand Per Meter*/
Route::get('/demand_report/', [DemandReportController::class,'demand_report'])->name('DemandReport')->middleware('isLoggedIn');
/*Generate Meter Demand Hourly*/
Route::post('/generate_demand_report/hourly', [DemandReportController::class,'demand_report_hourly'])->name('demand_report_hourly')->middleware('isLoggedIn');
/*Generate Meter Demand Daily*/
Route::post('/generate_demand_report/fifteen', [DemandReportController::class,'demand_report_15'])->name('demand_report_15')->middleware('isLoggedIn');
/*Downlows Meter Demand Hourly or Daily*/
Route::get('/download_demand_report', [DemandReportController::class,'download_demand_report'])->name('download_demand_report')->middleware('isLoggedIn');

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

/*Company Info*/
Route::get('/company', [CompanyController::class,'company'])->name('company')->middleware('isLoggedIn');
Route::post('company_list', [CompanyController::class, 'getCompanyList'])->name('CompanyList')->middleware('isLoggedIn');
Route::post('create_company_post', [CompanyController::class, 'create_company_post'])->name('create_company_post')->middleware('isLoggedIn');
Route::post('/company_info', [CompanyController::class, 'company_info'])->name('company_info')->middleware('isLoggedIn');
Route::post('update_company_post', [CompanyController::class, 'update_company_post'])->name('update_company_post')->middleware('isLoggedIn');
Route::post('/delete_company_confirmed', [CompanyController::class, 'delete_company_confirmed'])->name('delete_company_confirmed')->middleware('isLoggedIn');

/*Division Info*/
Route::get('/division', [DivisionController::class,'division'])->name('division')->middleware('isLoggedIn');
Route::post('division_list', [DivisionController::class, 'getDivisionList'])->name('DivisionList')->middleware('isLoggedIn');
Route::post('create_division_post', [DivisionController::class, 'create_division_post'])->name('create_division_post')->middleware('isLoggedIn');
Route::post('/division_info', [DivisionController::class, 'division_info'])->name('division_info')->middleware('isLoggedIn');
Route::post('update_division_post', [DivisionController::class, 'update_division_post'])->name('update_division_post')->middleware('isLoggedIn');
Route::post('/delete_division_confirmed', [DivisionController::class, 'delete_division_confirmed'])->name('delete_division_confirmed')->middleware('isLoggedIn');

/*GET User Info*/
Route::post('/user_info', [UserController::class, 'user_info'])->name('user_info')->middleware('isLoggedIn');
/*Update User*/
Route::post('/update_user_post', [UserController::class,'update_user_post'])->name('update_user_post')->middleware('isLoggedIn');
/*Confirm Delete Switch*/
Route::post('/delete_user_confirmed', [UserController::class, 'delete_user_confirmed'])->name('delete_user_confirmed')->middleware('isLoggedIn');
/*Update User Account*/
Route::post('/user_account_post', [UserController::class,'user_account_post'])->name('user_account_post')->middleware('isLoggedIn');

 
/*CHECK TIME*/
Route::get('/check_time.php', [CAMRGatewayDeviceController::class,'check_time'])->name('check_time');
/*Update CSV*/
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/get_update_csv', [CAMRGatewayDeviceController::class,'csv_update_status'])->name('csv_update_status');
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/get_content_csv', [CAMRGatewayDeviceController::class,'csv_download'])->name('csv_download');
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/reset_update_csv', [CAMRGatewayDeviceController::class,'csv_update_status_reset'])->name('csv_update_status_reset');
/*Update Location*/
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/get_update_location', [CAMRGatewayDeviceController::class,'site_code_update_status'])->name('site_code_update_status');
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/get_content_location', [CAMRGatewayDeviceController::class,'site_code_download'])->name('site_code_download');
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/reset_update_location', [CAMRGatewayDeviceController::class,'site_code_update_status_reset'])->name('site_code_update_status_reset');
/*Remote SSH*/
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/rtu_remote_ssh', [CAMRGatewayDeviceController::class,'gateway_ssh'])->name('gateway_ssh');
/*Force Load Profile*/
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/force_lp', [CAMRGatewayDeviceController::class,'force_load_profile_status'])->name('force_load_profile_status');
Route::get('/rtu/index.php/rtu/rtu_check_update/{mac}/reset_force_lp', [CAMRGatewayDeviceController::class,'force_load_profile_status_reset'])->name('force_load_profile_status_reset');

Route::post('/http_post_server', [CAMRGatewayDeviceController::class, 'http_post_server'])->name('http_post_server');





