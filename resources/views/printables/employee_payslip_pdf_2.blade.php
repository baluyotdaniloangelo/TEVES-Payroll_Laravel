<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        @page {
            size: legal portrait;
            /*margin: 10mm;*/
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        .payslip-container {
            width: 100%;
            text-align: center;
        }

        .payslip {
            width: 47%;
            display: inline-block;
            vertical-align: top;
            /*margin: 1%;*/
            border: 1px solid #000;
            padding: 5px;
            box-sizing: border-box;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        th, td {
            border: 1px solid #000;
            padding: 3px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .section-header {
            background-color: #ccc;
            text-align: center;
            font-weight: bold;
        }

        .page-break {
            page-break-after: always;
            clear: both;
        }
    </style>
</head>
<body>
<?php
$no = 0;
$total = count($payrolls);

for ($i = 0; $i < $total; $i++) {
    if ($no % 6 == 0) {
        echo '<div class="payslip-container">';
    }
?>
    <div class="payslip">
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
    </div>
<?php
    $no++;

    if ($no % 6 == 0 || $i == $total - 1) {
        echo '</div>';
        if ($i < $total - 1) echo '<div class="page-break"></div>';
    }
}
?>
</body>
</html>
