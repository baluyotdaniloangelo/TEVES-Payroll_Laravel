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
			font-size: 10px;
			color:#000;
			background-color:#c6e0b4;
		}
		.data_tr {
			padding: 5px;
			font-size: 10px;
		}
	</style>
	
</head>
<body>
    
	<table class="" width="100%" cellspacing="0" cellpadding="1" >
			<?php
				$logo = $receivable_header['branch_logo'];
			?>
			<tr>
			<td rowspan="4" align="right" colspan="5">
			<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:160px;">
			</td>
			<td style="font-size:20px; font-weight:bold;" align="center" colspan="2"><?=$receivable_header['branch_name'];?></td>
		</tr>
		
		<tr>
			<td  style="font-size:12px;" align="center" colspan="2"><?=$receivable_header['branch_address'];?></td>
		</tr>
		
		<tr>
			<td style="font-size:12px;" nowrap align="center" colspan="2">VAT REG. TIN : <?=$receivable_header['branch_tin'];?></td>
		</tr>
		<tr>
			<td style="font-size:12px;" nowrap align="center" colspan="2"><?=$receivable_header['branch_owner'];?> - <?=$receivable_header['branch_owner_title'];?></td>
		</tr>

		<tr>
			<td colspan="10"><div align="center"><h5>{{ $title }}</h5></div></td>
		</tr>
		<tr style="font-size:12px;">
			<td colspan="1" align="left">ACCOUNT NAME:</td>
			<td colspan="5" align="left" style="border-bottom:1px solid #000;">{{ $client_data['client_name'] }}</td>			
			<td colspan="1" align="left">DATE PRINTED:</td>
			<td colspan="3" align="left" style="border-bottom:1px solid #000;"><?php echo strtoupper(date('M/d/Y')); ?></td>
			
		</tr>
		<tr style="font-size:12px;">
			<td colspan="1" align="left">TIN:</td>
			<td colspan="5" align="left" style="border-bottom:1px solid #000;">{{ $client_data['client_tin'] }}</td>			
			<td colspan="1" nowrap align="left">SALES ORDER PERIOD:</td>
			<?php
			
			$_po_start_date=date_create("$start_date");
			$po_start_date = strtoupper(date_format($_po_start_date,"M/d/Y"));
			
			$_po_end_date=date_create("$end_date");
			$po_end_date = strtoupper(date_format($_po_end_date,"M/d/Y"));
			?>
			<td colspan="3" nowrap align="left" style="border-bottom:1px solid #000;"><?=$po_start_date;?> - <?=$po_end_date;?></td>			
			
		</tr>
		<tr style="font-size:12px;">
			<td colspan="1" align="left">ADDRESS:</td>
			<td colspan="5" align="left" style="border-bottom:1px solid #000;">{{ $client_data['client_address'] }}</td>
			<td colspan="4" align="left"></td>
		</tr>
		
		<tr style="font-size:12px;">
			<td colspan="10">&nbsp;</td>
		</tr>
		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" >
		
			<tr>
				<th class="data_th" style="border:1px solid #000;">#</th>
				<th class="data_th" nowrap style="border:1px solid #000;">Date</th>
				<th class="data_th" nowrap style="border:1px solid #000;">Time</th>
				<th class="data_th" nowrap style="border:1px solid #000;">Driver's Name</th>
				<th class="data_th" nowrap style="border:1px solid #000;">S.O. No.</th>
				<th class="data_th" nowrap style="border:1px solid #000;" width="15%">Description</th>
				<th class="data_th" nowrap style="border:1px solid #000;">Product</th>
				<th class="data_th" nowrap style="border:1px solid #000;">Quantity</th>
				<th class="data_th" nowrap style="border:1px solid #000;" width="15%">Price</th>
				<th class="data_th" nowrap style="border:1px solid #000;" width="15%">Amount</th>
			</tr>
											
		<tbody>
			<?php 
			$no = 1;
			$total_payable = 0;
			$total_liters = 0;
			?>
			@foreach ($billing_data as $billing_data_cols)
			<?php
			$_order_date=date_create("$billing_data_cols->order_date");
			$order_date = strtoupper(date_format($_order_date,"M/d/Y"));
			?>
			<tr class="data_tr" >
				<td align="center" nowrap style="border:1px solid #000;"><?=$no;?></td>
				<td align="center" nowrap style="border:1px solid #000;"><?=$order_date;?></td>
				<td align="center" nowrap style="border:1px solid #000;">{{$billing_data_cols->order_time}}</td>
				<td nowrap style="border:1px solid #000;">{{$billing_data_cols->drivers_name}}</td>
				<td align="center" nowrap style="border:1px solid #000;">{{$billing_data_cols->order_po_number}}</td>
				<td align="center" nowrap style="border:1px solid #000;">{{$billing_data_cols->plate_no}}</td>
				<td nowrap style="border:1px solid #000;">{{$billing_data_cols->product_name}}</td>
				<td align="center" nowrap style="border:1px solid #000;"><?=number_format($billing_data_cols->order_quantity,2)?> {{$billing_data_cols->product_unit_measurement}}</td>
				<td align="center" nowrap style="border:1px solid #000;"><?=number_format($billing_data_cols->product_price,2);?></td>
				<td align="center" nowrap style="border:1px solid #000;"><?=number_format($billing_data_cols->order_total_amount,2);?></td>
			</tr>
			<?php 
			$no++; 
			
			if($billing_data_cols->product_unit_measurement=='L'){
				$total_liters += $billing_data_cols->order_quantity;
			}else{
				$total_liters += 0;
			}
			
			$total_payable += $billing_data_cols->order_total_amount;
			?>
			
			@endforeach
	
											<tr class="data_tr">
												<td align="left" colspan="6"></td>
												<td align="left">Total Volume:</td>
												<td align="left"><?=number_format($total_liters,2);?> L</td>
												<td align="left"><b>Total Sales:</b></td>
												<td align="right" ><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span>  <?=number_format($total_payable,2);?></td>
											</tr>
											<!--'withholding_tax_percentage','net_value_percentage','vat_value_percentage'-->
											<tr class="data_tr">
												<td align="left" colspan="6"></td>
												<td align="left" colspan="1">Discount per liter:</td>
												<td align="left" ><?=($less_per_liter+0);?> L</td>
												<td align="left" colspan="1">VATable Sales</td>
												<td align="right" ><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?=number_format(($total_payable/$net_value_percentage),2);?></td>
												
											</tr>
											
											<tr class="data_tr">
												<td align="left" colspan="6"></td>
												<td align="left" colspan="1"></td>
												<td align="left" ></td>
												<td align="left" colspan="1">VAT Amount</td>
												<td align="right"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <span style="text-slign:right;"><?=number_format((($total_payable/$net_value_percentage)*$vat_value_percentage),2);?></span></td>
												
											</tr>
									
											<tr class="data_tr">
												<td align="left" colspan="6"></td>
												<td align="left" colspan="1"></td>
												<td align="left" ></td>
												<td align="left" colspan="1">Less: Discount</td>
												<td align="right" ><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?=number_format($total_liters*$less_per_liter,2);?></td>
												
											</tr>
											
											<tr class="data_tr">
												<td align="left" colspan="6"></td>
												<td align="left" colspan="1"></td>
												<td align="left" ></td>
												<td align="left" colspan="1">Less: With Holding Tax</td>
												<td align="right" ><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?=number_format( ( ($total_payable/$net_value_percentage)*$withholding_tax_percentage) ,2 );?></td>
												
											</tr>
									
											<tr class="data_tr">
												<td align="left" colspan="6"></td>
												<td align="left" colspan="1"></td>
												<td align="left" ></td>
												<td align="left" colspan="1"><b>TOTAL AMOUNT DUE:</b></td>
												<td align="right" ><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> <?=number_format($total_payable-($less_per_liter*$total_liters)-(($total_payable/$net_value_percentage)*$withholding_tax_percentage),2);?></span></td>
											</tr>
			
			<tr style="font-size:12px;"><td colspan="10">&nbsp;</td></tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2">Prepared by:</td>
				<td align="center" colspan="3" style="border-bottom:1px solid #000;">{{$user_data->user_real_name}}</td>
				<td align="left" colspan="5"></td>
			</tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2"></td>
				<td align="center" colspan="3">{{$user_data->user_job_title}}</td>
				<td align="left" colspan="5"></td>
			</tr>
			<tr style="font-size:12px;"><td colspan="10">&nbsp;</td></tr>
			
		</tbody>
		</table>
		
</body>
</html>