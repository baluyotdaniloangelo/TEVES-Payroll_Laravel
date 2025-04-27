<!DOCTYPE html>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
	<style>
		body {
			font-family: "Open Sans", sans-serif;
		}
		.data_thead {
			background-color: #000000;
			color: #fff;
		}
		.data_th {
			padding: 5px;
			font-size: 12px;
		}
		.data_tr {
			padding: 5px;
		} 	
		.td_colon:before{
			content:":";
			font-weight:bold;
			text-align:center;
			color:black;
			position:relative;
			left:-30px;
		}
</style>
</head>
<body>
    
	<table class="" width="100%" cellspacing="0" cellpadding="1">

			<?php			
				$billing_date = strtoupper(date("M/d/Y"));
				$logo = $branch_information['branch_logo'];
			?>
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%">
			<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:112px;">
			</td>
			<td colspan="6" width="30%" style="horizontal-align:center;text-align:left;"><b style="font-size:18px;"><?=$branch_information['branch_name'];?></b></td>
			<td colspan="3" nowrap align="center" width="60%" style="font-size:12px; background-color: skyblue; text-align:center; font-weight:bold; color:#000; border-top-left-radius:30px;border-bottom-left-radius:30px; width:50px"><b>{{ $title }}</b></td>
		</tr>
		
		<tr>
			<td colspan="4"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:12px;"><?=$branch_information['branch_address'];?></div>
			</td>		
			<td colspan="2" align="left" width="20%" style="font-size:12px; font-weight:bold;;"><b>DATE</b></td>
			<td colspan="3" align="left" width="30%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon"><?=$billing_date;?></td>
		</tr>		
		<?php
				
				$_po_start_date=date_create("$start_date");
				$po_start_date = strtoupper(date_format($_po_start_date,"M/d/Y"));
				
				$_po_end_date=date_create("$end_date");
				$po_end_date = strtoupper(date_format($_po_end_date,"M/d/Y"));	
		?>
		<tr>
			<td colspan="4"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:12px;">VAT REG. TIN : <?=$branch_information['branch_tin'];?></div>
			</td>
			<td colspan="2" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>PERIOD</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon"><?php echo "$po_start_date - $po_end_date"; ?></td>
		</tr>

		<tr>
			<td colspan="4"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:12px;"><?=$branch_information['branch_owner'];?> - <?=$branch_information['branch_owner_title'];?></div>
			</td>
			<td colspan="2" align="left" width="25%" style=""><b></b></td>
			<td colspan="3" align="left" width="25%" style="" class=""></td>
		</tr>
		
		<tr>
			<td colspan="5"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="25%" style=""></td>
			<td colspan="3" align="left" width="25%" style=""></td>
		</tr>
		
		<tr>
			<td colspan="5"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="25%" style=""></td>
			<td colspan="3" align="left" width="25%" style=""></td>
		</tr>
		

		<tr>
			<td colspan="10"  width="50%" style="horizontal-align:center;text-align:left;"></td>
		</tr>
		

		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" style="table-layout:fixed;">
	
		
		<tr>
			<td colspan="15" style="border-left:0px solid #000;border-right:0px solid #000;border-bottom:0px solid #000;">&nbsp;</td>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="15" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:10px !important; "></td>
		</tr>
		<!--
		<th class="all">#</th>
																		<th class="" title="Employee Number">Employee Number</th>
																		<th class="all" title="Employee Name">Employee Name</th>
																		<th class="" title="Employee Status">Employment Status</th>
																		<th class="all">Daily Rate</th>
																		<th class="">Number of Days Work</th>
																		<th class="">Number of Days Leave</th>
																		<th class="all">Basic Pay</th>
																		<th class="all">Night Differential Pay</th>
																		<th class="all">OT Pay</th>
																		<th class="all">Day Off Pay</th>
																		<th class="all">Special Holiday</th>
																		<th class="all">Regular Holiday</th>
																		<th class="all">Allowance</th>
																		<th class="all">Deduction</th>
																		<th class="all">Gross Salary</th>
																		<th class="all">Net Salary</th>
																		-->
		<tr style="font-size:12px;border:0 solid #000;">
			<td align="center" style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;" width="2%">Item</td>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Employee No.</th>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Name</th>
			<th align="left" style="border-right:1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">Daily Rate</th>
			<th align="right" style="border-right:1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">No. Days Work</th>
			<th align="right" style="border-right:1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">No. Days Leave</th>
			<th align="right" style="border-right1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">Basic Pay</th>
			<th align="right" style="border-right:1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">Night Differential Pay</th>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">OT Pay</th>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Special Holiday</th>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Regular Holiday</th>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Allowance</th>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Deduction</th>
			<th style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Gross Salary</th>
			<th style="  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Net Salary</th>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="15" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:10px !important; "></td>
		</tr>
		<?php 
			$no = 1;
			//$total_gross_amount = 0;
			//$total_withholding_tax = 0;
			//$total_net_amount = 0;
			//$total_amount_due = 0;
			var_dump($result);
			?>
		@foreach ($result as $result_data_cols)
			<?php
			
				//$_purchase_order_date=date_create("$result_data_cols->purchase_order_date");
				//$purchase_order_date = strtoupper(date_format($_purchase_order_date,"M/d/Y"));
				//$purchase_order_less_percentage = $result_data_cols['purchase_order_net_amount'] * $result_data_cols['purchase_order_less_percentage']/100;
				
				//$total_gross_amount 	+= $result_data_cols['purchase_order_gross_amount'];
				//$total_withholding_tax 	+= $purchase_order_less_percentage;
				//$total_net_amount 		+= $result_data_cols['purchase_order_net_amount'];
				//$total_amount_due 		+= $result_data_cols['purchase_order_total_payable'];
				/*
				
					$result[] = array(
					 'employee_number' 					=> $employee_number,
					 'employee_full_name'				=> $employee_full_name,
					 'daily_rate'						=> $daily_rate,
					 'employment_type'					=> $employment_type,
					 'basic_pay_total'					=> $basic_pay_total,
					 'regular_overtime_pay_total'		=> $regular_overtime_pay_total,
					 'day_off_pay_total'				=> $day_off_pay_total,
					 'night_differential_pay_total'		=> $night_differential_pay_total,
					 'regular_holiday_pay_total' 		=> $regular_holiday_pay_total,
					 'special_holiday_pay_total'		=> $special_holiday_pay_total,
					 'count_days'						=> $count_days,
					 'deduction_amount_total'			=> $deduction_amount_total,
					 'allowance_amount_total'			=> $allowance_amount_total,
					 'leave_logs_count'					=> $leave_logs_count,					
					 'leave_amount_pay_total'			=> $leave_amount_pay_total,
					 'gross_salary'						=> $gross_salary,
					 'net_salary'						=> $net_salary
					 );
					 
				*/
				$daily_rate = $result_data_cols['daily_rate'];
				
			?>
		<tr style="font-size:12px;">
			
			<td align="center" style="border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;"><?=$no;?></td>
			<td align="left" style="border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;">{{ $result_data_cols['employee_number'] }}</td>
			<td align="left" style="border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;">{{ $result_data_cols['employee_full_name'] }}</td>
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($daily_rate,2);?></td>		
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['count_days'],0);?></td>		
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['leave_logs_count'],0);?></td>		
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['basic_pay_total'],2);?></td>					
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['regular_overtime_pay_total'],2);?></td>				
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['special_holiday_pay_total'],2);?></td>				
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['regular_holiday_pay_total'],2);?></td>		
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['allowance_amount_total'],2);?></td>			
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['deduction_amount_total'],2);?></td>			
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['gross_salary'],2);?></td>					
			<td align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($result_data_cols['net_salary'],2);?></td>			
			
		</tr>
		
			<?php
				$no++; 
			?>
			
		@endforeach
		
		
		<tr>
			<td colspan="15" style="height:5.66px !important;"></td>
		</tr>
		
		<tr>
			<td colspan="15" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="15" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="15" style="height:5.66px !important;"></td>
		</tr>	
		</table>
		<table>
		<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2">PREPARED BY:</td>
				<td align="center" colspan="3" style=""></td>
				<td align="left" colspan="6"></td>
		</tr>
		
		<tr>
			<td colspan="11" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="11" style="height:5.66px !important;"></td>
		</tr>
		
		<tr>
			<td colspan="11" style="height:5.66px !important;"></td>
		</tr>
		
		<tr class="data_tr" style="font-size:12px;">
				
				<td align="center" colspan="3" style="border-bottom:1px solid #000;">{{$user_data->user_real_name}}</td>
				<td align="left" colspan="6"></td>
				<td align="left" colspan="2"></td>
		</tr>
		
		<tr class="data_tr" style="font-size:12px;">
				
				<td align="center" colspan="3" style=" ">{{$user_data->user_job_title}}</td>
				<td align="left" colspan="6"></td>
				<td align="left" colspan="2"></td>
		</tr>
		
		<tr>
			<td colspan="11" style="height:5.66px !important;"></td>
		</tr>	
	
		<tr>
			<td colspan="11" style="height:5.66px !important;"></td>
		</tr>
		
		
		</table>
		
</body>
</html>