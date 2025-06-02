<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>{{ $title }}</title>
<style>
  @page {
    size: legal;
    margin: 5mm;
  }

  body {
    margin: 0;
    padding: 0;
    background: white;
  }

  .page {
    width: 100%;
    height: 333mm; /* Legal height approx: 14in x 25.4 = 355.6mm; leave margin space */
    page-break-after: always;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    align-content: flex-start;
  }

  .payslip-wrapper {
    width: 48%;
    margin: 1%;
    border: 1px solid #000;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
    font-size: 10px;
  }

  table {
    border-collapse: collapse;
    width: 100%;
    font-size: 10px;
  }

  th, td {
    border: 1px solid #000;
    padding: 3px;
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
</style>
</head>
<body>

<?php
$no = 0;
$total = count($payrolls);

for ($i = 0; $i < $total; $i++) {
    // Open a new page container every 6 payslips
    if ($no % 6 == 0) {
        if ($no > 0) echo "</div>"; // close previous .page
        echo "<div class='page'>";
    }

    echo "<div class='payslip-wrapper'>";
    ?>
    <table>
      <tr><th colspan="2" style="text-align:center; background-color:pink;">PAYSLIP</th></tr>
      <tr><td><strong>NAME:</strong></td><td>{{ $payrolls[$i]->employee_full_name }}</td></tr>
      <tr><td><strong>POSITION:</strong></td><td>{{ $payrolls[$i]->employee_position }}</td></tr>
      <tr><td><strong>PERIOD COVERED:</strong></td><td>{{ $payrolls[$i]->period_covered }}</td></tr>
      <tr><td><strong>WORKING DAYS:</strong></td><td>{{ $payrolls[$i]->working_days }}</td></tr>
      <tr><th colspan="2">EARNINGS | DEDUCTIONS</th></tr>
      <tr>
        <td>
          <table>
            <tr><td style="background:yellowgreen;">Description</td><td style="background:yellowgreen;">Amount</td></tr>
            <tr><td>Basic Pay</td><td style="text-align:right;">{{ $payrolls[$i]->basic_pay }}</td></tr>
            <tr><td>Overtime</td><td style="text-align:right;">{{ $payrolls[$i]->over_time }}</td></tr>
            <tr><td>Day-off</td><td style="text-align:right;">{{ $payrolls[$i]->day_off }}</td></tr>
            <tr><td>Special Holiday</td><td style="text-align:right;">{{ $payrolls[$i]->special_holiday_pay }}</td></tr>
            <tr><td>Regular Holiday</td><td style="text-align:right;">{{ $payrolls[$i]->regular_holiday_pay }}</td></tr>
            <tr><td>Night Diff</td><td style="text-align:right;">{{ $payrolls[$i]->night_differential_pay }}</td></tr>
            <tr><td>Refund</td><td style="text-align:right;">{{ $payrolls[$i]->refund }}</td></tr>
            <tr><td>Cash Incentives</td><td style="text-align:right;">{{ $payrolls[$i]->cash_incentives }}</td></tr>
          </table>
        </td>
        <td>
          <table>
            <tr><td style="background:skyblue;">Description</td><td style="background:skyblue;">Amount</td></tr>
            <tr><td>SSS</td><td>{{ $payrolls[$i]->sss_deduction }}</td></tr>
            <tr><td>Philhealth</td><td>{{ $payrolls[$i]->philhealth_deduction }}</td></tr>
            <tr><td>Pag-ibig</td><td>{{ $payrolls[$i]->pagibig_deduction }}</td></tr>
            <tr><td>Accounts Receivable</td><td>{{ $payrolls[$i]->accounts_receivable_deduction }}</td></tr>
            <tr><td>Debit Memo</td><td>{{ $payrolls[$i]->debit_memo_deduction }}</td></tr>
            <tr><td>Prev. Pay</td><td>{{ $payrolls[$i]->prev_pay_deduction }}</td></tr>
            <tr><td>Groceries</td><td>{{ $payrolls[$i]->groceries_deduction }}</td></tr>
            <tr><td>Softdrinks</td><td>{{ $payrolls[$i]->softdrinks_deduction }}</td></tr>
          </table>
        </td>
      </tr>
      <tr>
        <th>Total Earnings:</th>
        <th>Total Deductions:</th>
      </tr>
      <tr>
        <th>NET PAY:</th>
        <th>0.00</th>
      </tr>
      <tr>
        <td style="border-bottom:0;">Received by:</td>
        <td style="border-bottom:0;">Date:</td>
      </tr>
      <tr>
        <td style="text-align:center; border-top:0;">_____________________<br>Signature</td>
        <td style="text-align:center; border-top:0;">_____________________<br>MM/DD/YYYY</td>
      </tr>
    </table>
    <?php
    echo "</div>"; // close payslip-wrapper
    $no++;
}

if ($no > 0) echo "</div>"; // Close last page
?>
</body>
</html>
