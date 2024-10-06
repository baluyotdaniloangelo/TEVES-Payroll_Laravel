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
				
				$logo = $receivable_header['branch_logo'];
			?>
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%">
			<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:112px;">
			</td>
			<td colspan="6" width="30%" style="horizontal-align:center;text-align:left;"><b style="font-size:18px;"><?=$receivable_header['branch_name'];?></b></td>
			<td colspan="3" nowrap align="center" width="60%" style="font-size:12px; background-color: skyblue; text-align:center; font-weight:bold; color:#000; border-top-left-radius:30px;border-bottom-left-radius:30px; width:50px"><b>{{ $title }}</b></td>
		</tr>
		
		<tr>
			<td colspan="4"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:12px;"><?=$receivable_header['branch_address'];?></div>
			</td>		
			<td colspan="2" align="left" width="20%" style="font-size:12px; font-weight:bold;;"><b>BILLING DATE</b></td>
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
			<div style="font-size:12px;">VAT REG. TIN : <?=$receivable_header['branch_tin'];?></div>
			</td>
			<td colspan="2" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>PERIOD</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon"><?php echo "$po_start_date - $po_end_date"; ?></td>
		</tr>

		<tr>
			<td colspan="4"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:12px;"><?=$receivable_header['branch_owner'];?> - <?=$receivable_header['branch_owner_title'];?></div>
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
		<br>
		<table class="" width="100%" cellspacing="0" cellpadding="1" >	
		<?php
				$_billing_date=date_create($receivable_data[0]['billing_date']);
				$billing_date = strtoupper(date_format($_billing_date,"M/d/Y"));
		?>
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="10%"><b>ACCOUNT NAME</b></td>	
			<td colspan="11" align="left" width="90%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $client_data['client_name'] }}</td>		
		</tr>
		<?php
				$_print_date=date_create(date('Y-m-d'));
				$print_date = strtoupper(date_format($_print_date,"M/d/Y"));
		?>
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="10%"><b>TIN</b></td>	
			<td colspan="11" align="left" width="90%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $client_data['client_tin'] }}</td>
		</tr>
		
		<tr style="font-size:12px;">		
			<td colspan="1" align="left" width="20%"><b>ADDRESS</b></td>	
			<td colspan="11" align="left" width="80%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $client_data['client_address'] }}</td>			
		</tr>

		<tr style="font-size:12px;">
			<td colspan="12" style="height:5.66px !important;"></td>
		</tr>
		
		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" style="table-layout:fixed;">
	
		
		<tr>
			<td colspan="12" style="border-left:0px solid #000;border-right:0px solid #000;border-bottom:0px solid #000;">&nbsp;</td>
		</tr>>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="14" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:10px !important; "></td>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="1" align="center" style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">#</td>		
			<td colspan="2" align="left" style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Transaction Date</td>		
			<td colspan="2" align="left" style="border-right:1px solid skyblue;  background-color: #c6e0b4; font-weight:bold; height:25px !important; padding:10px;">Reference No.</td>		
			<td colspan="3" nowrap align="left" style="border-right:1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">Description</td>
			<td colspan="2" nowrap align="right" style="border-right:1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">Amount</td>
			<td colspan="2" nowrap align="right" style="border-right:1px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">Remaining Balance</td>
			<td colspan="2" nowrap align="right" style="border:0px solid skyblue; background-color: #c6e0b4; font-weight:bold; padding:10px;">Current Balance</td>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="14" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:10px !important; "></td>
		</tr>
		<?php 
			$no = 1;
			$total_payment = 0;
			$current_balance = 0;
			?>
		@foreach ($receivable_data as $receivable_data_data_cols)
			<?php
			
			
				$_billing_date=date_create("$receivable_data_data_cols->billing_date");
				$billing_date = strtoupper(date_format($_billing_date,"M/d/Y"));
				
				$current_balance += $receivable_data_data_cols->receivable_remaining_balance;	
				
			?>
		<tr style="font-size:12px;">
			
			<td colspan="1" align="center" style="border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;"><?=$no;?></td>
			<td colspan="2" align="left" style="border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;"><?=$billing_date;?></td>
			<td colspan="2" align="left" style="border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;">{{ $receivable_data_data_cols['control_number'] }}</td>
			<td colspan="3" align="left" style="border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;">{{ $receivable_data_data_cols['receivable_description'] }}</td>
			<td colspan="2" align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($receivable_data_data_cols['receivable_amount'],2);?></td>	
			<td colspan="2" align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($receivable_data_data_cols['receivable_remaining_balance'],2);?></td>			
		<td colspan="2" align="right" style="border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($current_balance,2);?></td>			
		
		</tr>
		
			<?php
				$no++; 
			?>
			
		@endforeach
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>	
		
		
		<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2">PREPARED BY:</td>
				<td align="center" colspan="3" style=""></td>
				<td align="left" colspan="5"></td>
		</tr>
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>	
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>
		
		<tr class="data_tr" style="font-size:12px;">
				
				<td align="center" colspan="3" style="border-bottom:1px solid #000;">{{$user_data->user_real_name}}</td>
				<td align="left" colspan="6"></td>
				<td align="left" colspan="5"></td>
		</tr>
		
		<tr class="data_tr" style="font-size:12px;">
				
				<td align="center" colspan="3" style=" ">{{$user_data->user_job_title}}</td>
				<td align="left" colspan="6"></td>
				<td align="left" colspan="5"></td>
		</tr>
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>	
	
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>
		<tr style="font-size:12px;border:0px solid #000;font-style: italic;">
			<td colspan="14" align="left"style="border:0px solid #000; height:25px !important;">
			PLEASE ADVISE OUR OFFICE FOR ANY DISCREPANCIES WITHIN 7 DAYS, IF NONE, WE WILL CONSIDER STATED ABOVE IS TRUE AND CORRECT.</br>
			</td>
		</tr>
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>
		
		<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="3">RECEIVED AND ACKNOWLEDGE BY:</td>
				<td align="center" colspan="3" style=""></td>
				<td align="left" colspan="8"></td>
		</tr>
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>
		
		<tr>
			<td colspan="14" style="height:5.66px !important;"></td>
		</tr>		
		
		<tr class="data_tr" style="font-size:12px;">
				
				<td align="center" colspan="3" style="border-bottom:1px solid #000;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td align="left" colspan="6"></td>
				<td align="left" colspan="5"></td>
		</tr>
		
		</table>
		
</body>
</html>