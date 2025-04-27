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
	
		
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td align="center" style="border:1px solid; padding-top:5px; padding-bottom:5px;" width="5%">Item</td>
			<th style="border:1px solid;" width="15%">Name</th>
			<th align="center" style="border:1px solid;">Daily Rate</th>
			<th align="center" style="border:1px solid;">No. Days Work</th>
			<th align="center" style="border:1px solid;">No. Days Leave</th>
			<th align="center" style="border:1px solid;">Basic Pay</th>
			<th align="center" style="border:1px solid;">OT Pay</th>
			<th align="center" style="border:1px solid; padding-top:5px; padding-bottom:5px;">Night Differential Pay</th>
			<th align="center" style="border:1px solid;">Day-off Pay</th>
			<th align="center" style="border:1px solid;">Holiday Pay</th>
			<th style="border:1px solid;">Gross Salary</th>
			<th style="border:1px solid;">Allowance</th>
			<th style="border:1px solid;">Deduction</th>
			<th style="border:1px solid;">Net Salary</th>
		</tr>
		
		
		<?php 
			$no = 1;
			$grand_total_net_salary = 0;
			?>
		@foreach ($result as $result_data_cols)
			<?php
			
				$daily_rate = $result_data_cols['daily_rate'];
				
			?>
		<tr style="font-size:12px;">
			<td align="center" style="border:1px solid;"><?=$no;?></td>
			<td align="left" style="border:1px solid; padding-top:5px; padding-bottom:5px;">{{ $result_data_cols['employee_full_name'] }}</td>
			<td align="right" style="border:1px solid;"><?=number_format($daily_rate,2);?></td>		
			<td align="center" style="border:1px solid;"><?=number_format($result_data_cols['count_days'],0);?></td>		
			<td align="center" style="border:1px solid;"><?=number_format($result_data_cols['leave_logs_count'],0);?></td>		
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['basic_pay_total'],2);?></td>		
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['regular_overtime_pay_total'],2);?></td>	
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['night_differential_pay_total'],2);?></td>	
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['regular_holiday_pay_total']+$result_data_cols['special_holiday_pay_total'],2);?></td>	
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['day_off_pay_total'],2);?></td>	
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['gross_salary'],2);?></td>				
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['allowance_amount_total'],2);?></td>			
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['deduction_amount_total'],2);?></td>		
			<td align="right" style="border:1px solid;"><?=number_format($result_data_cols['net_salary'],2);?></td>			
		</tr>
		
			<?php
				$grand_total_net_salary += $result_data_cols['net_salary'];
				$no++; 
			?>
			
		@endforeach
		
		<tr style="font-size:12px;">
			<td align="center" colspan="11"></td>
			<td align="right">Total:</td>
			<td align="right"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span></td>
			<td align="right" style="border-bottom: 3px double #000000">
			<?=$grand_total_net_salary;?></td>
		</tr>
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>
		
		<tr>
			<td colspan="9" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="9" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="9" style="height:5.66px !important;"></td>
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