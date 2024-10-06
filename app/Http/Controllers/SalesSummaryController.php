<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

use App\Models\SalesSummaryChart;
use App\Charts\SalesLineChart;

use App\Models\BillingTransactionModel;
use App\Models\SalesOrderComponentModel;

use App\Models\ReceivablesPaymentModel;

use Session;
use Validator;
use DataTables;

class SalesSummaryController extends Controller
{
	
	/*Load Monthly Sale Summary Interface*/
	public function MonthlySalesSummary(){
		
		$title = 'Monthly Sales';
		$data = array();
		if(Session::has('loginID')){
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
		}
		
		$api = url('/monthly-chart-line-ajax');
   
        $MonthlyChart = new SalesLineChart;
        $MonthlyChart->labels(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'])->load($api);

		return view("pages.monthly_sales_summary", compact('data','title','MonthlyChart'));
		
	}   
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public function MonthlySaleschartLineAjax(Request $request)
    {
				
        $year = $request->has('year') ? $request->year : date('Y');
		
		$monthly_data = SalesSummaryChart::select(\DB::raw("sum(monthly_sales_total) as count"))
                    ->whereYear('sales_month_year', $year)
                    ->groupBy(\DB::raw("Month(sales_month_year)"))
                    ->pluck('count');
					
        $MonthlyChart = new SalesLineChart;
  
        $MonthlyChart->dataset('Monthly Sales', 'bar', $monthly_data)->options([
                    'fill' => 'true',
                    'borderColor' => '#51C1C0'
                ]);
  
        return $MonthlyChart->api();
		
    }
	
	/*Refresh or Update Data of Monthly Summary*/
	public function ReloadMonthlySales(Request $request)
    {
		
		$year = $request->year;
		$current_year = date("Y");
		//foreach(range(2021, $current_year) as $year_cols){
					
				foreach(range(1, 12) as $month){
					
						$year_month_day = date('Y-m-d', strtotime('Last day of ' . date('F', strtotime($year . '-' . $month . '-01')) . $year));
						$year_r = date('Y', strtotime('Last day of ' . date('F', strtotime($year . '-' . $month . '-01')) . $year));
						$month_r = date('m', strtotime('Last day of ' . date('F', strtotime($year . '-' . $month . '-01')) . $year));
						
						/*Check if Exist*/
						if (SalesSummaryChart::where('sales_month_year', '=', $year_month_day)->exists()) {
									  
						   /*Query on Billing (teves_billing_table)*/
						   $data_billing = BillingTransactionModel::select(\DB::raw("COALESCE(sum(order_total_amount),0) as total"))
							->whereYear('order_date', $year_r)
							->whereMonth('order_date',$month_r)
							//->groupBy(\DB::raw("Month(order_date)"))
							->pluck('total');
							
							/*Query on Sales Order (teves_sales_order_component_table)*/	
							$data_sales_order = SalesOrderComponentModel::select(\DB::raw("COALESCE(sum(order_total_amount),0) as total"))
							->whereYear('sales_order_date', $year_r)
							->whereMonth('sales_order_date',$month_r)
							//->groupBy(\DB::raw("Month(sales_order_date)"))
							->pluck('total');
							
							
							/*Query on Receivables Payment (teves_receivable_payment)*/	
							$data_receivables_payment = ReceivablesPaymentModel::select(\DB::raw("COALESCE(sum(receivable_payment_amount),0) as total"))
							->whereYear('receivable_date_of_payment', $year_r)
							->whereMonth('receivable_date_of_payment',$month_r)
							//->groupBy(\DB::raw("Month(sales_order_date)"))
							->pluck('total');
							
							
							/*Update teves_sales_summary*/
							$update_monthy_sales = new SalesSummaryChart();
							$update_monthy_sales = SalesSummaryChart::where('sales_month_year','=', $year_month_day)
							->update(array('billing_total_sales' => $data_billing[0],
							'sales_order_total_sales' => $data_sales_order[0],
							'receivable_payment_total' => $data_receivables_payment[0],
							'monthly_sales_total' => $data_billing[0] + $data_sales_order[0]));
							
						}else{
						
							/*Insert To teves_sales_summary*/
							$save_month = new SalesSummaryChart();
							$save_month->sales_month_year 			= $year_month_day;
							@$result_month = $save_month->save();
						
							/*Query on Billing (teves_billing_table)*/
						   $data_billing = BillingTransactionModel::select(\DB::raw("COALESCE(sum(order_total_amount),0) as total"))
							->whereYear('order_date', $year_r)
							->whereMonth('order_date',$month_r)
							//->groupBy(\DB::raw("Month(order_date)"))
							->pluck('total');
							
							/*Query on Sales Order (teves_sales_order_component_table)*/	
							$data_sales_order = SalesOrderComponentModel::select(\DB::raw("COALESCE(sum(order_total_amount),0) as total"))
							->whereYear('sales_order_date', $year_r)
							->whereMonth('sales_order_date',$month_r)
							//->groupBy(\DB::raw("Month(sales_order_date)"))
							->pluck('total');
							
							/*Query on Receivables Payment (teves_receivable_payment)*/	
							$data_receivables_payment = ReceivablesPaymentModel::select(\DB::raw("COALESCE(sum(receivable_payment_amount),0) as total"))
							->whereYear('receivable_date_of_payment', $year_r)
							->whereMonth('receivable_date_of_payment',$month_r)
							//->groupBy(\DB::raw("Month(sales_order_date)"))
							->pluck('total');
							
							
							/*Update teves_sales_summary*/
							$update_monthy_sales = new SalesSummaryChart();
							$update_monthy_sales = SalesSummaryChart::where('sales_month_year','=', $year_month_day)
							->update(array('billing_total_sales' => $data_billing[0],
							'sales_order_total_sales' => $data_sales_order[0],
							'receivable_payment_total' => $data_receivables_payment[0],
							'monthly_sales_total' => $data_billing[0] + $data_sales_order[0]));
						
						}

				}
					
	}
}
