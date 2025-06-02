<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BranchModel;
use App\Models\EmployeeModel;
use App\Models\EmployeeLogsModel;
use App\Models\HolidayModel;
use App\Models\EmployeeDeductionLogsModel;
use App\Models\EmployeeAllowanceLogsModel;
use App\Models\EmployeeLeaveLogsModel;
use App\Models\CutOffModel;
use App\Models\EmployeesSalaryPerCutOffModel;
use App\Models\CompanyDetailsModel;

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

	public function print_payslip(Request $request){
		
		$cutoff_idx	  = $request->cutoff_idx;
         \DB::statement("SET SQL_MODE=''");
        $payrolls = \DB::table('teves_payroll_employee_salary_per_cut_off as employee_salary_tb')
            ->join('teves_payroll_employee_table as employee_info_tb', 'employee_info_tb.employee_id', '=', 'employee_salary_tb.employee_idx')
            ->join('teves_payroll_cutoff_table as teves_payroll_cutoff_table', 'teves_payroll_cutoff_table.cutoff_id', '=', 'employee_salary_tb.cutoff_idx')
            ->where('teves_payroll_cutoff_table.cutoff_id', $cutoff_idx)
            ->select([
                'employee_info_tb.employee_id',
                'employee_info_tb.employee_full_name',
                'employee_info_tb.employee_position',
                \DB::raw("CONCAT(teves_payroll_cutoff_table.cut_off_period_start, ' to ', teves_payroll_cutoff_table.cut_off_period_end) AS period_covered"),
                \DB::raw("(employee_salary_tb.count_days + employee_salary_tb.leave_logs_count) AS working_days"),
                'employee_salary_tb.basic_pay_total AS basic_pay',
                'employee_salary_tb.regular_overtime_pay_total AS over_time',
                'employee_salary_tb.day_off_pay_total AS day_off',
                'employee_salary_tb.night_differential_pay_total AS night_differential_pay',
                'employee_salary_tb.regular_holiday_pay_total AS regular_holiday_pay',
                'employee_salary_tb.special_holiday_pay_total AS special_holiday_pay',
                \DB::raw("(SELECT SUM(allowance_amount) FROM teves_payroll_employee_allowance_logs WHERE allowance_type = 'Cash_Incentives' AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS cash_incentives"),
                \DB::raw("(SELECT SUM(allowance_amount) FROM teves_payroll_employee_allowance_logs WHERE allowance_type = 'Refund' AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS refund"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 1 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS sss_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 2 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS pagibig_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 3 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS philhealth_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 10 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS accounts_receivable_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 11 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS debit_memo_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 17 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS cash_advance_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 18 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS prev_pay_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 19 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS groceries_deduction"),
                \DB::raw("(SELECT SUM(deduction_amount) FROM teves_payroll_employee_deduction_logs WHERE deduction_idx = 20 AND employee_idx = employee_info_tb.employee_id AND cutoff_idx = teves_payroll_cutoff_table.cutoff_id) AS softdrinks_deduction"),
            ])
            ->groupBy('employee_info_tb.employee_id', 'teves_payroll_cutoff_table.cutoff_id')
            ->get();
					
        $cut_off_information = CutOffModel::query()
            ->leftJoin('teves_payroll_user_table as prepared_by', 'teves_payroll_cutoff_table.created_by_user_idx', '=', 'prepared_by.user_id')
            ->leftJoin('teves_payroll_user_table as reviewed_by', 'teves_payroll_cutoff_table.reviewed_by_user_idx', '=', 'reviewed_by.user_id')
            ->leftJoin('teves_payroll_user_table as approved_by', 'teves_payroll_cutoff_table.approved_by_user_idx', '=', 'approved_by.user_id')
            ->leftJoin('teves_branch_table as branch_details', 'teves_payroll_cutoff_table.branch_idx', '=', 'branch_details.branch_id')
            ->where('teves_payroll_cutoff_table.cutoff_id', $cutoff_idx)
            ->select(
                'teves_payroll_cutoff_table.*',
                'prepared_by.user_real_name as prepared_by_name',
                'prepared_by.user_job_title as prepared_by_position',
                'reviewed_by.user_real_name as reviewed_by_name',
                'reviewed_by.user_job_title as reviewed_by_position',
                'approved_by.user_real_name as approved_by_name',
                'approved_by.user_job_title as approved_by_position',
                'branch_details.branch_code as branch_code'
            )
           ->get();

        $branchID	= $cut_off_information[0]->branch_idx;
		$start_date	= $cut_off_information[0]->cut_off_period_start;
		$end_date	= $cut_off_information[0]->cut_off_period_end;

		$prepared_by_name	    = $cut_off_information[0]->prepared_by_name;
        $prepared_by_position	= $cut_off_information[0]->prepared_by_position;
		$reviewed_by_name	    = $cut_off_information[0]->reviewed_by_name;
		$reviewed_by_position	= $cut_off_information[0]->reviewed_by_position;
		$approved_by_name	    = $cut_off_information[0]->approved_by_name;
		$approved_by_position	= $cut_off_information[0]->approved_by_position;
		$branch_code	        = $cut_off_information[0]->branch_code;
		
		$company_information = CompanyDetailsModel::find(1, ['sss_number','pagibig_number','philhealth']);
		$branch_information = BranchModel::find($branchID, ['branch_code','branch_name','branch_tin','branch_address','branch_contact_number','branch_owner','branch_owner_title','branch_logo']);

		/*USER INFO*/
		$user_data = User::where('user_id', '=', Session::get('loginID'))->first();

		$title = "Employee's Weekly Payroll";
        //$pdf = PDF::loadView('printables.employee_payslip_pdf_2', compact('title', 'user_data', 'branch_information','start_date','end_date','prepared_by_name','prepared_by_position','reviewed_by_name','reviewed_by_position','approved_by_name','approved_by_position', 'company_information', 'cutoff_idx', 'payrolls'));
		/*Download Directly*/
        //return $pdf->download($client_data['client_name'].".pdf");
		/*Stream for Saving/Printing*/
		//$pdf->setPaper('legal', 'portrait');/*Set to Landscape*/
		//return $pdf->stream($branch_information['branch_code']."_Payslip.pdf");	
		return view("printables.employee_payslip_pdf_2", compact('title', 'user_data', 'branch_information','start_date','end_date','prepared_by_name','prepared_by_position','reviewed_by_name','reviewed_by_position','approved_by_name','approved_by_position', 'company_information', 'cutoff_idx', 'payrolls'));
	}


}
