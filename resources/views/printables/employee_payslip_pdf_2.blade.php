<!DOCTYPE html>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
	<style>
    @page {
    /*size: A4;*/
    size: legal;
    margin: 1mm; /* Adjust margins as needed */
}

body {
    /*width: 210mm;  *//* A4 width */
  /*  height: 297mm; *//* A4 height */
    margin: 0;
    padding: ;
    background: white;
}


		table {
      border-collapse: collapse;
      width: 310px;
      font-family: Arial, sans-serif;
    }
    th, td {
      border: 1px solid #000;
      padding: 5px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    .center {
      text-align: center;
    }
    .bold {
      font-weight: bold;
    }
      .zoom-table {
    transform: scale(0.5); /* Adjust zoom level */
    transform-origin: top left; /* Set the zoom anchor */
  }
  .page-break { page-break-before: always; }
</style>

</head>
<body>
<?php

echo "<table border='1' class='zoom-table'>";

$no = 1;
for ($i = 0; $i < count($payrolls); $i++) {
    if ($i % 2 == 0) echo "<tr>"; // Start a new row every two payslips

    echo "<td>";
    ?>
    <table>
  
  <tr align="">
    <th colspan="2" style="text-align:center; background-color:pink;">PAYSLIP</th>
  </tr>
  
  <tr><td><strong>NAME:</strong></td><td>{{ $payrolls[$i]->employee_full_name }}</td></tr>
  <tr><td><strong>POSITION:</strong></td><td>{{ $payrolls[$i]->employee_position }}</td></tr>
  <tr><td><strong>PERIOD COVERED:</strong></td><td>{{ $payrolls[$i]->period_covered }}</td></tr>
  <tr><td><strong>TOTAL WORKING DAYS:</strong></td><td>{{ $payrolls[$i]->working_days }}</td></tr>
  
  <tr>
    <th colspan="2">&nbsp;</th>
  </tr>
  
  <tr>
    <th style="text-align:center; background-color:white;">EARNINGS</th>
    <th style="text-align:center; background-color:white;">DEDUCTION/S</th>
  </tr>
  <!--
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
  -->
  <tr>
    <td>
		<table>
			<tr><td style="text-align:center; background-color:yellowgreen;">Description</td><td style="text-align:center; background-color:yellowgreen;">Amount</td></tr>
			<tr><td>Basic Pay</td><td style="text-align:right;">{{ $payrolls[$i]->basic_pay }}</td></tr>
			<tr><td>Overtime & Duty</td><td style="text-align:right;">{{ $payrolls[$i]->over_time }}</td></tr>
			<tr><td>Day-off Pay</td><td style="text-align:right;">{{ $payrolls[$i]->day_off }}</td></tr>
			<tr><td>Special Holiday</td><td style="text-align:right;">{{ $payrolls[$i]->special_holiday_pay }}</td></tr>
			<tr><td>Regular Holiday</td><td style="text-align:right;">{{ $payrolls[$i]->regular_holiday_pay }}</td></tr>
			<tr><td>Night Differential</td><td style="text-align:right;">{{ $payrolls[$i]->night_differential_pay }}</td></tr>
			<tr><td>Refund</td><td style="text-align:right;">{{ $payrolls[$i]->refund }}</td></tr>
			<tr><td>Cash Incentives</td><td style="text-align:right;">{{ $payrolls[$i]->cash_incentives }}</td></tr>
			<tr><td colsapn='2'>&nbsp;</td style="text-align:right;"></tr>
		</table>
	</td>
    <td>
		<table>
			<tr><td style="text-align:center; background-color:skyblue;">Description</td><td style="text-align:center; background-color:skyblue;">Amount</td></tr>
			<tr><td>SSS Contribution</td><td>{{ $payrolls[$i]->sss_deduction }}</td></tr>
			<tr><td>Philhealth</td><td></td>{{ $payrolls[$i]->philhealth_deduction }}</tr>
			<tr><td>Pag-ibig</td><td>{{ $payrolls[$i]->pagibig_deduction }}</td></tr>
			<tr><td>Accounts Receivable</td><td>{{ $payrolls[$i]->accounts_receivable_deduction }}</td></tr>
			<tr><td>Debit Memo</td><td>{{ $payrolls[$i]->debit_memo_deduction }}</td></tr>
			<tr><td>Late</td><td>-</td></tr>
			<tr><td>Prev. Pay</td><td>{{ $payrolls[$i]->prev_pay_deduction }}</td></tr>
			<tr><td>Groceries</td><td>{{ $payrolls[$i]->groceries_deduction }}</td></tr>
			<tr><td>Softdrinks</td><td>{{ $payrolls[$i]->softdrinks_deduction }}</td></tr>
		</table>
	</td>
  </tr>
  <tr>
    <th style="text-align:left; background-color:white;">TOTAL EARNINGS:</th>
    <th style="text-align:left; background-color:white;">TOTAL DEDUCTIONS:</th>
  </tr>
  <tr>
    <th style="text-align:left; background-color:white;">NET PAY:</th>
    <th style="text-align:left; background-color:white;">0.00</th>
  </tr>
  <tr>
    <td style="text-align:left; background-color:white; border-bottom:0px;">Received by:</td>
	<td style="text-align:left; background-color:white; border-bottom:0px solid;">Date:</td>
  </tr>
  <tr>
    <td style="text-align:center; background-color:white; border-top:0px;">___________________________________<br>Signature over Printed Name</td>
	<td style="text-align:center; background-color:white; border-bottom:1px solid; border-top:0px solid;">___________________________________<br>MM/DD/YYYY</td>
  </tr>
</table>
    <?php
    echo "</td>";

    if ($i % 2 == 1) echo "</tr>"; // Close the row after two payslips

    // Insert a page break every 4 payslips
    if ($no % 6 == 0 && ($i + 1) < count($payrolls)) {
        echo "</table><div class='page-break'>ddddddd</div><table border='1' class='zoom-table'>";
    }

    $no++;
}

if (count($payrolls) % 2 != 0) echo "<td>empty</td></tr>"; // Add an empty cell if the total is odd
echo "</table>";
?>
		
</body>
</html>
