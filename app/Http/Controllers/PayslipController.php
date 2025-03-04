<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BranchModel;


use Session;
use Validator;
//use DataTables;
use Illuminate\Support\Facades\DB;
/*Excel*/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

/*PDF*/
use PDF;
use DataTables;

class PayslipController extends Controller
{
	/*Employee's Individual Payslip*/
	public function employees_payslip(){
		
		if(Session::has('loginID')){
				
			if(Session::has('loginID')){
				
				$title = 'Payslip';
				$data = array();
				
				$data = User::where('user_id', '=', Session::get('loginID'))->first();
				
				$teves_branch = BranchModel::all();
				
				return view("reports.employee_payslip", compact('data','title','teves_branch'));
				
			}

		}
	}  	

	public function employees_payslip_pdf(Request $request){

		$request->validate([
			'receivable_id'      		=> 'required'
        ], 
        [
			'receivable_id.required' 	=> 'Please select a receivable_id'
        ]
		);

		$receivable_id = $request->receivable_id;
					
				$receivable_data = ReceivablesModel::where('receivable_id', $request->receivable_id)
				->join('teves_client_table', 'teves_client_table.client_id', '=', 'teves_receivable_table.client_idx')
              	->get([
					'teves_receivable_table.receivable_id',
					'teves_receivable_table.sales_order_idx',
					'teves_receivable_table.receivable_name',
					'teves_receivable_table.billing_date',
					'teves_client_table.client_name',
					'teves_client_table.client_address',
					'teves_receivable_table.control_number',
					'teves_client_table.client_tin',
					'teves_receivable_table.or_number',
					'teves_receivable_table.ar_reference',
					'teves_receivable_table.payment_term',
					'teves_receivable_table.receivable_description',
					'teves_receivable_table.receivable_amount',
					'teves_receivable_table.created_by_user_id',
					'billing_period_start',
					'billing_period_end',
					'company_header'
				]);
		
		$receivable_payment_data =  ReceivablesPaymentModel::where('teves_receivable_payment.receivable_idx', $request->receivable_id)
				->orderBy('receivable_payment_id', 'asc')
              	->get([
					'teves_receivable_payment.receivable_payment_id',
					'teves_receivable_payment.receivable_date_of_payment',
					'teves_receivable_payment.receivable_mode_of_payment',
					'teves_receivable_payment.receivable_reference',
					'teves_receivable_payment.receivable_payment_amount',
					]);
		
		$receivable_header = TevesBranchModel::find($receivable_data[0]['company_header'], ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);
		
		$receivable_amount_amt =  number_format($receivable_data[0]['receivable_amount'],2,".","");
		
		@$amount_split_whole_to_decimal = explode('.',$receivable_amount_amt);
		
		$amount_in_word_whole = $this->numberToWord($amount_split_whole_to_decimal[0]) ." Pesos";
		
		if(@$amount_split_whole_to_decimal[1]==0){
			$amount_in_word_decimal = "";
		}else{
			$amount_in_word_decimal = " and ".$this->numberToWord( $amount_split_whole_to_decimal[1] ) ." Centavos";
		}
		
		$amount_in_words = $amount_in_word_whole."".$amount_in_word_decimal;		
	
		
		/*USER INFO*/
		$user_data = User::where('user_id', '=', $receivable_data[0]['created_by_user_id'])->first();
		
		$title = 'STATEMENT OF ACCOUNT';
		  
        $pdf = PDF::loadView('printables.report_receivables_soa_pdf', compact('title', 'receivable_data', 'user_data', 'amount_in_words', 'receivable_payment_data','receivable_header'));
		
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('A4', 'landscape');/*Set to Landscape*/
		return $pdf->stream($receivable_data[0]['client_name']."_RECEIVABLE_SOA.pdf");
	}


}