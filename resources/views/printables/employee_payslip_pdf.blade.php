<!DOCTYPE html>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
	<style>
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
</style>

</head>
<body>
    
	<?php
    $data = ['Apple', 'Banana', 'Cherry', 'Date', 'Elderberry', 'Fig', 'Grape', 'Honeydew','newfruit']; // Example data

echo "<table border='1' class='zoom-table'>";
$no = 1;
for ($i = 0; $i < count($payrolls); $i++) {
    if ($i % 2 == 0) echo "<tr>"; // Start a new row every two items
    echo "<td>";
    ?>
    <table>
  
  <tr align="">
    <th colspan="2" style="text-align:center; background-color:pink;">PAYSLIP</th>
  </tr>
  
  <tr><td><strong>NAME:</strong></td><td>{{ $payrolls[$i]->employee_full_name }}</td></tr>
  <tr><td><strong>POSITION:</strong></td><td>Forecourt Attendant</td></tr>
  <tr><td><strong>PERIOD COVERED:</strong></td><td>May 3–9, 2025</td></tr>
  <tr><td><strong>TOTAL WORKING DAYS:</strong></td><td>0</td></tr>
  
  <tr>
    <th colspan="2">&nbsp;</th>
  </tr>
  
  <tr>
    <th style="text-align:center; background-color:white;">EARNINGS</th>
    <th style="text-align:center; background-color:white;">DEDUCTION/S</th>
  </tr>
  
  <tr>
    <td>
		<table>
			<tr><td style="text-align:center; background-color:yellowgreen;">Description</td><td style="text-align:center; background-color:yellowgreen;">Amount</td></tr>
			<tr><td>Basic Pay</td><td>0.00</td></tr>
			<tr><td>Overtime & Duty</td><td></td></tr>
			<tr><td>Day-off Pay</td><td></td></tr>
			<tr><td>Special Holiday</td><td></td></tr>
			<tr><td>Regular Holiday</td><td></td></tr>
			<tr><td>Night Differential</td><td>-</td></tr>
			<tr><td>Refund</td><td></td></tr>
			<tr><td>Cash Incentives</td><td>(500.00)</td></tr>
			<tr><td>Cash Incentives</td><td>(500.00)</td></tr>
		</table>
	</td>
    <td>
		<table>
			<tr><td style="text-align:center; background-color:skyblue;">Description</td><td style="text-align:center; background-color:skyblue;">Amount</td></tr>
			<tr><td>SSS Contribution</td><td></td></tr>
			<tr><td>Philhealth</td><td></td></tr>
			<tr><td>Pag-ibig</td><td></td></tr>
			<tr><td>Accounts Receivable</td><td>(109.20)</td></tr>
			<tr><td>Debit Memo</td><td></td></tr>
			<tr><td>Late</td><td></td></tr>
			<tr><td>Prev. Pay</td><td>(73.21)</td></tr>
			<tr><td>Groceries</td><td></td></tr>
			<tr><td>Softdrinks</td><td></td></tr>
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
    if ($i % 2 == 1) echo "</tr>"; // Close the row after two items


    // Insert a page break every 4 payslips
    if ($no % 4 == 0 && ($i + 1) < count($payrolls)) {
        echo "</table><div class='page-break'></div><table border='1' class='zoom-table'>";
    }
}
if (count($payrolls) % 2 != 0) echo "<td>SSSSS</td></tr>"; // If odd number of items, add an empty cell
echo "</table>";


    ?>
		
</body>
</html>
