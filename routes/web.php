<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\BillingTransactionController;
use App\Http\Controllers\SOBillingTransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTankController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReceivablesController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SalesOrderDeliveryController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderDeliveryController;
use App\Http\Controllers\PurchaseOrderController_v2;
use App\Http\Controllers\CashiersReportController;
use App\Http\Controllers\CashiersReport_Dipstick_Inventory_Controller;
use App\Http\Controllers\UserBranchAccessController;
use App\Http\Controllers\SalesSummaryController;
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

/*SAMPLE EXCEL
Route::get('/sample1', [CAMRSampleExcel::class,'sample1'])->name('site')->middleware('isLoggedIn');
*/

/*Login Page*/
Route::get('/',[UserAuthController::class,'login'])->middleware('alreadyLoggedIn');
Route::post('login-user', [UserAuthController::class,'loginUser'])->name('login-user');
/*Logout*/
Route::get('logout', [UserAuthController::class,'logout']);

/*Reset Password - Unable to Login*/
Route::get('/passwordreset',[UserAuthController::class,'passwordreset'])->name('passwordreset');
Route::post('/reset-password', [EmailController::class, 'sendTemporaryPasswordtoEmail'])->name('sendTemporaryPasswordtoEmail');


/*Load Billing Transaction*/
Route::get('/billing', [BillingTransactionController::class,'billing'])->name('billing')->middleware('isLoggedIn');

Route::get('billing/list', [BillingTransactionController::class, 'getBillingTransactionList'])->name('getBillingTransactionList')->middleware('isLoggedIn');

/*Billed Item to MAnually Generated*/
Route::get('billing/billed_list', [BillingTransactionController::class, 'getBillingTransactionList_Billed'])->name('getBillingTransactionList_Billed')->middleware('isLoggedIn');

/*Create Bill*/
Route::post('/create_bill_post', [BillingTransactionController::class,'create_bill_post'])->name('create_bill_post')->middleware('isLoggedIn');
/*Update Bill*/
Route::post('/update_bill_post', [BillingTransactionController::class,'update_bill_post'])->name('update_bill_post')->middleware('isLoggedIn');
/*GET Bill Info*/
Route::post('/bill_info', [BillingTransactionController::class, 'bill_info'])->name('bill_info')->middleware('isLoggedIn');
/*Confirm Delete Bill*/
Route::post('/delete_bill_confirmed', [BillingTransactionController::class, 'delete_bill_confirmed'])->name('delete_bill_confirmed')->middleware('isLoggedIn');

/*FOR SO Billing*/
Route::get('/create_so_billing', [SOBillingTransactionController::class,'create_so_billing'])->name('create_so_billing')->middleware('isLoggedIn');
/*Create SO*/

Route::get('/so', [SOBillingTransactionController::class,'so'])->name('so')->middleware('isLoggedIn');
Route::get('so/list', [SOBillingTransactionController::class, 'getSOBillingTransactionList'])->name('getSOBillingTransactionList')->middleware('isLoggedIn');
Route::post('/so_info', [SOBillingTransactionController::class, 'so_info'])->name('so_info')->middleware('isLoggedIn');
Route::post('/delete_so_confirmed', [SOBillingTransactionController::class, 'delete_so_confirmed'])->name('delete_so_confirmed')->middleware('isLoggedIn');

Route::post('/create_so_post', [SOBillingTransactionController::class,'create_so_post'])->name('CreateSOPost')->middleware('isLoggedIn');
Route::get('/so_add_product/{id}', [SOBillingTransactionController::class, 'so_add_product'])->name('so_add_product')->middleware('isLoggedIn');
Route::post('/update_so_post', [SOBillingTransactionController::class,'update_so_post'])->name('UpdateSOPost')->middleware('isLoggedIn');
Route::post('/so_add_product_post', [SOBillingTransactionController::class,'so_add_product_post'])->name('SOAddProductPost')->middleware('isLoggedIn');
Route::post('/get_so_product', [SOBillingTransactionController::class,'get_so_product'])->name('GetSoProduct')->middleware('isLoggedIn');
Route::post('/so_update_product_post', [SOBillingTransactionController::class,'so_update_product_post'])->name('SOUpdateProductPost')->middleware('isLoggedIn');

/*Dev Date Nov 30 2022*/
/*Load Product List*/
Route::get('/product', [ProductController::class,'product'])->name('product')->middleware('isLoggedIn');
Route::get('product/list', [ProductController::class, 'getProductList'])->name('getProductList')->middleware('isLoggedIn');
/*Create Product*/
Route::post('/create_product_post', [ProductController::class,'create_product_post'])->name('create_product_post')->middleware('isLoggedIn');
/*GET Product Info*/
Route::post('/product_info', [ProductController::class, 'product_info'])->name('product_info')->middleware('isLoggedIn');
/*Update Product*/
Route::post('/update_product_post', [ProductController::class,'update_product_post'])->name('update_product_post')->middleware('isLoggedIn');
/*Confirm Delete Product*/
Route::post('/delete_product_confirmed', [ProductController::class, 'delete_product_confirmed'])->name('delete_product_confirmed')->middleware('isLoggedIn');
/*Load Product Pricing Per Branch 01-02-2023*/
Route::post('/get_product_pricing_per_branch', [ProductController::class,'get_product_pricing_per_branch'])->name('ProductPricingPerBranch')->middleware('isLoggedIn');
/*Save Product Pricing Per Branch 01-02-2023*/
Route::post('/save_branches_product_pricing_post', [ProductController::class,'save_branches_product_pricing_post'])->name('save_branches_product_pricing_post')->middleware('isLoggedIn');
/*Load Product Pricing Per Branch per Billing 01-09-2023*/
Route::post('/get_product_list_pricing_per_branch', [ProductController::class,'get_product_list_pricing_per_branch'])->name('ProductListPricingPerBranch')->middleware('isLoggedIn');
/**/
Route::get('/update_product_information', [ProductController::class, 'update_product_information'])->name('update_product_information')->middleware('isLoggedIn');
/*Product Tank*/
Route::post('/get_product_tank', [ProductTankController::class,'get_product_tank'])->name('ProductTank')->middleware('isLoggedIn');
/*Product Tank per Branch*/
Route::post('/get_product_tank_per_branch', [ProductTankController::class,'get_product_tank_per_branch'])->name('ProductTankPerBranch')->middleware('isLoggedIn');
/*Create Product Tank*/
Route::post('/create_tank_post', [ProductTankController::class,'create_tank_post'])->name('create_tank_post')->middleware('isLoggedIn');
/* Product Tank Info*/
Route::post('/product_tank_info', [ProductTankController::class, 'product_tank_info'])->name('product_tank_info')->middleware('isLoggedIn');
/*Update Product Tank*/
Route::post('/update_tank_post', [ProductTankController::class,'update_tank_post'])->name('update_tank_post')->middleware('isLoggedIn');

Route::post('/delete_product_tank_confirmed', [ProductTankController::class, 'delete_product_tank_confirmed'])->name('delete_product_tank_confirmed')->middleware('isLoggedIn');

/*Dev Date Nov 30 2022*/
/*Load Client List*/
Route::get('/client', [ClientController::class,'client'])->name('client')->middleware('isLoggedIn');
Route::get('client/list', [ClientController::class, 'getClientList'])->name('getClientList')->middleware('isLoggedIn');
/*Create Product*/
Route::post('/create_client_post', [ClientController::class,'create_client_post'])->name('create_client_post')->middleware('isLoggedIn');
/*GET Product Info*/
Route::post('/client_info', [ClientController::class, 'client_info'])->name('client_info')->middleware('isLoggedIn');
/*Update Product*/
Route::post('/update_client_post', [ClientController::class,'update_client_post'])->name('update_client_post')->middleware('isLoggedIn');
/*Confirm Delete Product*/
Route::post('/delete_client_confirmed', [ClientController::class, 'delete_client_confirmed'])->name('delete_client_confirmed')->middleware('isLoggedIn');


/*Load Billing Report History Interface*/
Route::get('/billing_history', [ReportController::class,'billing_history'])->name('report')->middleware('isLoggedIn');
/*Generate via Web Page View - For receivable*/
Route::post('/generate_report_recievable', [ReportController::class,'generate_report_recievable'])->name('generate_report_recievable')->middleware('isLoggedIn');
/*Generate via Web Page View - For receivable*/
Route::post('/generate_report_recievable_after_saved', [ReportController::class,'generate_report_recievable_after_saved'])->name('generate_report_recievable_after_saved')->middleware('isLoggedIn');


/*Load SOA Summary Report History Interface*/
Route::get('/soa_summary_history', [ReportController::class,'soa_summary_history'])->name('soa_summary_history')->middleware('isLoggedIn');
/*Generate via Web Page View - For receivable*/
Route::post('/generate_soa_summary', [ReportController::class,'generate_soa_summary'])->name('generate_soa_summary')->middleware('isLoggedIn');
/*Generate via Web Page View - For receivable*/
Route::get('/generate_soa_summary_pdf', [ReportController::class,'generate_soa_summary_pdf'])->name('generate_soa_summary_pdf')->middleware('isLoggedIn');
//Route::get('/generate_soa_summary_pdf', [ReportController::class,'generate_soa_summary_pdf'])->name('generate_receivable_soa_pdf')->middleware('isLoggedIn');
//Route::post('/generate_report_recievable_after_saved', [ReportController::class,'generate_report_recievable_after_saved'])->name('generate_report_recievable_after_saved')->middleware('isLoggedIn');


/*Generate via Web Page View*/
Route::post('/generate_report', [ReportController::class,'generate_report'])->name('generate_report')->middleware('isLoggedIn');
/*Download Directly via Excel*/
Route::get('/generate_report_excel', [ReportController::class,'generate_report_excel'])->name('generate_report_excel')->middleware('isLoggedIn');
/*Download via PDF*/
Route::get('/generate_report_pdf', [ReportController::class,'generate_report_pdf'])->name('generate_report_pdf')->middleware('isLoggedIn');

/*Download via PDF*/
Route::get('/generate_receivable_covered_bill_pdf', [ReportController::class,'generate_receivable_covered_bill_pdf'])->name('generate_receivable_covered_bill_pdf')->middleware('isLoggedIn');

/*Load User Account List for Admin Only*/
Route::get('/user', [UserController::class,'user'])->name('user')->middleware('isLoggedIn');
/*Get List of User*/
Route::post('user_list', [UserController::class, 'getUserList'])->name('UserList')->middleware('isLoggedIn');
/*Create User*/
Route::post('/create_user_post', [UserController::class,'create_user_post'])->name('create_user_post')->middleware('isLoggedIn');

/*GET User Info*/
Route::post('/user_info', [UserController::class, 'user_info'])->name('user_info')->middleware('isLoggedIn');
/*Update User*/
Route::post('/update_user_post', [UserController::class,'update_user_post'])->name('update_user_post')->middleware('isLoggedIn');
/*Confirm Delete Switch*/
Route::post('/delete_user_confirmed', [UserController::class, 'delete_user_confirmed'])->name('delete_user_confirmed')->middleware('isLoggedIn');
/*Update User Account*/
Route::post('/user_account_post', [UserController::class,'user_account_post'])->name('user_account_post')->middleware('isLoggedIn');


/*Branch Access*/
Route::get('user_branch_access', [UserBranchAccessController::class, 'getUserBranchAccess'])->name('getUserBranchAccess')->middleware('isLoggedIn');
/*Add Site Access*/
Route::post('/add_user_access_post', [UserBranchAccessController::class,'add_user_access_post'])->name('add_user_access_post')->middleware('isLoggedIn');

/*Receivables*/
/*December 17, 2022*/
/*Load Create Receivable Interface June 18, 2023*/
Route::get('/create_recievable', [ReceivablesController::class,'create_recievable'])->name('create_recievable')->middleware('isLoggedIn');
/*Receivables List*/
Route::get('/receivables', [ReceivablesController::class,'receivables'])->name('receivables')->middleware('isLoggedIn');
Route::get('receivables/list_billing', [ReceivablesController::class, 'getReceivablesList_billing'])->name('getReceivablesList_billing')->middleware('isLoggedIn');
Route::get('receivables/list_salesorder', [ReceivablesController::class, 'getReceivablesList_sales_order'])->name('getReceivablesList_sales_order')->middleware('isLoggedIn');
/*GET receivables Info*/
Route::post('/receivable_info', [ReceivablesController::class, 'receivable_info'])->name('receivable_info')->middleware('isLoggedIn');
/*Confirm Delete receivables*/
Route::post('/delete_receivable_confirmed', [ReceivablesController::class, 'delete_receivable_confirmed'])->name('delete_receivable_confirmed')->middleware('isLoggedIn');
/*Create receivables*/
Route::post('/create_receivables_post', [ReceivablesController::class,'create_receivables_post'])->name('create_receivables_post')->middleware('isLoggedIn');
/*Create receivables*/
Route::post('/update_receivables_post', [ReceivablesController::class,'update_receivables_post'])->name('update_receivables_post')->middleware('isLoggedIn');
/*Get Receivables Payment*/
Route::post('/get_receivable_payment_list', [ReceivablesController::class,'get_receivable_payment_list'])->name('get_receivable_payment_list')->middleware('isLoggedIn');
/*Delete Receivables Payment*/
Route::post('/delete_receivable_payment_item', [ReceivablesController::class,'delete_receivable_payment_item'])->name('delete_receivable_payment_item')->middleware('isLoggedIn');
/*Save Receivables Payment*/
Route::post('/save_receivable_payment_post', [ReceivablesController::class,'save_receivable_payment_post'])->name('save_receivable_payment_post')->middleware('isLoggedIn');
/*Save Receivables Payment from Sales Order*/
Route::post('/create_receivables_from_sale_order_post', [ReceivablesController::class,'create_receivables_from_sale_order_post'])->name('create_receivables_from_sale_order_post')->middleware('isLoggedIn');
Route::post('/update_receivables_from_sale_order_post', [ReceivablesController::class,'update_receivables_from_sale_order_post'])->name('update_receivables_from_sale_order_post')->middleware('isLoggedIn');



Route::get('/receivable_from_billing_form', [ReceivablesController::class, 'receivable_from_billing_form'])->name('receivable_from_billing_form')->middleware('isLoggedIn');
Route::post('/billing_to_receivable_product', [ReceivablesController::class,'billing_to_receivable_product'])->name('billing_to_receivable_product')->middleware('isLoggedIn');

/*Sales Order*/
/*January 04, 2023*/
Route::get('/salesorder', [SalesOrderController::class,'salesorder'])->name('salesorder')->middleware('isLoggedIn');
Route::get('salesorder/list', [SalesOrderController::class, 'getSalesOrderList'])->name('getSalesOrderList')->middleware('isLoggedIn');
/*GET Sales Order Info*/
Route::post('/sales_order_info', [SalesOrderController::class, 'sales_order_info'])->name('sales_order_info')->middleware('isLoggedIn');
/*Confirm Delete Sales Order*/
Route::post('/delete_sales_order_confirmed', [SalesOrderController::class, 'delete_sales_order_confirmed'])->name('delete_sales_order_confirmed')->middleware('isLoggedIn');
/*Create Sales Order*/
Route::post('/create_sales_order_post', [SalesOrderController::class,'create_sales_order_post'])->name('create_sales_order_post')->middleware('isLoggedIn');
/*Update Sales Order*/
Route::post('/update_sales_order_post', [SalesOrderController::class,'update_sales_order_post'])->name('update_sales_order_post')->middleware('isLoggedIn');
/*Get Sales Order Product Item*/
Route::post('/get_sales_order_product_list', [SalesOrderController::class,'get_sales_order_product_list'])->name('get_sales_order_product_list')->middleware('isLoggedIn');
/*Delete Sales Order Product Item*/
Route::post('/delete_sales_order_item', [SalesOrderController::class,'delete_sales_order_item'])->name('delete_sales_order_item')->middleware('isLoggedIn');
/*Update Sales Order Status*/
Route::post('/update_sales_status', [SalesOrderController::class,'update_sales_status'])->name('update_sales_status')->middleware('isLoggedIn');
/*Update Sales Order Delivery Status*/
Route::post('/update_sales_order_delivery_status', [SalesOrderController::class,'update_sales_order_delivery_status'])->name('update_sales_order_delivery_status')->middleware('isLoggedIn');
/*Download Sales Order via PDF*/
Route::get('/generate_sales_order_pdf', [ReportController::class,'generate_sales_order_pdf'])->name('generate_sales_order_pdf')->middleware('isLoggedIn');
Route::get('/generate_sales_order_delivery_status_pdf', [ReportController::class,'generate_sales_order_delivery_status_pdf'])->name('generate_sales_order_delivery_status_pdf')->middleware('isLoggedIn');

/*New Version for Sales Order*/
Route::get('/sales_order_form', [SalesOrderController::class, 'sales_order_form'])->name('sales_order_form')->middleware('isLoggedIn');
Route::post('/sales_order_component_info', [SalesOrderController::class,'sales_order_component_info'])->name('sales_order_component_info')->middleware('isLoggedIn');
Route::post('/sales_order_component_compose', [SalesOrderController::class,'sales_order_component_compose'])->name('SalesOrderComponentCompose')->middleware('isLoggedIn');
Route::post('/delete_sales_order_component_confirmed', [SalesOrderController::class,'delete_sales_order_component_confirmed'])->name('SalesOrderDeleteComponent')->middleware('isLoggedIn');

/*Sales Order Delivery*/
Route::post('/sales_order_component_delivery_compose', [SalesOrderDeliveryController::class,'sales_order_component_delivery_compose'])->name('SalesOrderDeliveryCompose')->middleware('isLoggedIn');
Route::post('/get_sales_order_product_list_delivery', [SalesOrderDeliveryController::class,'get_sales_order_product_list_delivery'])->name('ProductListDelivery')->middleware('isLoggedIn');
Route::post('/sales_order_component_delivery_info', [SalesOrderDeliveryController::class,'sales_order_component_delivery_info'])->name('SalesOrderDeliveryInfo')->middleware('isLoggedIn');
Route::post('/delete_sales_order_product_delivery_confirmed', [SalesOrderDeliveryController::class,'delete_sales_order_product_delivery_confirmed'])->name('SalesOrderDeleteDelivery')->middleware('isLoggedIn');


/*Get Receivable Payment Item*/
Route::post('/receivable_payment_list', [ReceivablesController::class,'receivable_payment_list'])->name('receivable_payment_list')->middleware('isLoggedIn');
/*Get Receivable Per Payment Item*/
Route::post('/receivable_payment_info', [ReceivablesController::class,'receivable_payment_info'])->name('ReceivablePaymentInfo')->middleware('isLoggedIn');

/* Sales Order to Receivable Payment Action */
Route::post('/sales_order_receivable_payment',[ReceivablesController::class,'sales_order_receivable_payment'])->name('sales_order_receivable_payment')->middleware('isLoggedIn');
/*Delete Sales Order Payment Item*/
Route::post('/sales_order_receivable_delete_payment', [ReceivablesController::class,'sales_order_receivable_delete_payment'])->name('SalesOrderDeletePayment')->middleware('isLoggedIn');
#Route::post('/get_sales_order_payment_list', [SalesOrderController::class,'get_sales_order_payment_list'])->name('get_sales_order_payment_list')->middleware('isLoggedIn');

/*Billing to Receivable Payment Item*/
Route::post('/billing_receivable_payment_post',[ReceivablesController::class,'billing_receivable_payment_post'])->name('billing_receivable_payment_post')->middleware('isLoggedIn');
/*Delete Billing Payment Item*/
Route::post('/billing_receivable_delete_payment', [ReceivablesController::class,'billing_receivable_delete_payment'])->name('BillingDeletePayment')->middleware('isLoggedIn');

/*Delete Purchase Order Payment Item*/
Route::post('/delete_purchase_order_payment_item', [PurchaseOrderController::class,'delete_purchase_order_payment_item'])->name('delete_purchase_order_payment_item')->middleware('isLoggedIn');
/*Update Purchase Status*/
Route::post('/update_purchase_status', [PurchaseOrderController::class,'update_purchase_status'])->name('update_purchase_status')->middleware('isLoggedIn');

/*Purchase Order Version 2*/
/*January 25, 2023*/
Route::get('/purchaseorder_v2', [PurchaseOrderController_v2::class,'purchaseorder'])->name('purchaseorder_v2')->middleware('isLoggedIn');
Route::get('purchaseorder_v2/list', [PurchaseOrderController_v2::class, 'getPurchaseOrderList_v2'])->name('getPurchaseOrderList_v2')->middleware('isLoggedIn');
/*GET Purchase Order Info*/
Route::post('/purchase_order_info', [PurchaseOrderController_v2::class, 'purchase_order_info'])->name('purchase_order_info')->middleware('isLoggedIn');
/*Confirm Delete Purchase Order*/
Route::post('/delete_purchase_order_confirmed', [PurchaseOrderController_v2::class, 'delete_purchase_order_confirmed'])->name('delete_purchase_order_confirmed')->middleware('isLoggedIn');
/*Create Purchase Order*/
Route::post('/create_purchase_order_post_v2', [PurchaseOrderController_v2::class,'create_purchase_order_post'])->name('SavePurchaseOrder')->middleware('isLoggedIn');
/*Update Purchase Order*/
Route::post('/update_purchase_order_post', [PurchaseOrderController_v2::class,'update_purchase_order_post'])->name('update_purchase_order_post')->middleware('isLoggedIn');
/*Create Purchase Order Product Item*/
Route::post('/create_purchase_order_product_item', [PurchaseOrderController_v2::class,'create_purchase_order_product_item'])->name('PurchaseOrderProduct')->middleware('isLoggedIn');
/*Create Product for Purchase Order*/
Route::post('/create_purchase_order_post_v2', [PurchaseOrderController_v2::class,'create_purchase_order_post'])->name('SavePurchaseOrder')->middleware('isLoggedIn');

Route::post('/purchase_order_product_info', [PurchaseOrderController_v2::class,'purchase_order_product_info'])->name('purchase_order_product_info')->middleware('isLoggedIn');

/*Get Purchase Order Product Item*/
Route::post('/get_purchase_order_product_list', [PurchaseOrderController_v2::class,'get_purchase_order_product_list'])->name('get_purchase_order_product_list')->middleware('isLoggedIn');
/*Delete Purchase Order Product Item*/
Route::post('/delete_purchase_order_product', [PurchaseOrderController_v2::class,'delete_purchase_order_product'])->name('PurchaseOrderDeleteProduct')->middleware('isLoggedIn');
/*Get Purchase Order Payment Item*/
Route::post('/get_purchase_order_payment_list', [PurchaseOrderController_v2::class,'get_purchase_order_payment_list'])->name('get_purchase_order_payment_list')->middleware('isLoggedIn');
/*Delete Purchase Order Payment Item*/
Route::post('/delete_purchase_order_payment_item', [PurchaseOrderController_v2::class,'delete_purchase_order_payment_item'])->name('delete_purchase_order_payment_item')->middleware('isLoggedIn');
/*Update Purchase Status*/
Route::post('/update_purchase_status', [PurchaseOrderController_v2::class,'update_purchase_status'])->name('update_purchase_status')->middleware('isLoggedIn');

Route::get('/purchase_order_form/{id}', [PurchaseOrderController_v2::class, 'purchase_order_form'])->name('purchase_order_forms')->middleware('isLoggedIn');

/*Create Payment for Purchase Order*/
Route::post('/create_purchase_order_payment_item', [PurchaseOrderController_v2::class,'create_purchase_order_payment_item'])->name('PurchaseOrderPayment')->middleware('isLoggedIn');
Route::post('/purchase_order_payment_info', [PurchaseOrderController_v2::class,'purchase_order_payment_info'])->name('PaymentInfo')->middleware('isLoggedIn');
Route::post('/purchase_order_delete_payment', [PurchaseOrderController_v2::class,'purchase_order_delete_payment'])->name('DeletePayment')->middleware('isLoggedIn');

Route::post('/save_purchase_order_payment',[PurchaseOrderController_v2::class,'save_purchase_order_payment'])->name('save_purchase_order_payment')->middleware('isLoggedIn');

Route::post('upload', [PurchaseOrderController_v2::class, 'store']);

/*Purchase Order Delivery*/
Route::post('/purchase_order_component_delivery_compose', [PurchaseOrderDeliveryController::class,'purchase_order_component_delivery_compose'])->name('PurchaseOrderDeliveryCompose')->middleware('isLoggedIn');
Route::post('/get_purchase_order_product_list_delivery', [PurchaseOrderDeliveryController::class,'get_purchase_order_product_list_delivery'])->name('PurchaseOrderProductListDelivery')->middleware('isLoggedIn');
Route::post('/purchase_order_component_delivery_info', [PurchaseOrderDeliveryController::class,'purchase_order_component_delivery_info'])->name('PurchaseOrderDeliveryInfo')->middleware('isLoggedIn');
Route::post('/delete_purchase_order_product_delivery_confirmed', [PurchaseOrderDeliveryController::class,'delete_purchase_order_product_delivery_confirmed'])->name('PurchaseOrderDeleteDelivery')->middleware('isLoggedIn');


/*Download Sales Order via PDF*/
Route::get('/generate_sales_order_pdf', [ReportController::class,'generate_sales_order_pdf'])->name('generate_sales_order_pdf')->middleware('isLoggedIn');
/*Download Purchase Order via PDF*/
Route::get('/generate_purchase_order_pdf', [ReportController::class,'generate_purchase_order_pdf'])->name('generate_purchase_order_pdf')->middleware('isLoggedIn');
Route::get('/generate_purchase_order_delivery_status_pdf', [ReportController::class,'generate_purchase_order_delivery_status_pdf'])->name('generate_purchase_order_delivery_status_pdf')->middleware('isLoggedIn');

Route::get('/generate_purchase_order_payment_pdf', [ReportController::class,'generate_purchase_order_payment_pdf'])->name('generate_purchase_order_payment_pdf')->middleware('isLoggedIn');


/*Download via PDF*/
Route::get('/generate_receivable_pdf', [ReportController::class,'generate_receivable_pdf'])->name('generate_receivable_pdf')->middleware('isLoggedIn');
Route::get('/generate_receivable_soa_pdf', [ReportController::class,'generate_receivable_soa_pdf'])->name('generate_receivable_soa_pdf')->middleware('isLoggedIn');
Route::get('/generate_test_pdf', [ReportController::class,'generate_test_pdf'])->name('generate_test_pdf')->middleware('isLoggedIn');

/*Dev Date Mar 27 2023*/
/*Load Supplier List*/
Route::get('/supplier', [SupplierController::class,'supplier'])->name('supplier')->middleware('isLoggedIn');
Route::get('supplier/list', [SupplierController::class, 'getSupplierList'])->name('getSupplierList')->middleware('isLoggedIn');
/*Create Product*/
Route::post('/create_supplier_post', [SupplierController::class,'create_supplier_post'])->name('create_supplier_post')->middleware('isLoggedIn');
/*GET Product Info*/
Route::post('/supplier_info', [SupplierController::class, 'supplier_info'])->name('supplier_info')->middleware('isLoggedIn');
/*Update Product*/
Route::post('/update_supplier_post', [SupplierController::class,'update_supplier_post'])->name('update_supplier_post')->middleware('isLoggedIn');
/*Confirm Delete Product*/
Route::post('/delete_supplier_confirmed', [SupplierController::class, 'delete_supplier_confirmed'])->name('delete_supplier_confirmed')->middleware('isLoggedIn');

/*Load Cashier's Report List*/
/*Dev Date May 10, 2023*/
Route::get('/cashier_report', [CashiersReportController::class,'cashierReport'])->name('cashierReport')->middleware('isLoggedIn');
Route::get('cashier_report/list', [CashiersReportController::class, 'getCashierReport'])->name('getCashierReport')->middleware('isLoggedIn');
/*Create Cashier's Report Primary Information*/
Route::post('/create_cashier_report_post', [CashiersReportController::class,'create_cashier_report_post'])->name('create_cashier_report_post')->middleware('isLoggedIn');
/*Update Cashier's Report Primary Information*/
Route::post('/update_cashier_report_post', [CashiersReportController::class,'update_cashier_report_post'])->name('update_cashier_report_post')->middleware('isLoggedIn');
/*GET Cashier's Report Primary Information*/
Route::post('/cashiers_report_info', [CashiersReportController::class, 'cashiers_report_info'])->name('cashiers_report_info')->middleware('isLoggedIn');
/**/
Route::post('/delete_cashiers_report_info', [CashiersReportController::class, 'delete_cashiers_report_info'])->name('delete_cashiers_report_info')->middleware('isLoggedIn');

/*Cashiers Report Part 1*/
Route::get('/cashiers_report_form/{id}', [CashiersReportController::class, 'cashiers_report_form'])->name('cashiers_report_form')->middleware('isLoggedIn');
/*Save Cashier's Report Product*/
Route::post('/save_product_cashiers_report_p1', [CashiersReportController::class,'save_product_cashiers_report_p1'])->name('SAVE_CHR_PH1')->middleware('isLoggedIn');
/* Load Product P1 */
Route::post('/get_cashiers_report_product_p1', [CashiersReportController::class,'get_cashiers_report_product_p1'])->name('GetCashiersProductP1')->middleware('isLoggedIn');
/* Delete Product P1 */
Route::post('/delete_cashiers_report_product_p1', [CashiersReportController::class,'delete_cashiers_report_product_p1'])->name('DeleteCashiersProductP1')->middleware('isLoggedIn');
/*GET Cashiers report product P1*/
Route::post('/cashiers_report_p1_info', [CashiersReportController::class, 'cashiers_report_p1_info'])->name('CRP1_info')->middleware('isLoggedIn');

/*Cashiers Report Part 2*/
Route::post('/save_product_cashiers_report_PH2', [CashiersReportController::class,'save_product_cashiers_report_PH2'])->name('SAVE_CHR_PH2')->middleware('isLoggedIn');
/* Load Product P2 */
Route::post('/get_cashiers_report_product_p2', [CashiersReportController::class,'get_cashiers_report_product_p2'])->name('GetCashiersProductP2')->middleware('isLoggedIn');
/* Delete Product P2 */
Route::post('/delete_cashiers_report_product_p2', [CashiersReportController::class,'delete_cashiers_report_product_p2'])->name('DeleteCashiersProductP2')->middleware('isLoggedIn');
/*GET Cashiers report product P2*/
Route::post('/cashiers_report_p2_info', [CashiersReportController::class, 'cashiers_report_p2_info'])->name('CRP2_info')->middleware('isLoggedIn');

/*Cashiers Report Part 3*/
Route::post('/save_product_cashiers_report_PH3', [CashiersReportController::class,'save_product_cashiers_report_PH3'])->name('SAVE_CHR_PH3')->middleware('isLoggedIn');
/* Load Product P3 */
Route::post('/get_cashiers_report_product_p3_DISCOUNTS', [CashiersReportController::class,'get_cashiers_report_product_p3_DISCOUNTS'])->name('GetCashiersProductP3_DISCOUNTS')->middleware('isLoggedIn');
/* Delete Product P3 */
Route::post('/delete_cashiers_report_product_p3', [CashiersReportController::class,'delete_cashiers_report_product_p3'])->name('DeleteCashiersProductP3')->middleware('isLoggedIn');
/*GET Cashiers report product P3*/
Route::post('/cashiers_report_p3_info_SALES_CREDIT', [CashiersReportController::class, 'cashiers_report_p3_info_SALES_CREDIT'])->name('CRP3_info_SALES_CREDIT')->middleware('isLoggedIn');
/*GET Cashiers report product P3*/
Route::post('/cashiers_report_p3_info_OTHERS', [CashiersReportController::class, 'cashiers_report_p3_info_OTHERS'])->name('CRP3_info_OTHERS')->middleware('isLoggedIn');
/*GET Cashiers report product P3*/
Route::post('/cashiers_report_p3_info_DISCOUNT', [CashiersReportController::class, 'cashiers_report_p3_info_DISCOUNT'])->name('CRP3_info_DISCOUNT')->middleware('isLoggedIn');

/*Cashiers Report Part 3.1
Route::post('/save_product_cashiers_report_PH3_1', [CashiersReportController::class,'save_product_cashiers_report_PH3_1'])->name('SAVE_CHR_PH3_1')->middleware('isLoggedIn');
*//* Load Product P3 */
Route::post('/get_cashiers_report_product_p3_SALES_CREDIT', [CashiersReportController::class,'get_cashiers_report_product_p3_SALES_CREDIT'])->name('GetCashiersProductP3_SALES_CREDIT')->middleware('isLoggedIn');
Route::post('/get_cashiers_report_product_p3_OTHERS', [CashiersReportController::class,'get_cashiers_report_product_p3_OTHERS'])->name('GetCashiersProductP3_OTHERS')->middleware('isLoggedIn');

/* Delete Product P3
Route::post('/delete_cashiers_report_product_p3_1', [CashiersReportController::class,'delete_cashiers_report_product_p3_1'])->name('DeleteCashiersProductP3_1')->middleware('isLoggedIn');
 *//*GET Cashiers report product P3
Route::post('/cashiers_report_p3_info_1', [CashiersReportController::class, 'cashiers_report_p3_info_1'])->name('CRP3_info_1')->middleware('isLoggedIn');
*/
/*Cashiers Report Part 4*/
/*Save or Update*/
Route::post('/save_cashiers_report_PH4', [CashiersReportController::class,'save_cashiers_report_PH4'])->name('SAVE_CHR_PH4')->middleware('isLoggedIn');
/* Load P4 */
Route::post('/get_cashiers_report_p4', [CashiersReportController::class,'get_cashiers_report_p4'])->name('GetCashiersP4')->middleware('isLoggedIn');
/* Delete P4 */
Route::post('/delete_cashiers_report_p4', [CashiersReportController::class,'delete_cashiers_report_p4'])->name('DeleteCashiersP4')->middleware('isLoggedIn');
/*GET Cashiers report P4*/
Route::post('/cashiers_report_p4_info', [CashiersReportController::class, 'cashiers_report_p4_info'])->name('CRP4_info')->middleware('isLoggedIn');

/*Cashiers Report Part 5*/
/*Save or Update*/
Route::post('/save_cashiers_report_PH5', [CashiersReportController::class,'save_cashiers_report_PH5'])->name('SAVE_CHR_PH5')->middleware('isLoggedIn');
/*GET Cashiers report P5*/
Route::post('/cashiers_report_p5_info', [CashiersReportController::class, 'cashiers_report_p5_info'])->name('CRP5_info')->middleware('isLoggedIn');

/*GET Cashiers Summary Report*/
Route::post('/cashiers_report_summary_info', [CashiersReportController::class, 'cashiers_report_summary_info'])->name('CashiersReportSummary')->middleware('isLoggedIn');



/*Cashiers Report Part 6*/
Route::post('/save_product_cashiers_report_p6', [CashiersReportController::class,'save_product_cashiers_report_p6'])->name('SAVE_CHR_PH6')->middleware('isLoggedIn');
/* Load P6 */
Route::post('/get_product_inventory_list', [CashiersReportController::class,'get_product_inventory_list'])->name('GetCashiersP6')->middleware('isLoggedIn');
/*Delete*/
Route::post('/delete_cashiers_report_p6', [CashiersReportController::class,'delete_cashiers_report_p6'])->name('DeleteCashiersProductP6')->middleware('isLoggedIn');

Route::post('/cashiers_report_p6_info', [CashiersReportController::class, 'cashiers_report_p6_info'])->name('CRP6_info')->middleware('isLoggedIn');
/*Load Cashiers Report */
Route::get('/generate_cashier_report_pdf', [CashiersReportController::class,'generate_cashier_report_pdf'])->name('generate_cashier_report_pdf')->middleware('isLoggedIn');

/*Cahiers's Report Dipstick Inventory*/
Route::post('/save_product_cashiers_report_p7', [CashiersReport_Dipstick_Inventory_Controller::class,'save_product_cashiers_report_p7'])->name('SAVE_CHR_PH7')->middleware('isLoggedIn');
/* Load P6 */
Route::post('/get_product_dipstick_inventory_list', [CashiersReport_Dipstick_Inventory_Controller::class,'get_product_dipstick_inventory_list'])->name('GetCashiersP7')->middleware('isLoggedIn');
/*Delete*/
Route::post('/delete_cashiers_report_p7', [CashiersReport_Dipstick_Inventory_Controller::class,'delete_cashiers_report_p7'])->name('DeleteCashiersProductP7')->middleware('isLoggedIn');

Route::post('/cashiers_report_p7_info', [CashiersReport_Dipstick_Inventory_Controller::class, 'cashiers_report_p7_info'])->name('CRP7_info')->middleware('isLoggedIn');

/*Load Cashiers Report VIA PDF */
//Route::get('/generate_cashier_report_pdf', [CashiersReport::class,'generate_cashier_report_pdf'])->name('generate_cashier_report_pdf')->middleware('isLoggedIn');


/*Dev Date Nov 30 2022*/
/*Load Branch List*/
Route::get('/branch', [BranchController::class,'branch'])->name('branch')->middleware('isLoggedIn');
Route::get('branch/list', [BranchController::class, 'getBranchList'])->name('getBranchList')->middleware('isLoggedIn');
/*Create Product*/
Route::post('/create_branch_post', [BranchController::class,'create_branch_post'])->name('CreateBranch')->middleware('isLoggedIn');
/*GET Product Info*/
Route::post('/branch_info', [BranchController::class, 'branch_info'])->name('BranchInfo')->middleware('isLoggedIn');
/*Update Product*/
Route::post('/update_branch_post', [BranchController::class,'update_branch_post'])->name('UpdateBranch')->middleware('isLoggedIn');
/*Confirm Delete Product*/
Route::post('/delete_branch_confirmed', [BranchController::class, 'delete_branch_confirmed'])->name('DeleteBranch')->middleware('isLoggedIn');



/* Sales Summary */
Route::get('/monthly_sales', [SalesSummaryController::class,'MonthlySalesSummary'])->name('MonthlySalesSummary')->middleware('isLoggedIn');
Route::get('/monthly-chart-line-ajax', [SalesSummaryController::class,'MonthlySaleschartLineAjax'])->name('MonthlySaleschartLineAjax')->middleware('isLoggedIn');
Route::post('/reload_monthly_sales_per_year', [SalesSummaryController::class,'ReloadMonthlySales'])->name('ReloadMonthlySales')->middleware('isLoggedIn');

Route::get('monthly-chart-line-ajax', 'SalesSummaryController@MonthlySaleschartLineAjax');

/*CHARTS TEST

Route::get('chart-line', 'ChartController@chartLine');
Route::get('chart-line-ajax', 'ChartController@chartLineAjax');

*/
