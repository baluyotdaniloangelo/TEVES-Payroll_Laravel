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
				$_print_date=date_create(date('Y-m-d'));
				$print_date = strtoupper(date_format($_print_date,"M/d/Y"));
				
				$_billing_date=date_create($receivable_data['billing_date']);
				$billing_date = strtoupper(date_format($_billing_date,"M/d/Y"));
				
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
				$_print_date=date_create(date('Y-m-d'));
				$print_date = strtoupper(date_format($_print_date,"M/d/Y"));
		?>
		<tr>
			<td colspan="4"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:12px;">VAT REG. TIN : <?=$receivable_header['branch_tin'];?></div>
			</td>
			<td colspan="2" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>DATE PRINTED</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon"><?=$print_date;?></td>
		</tr>
		<?php
			$_po_start_date=date_create("$start_date");
			$po_start_date = strtoupper(date_format($_po_start_date,"M/d/Y"));
			
			$_po_end_date=date_create("$end_date");
			$po_end_date = strtoupper(date_format($_po_end_date,"M/d/Y"));	
			?>
		<tr>
			<td colspan="4"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:12px;"><?=$receivable_header['branch_owner'];?> - <?=$receivable_header['branch_owner_title'];?></div>
			</td>
			<td colspan="2" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>SALES ORDER PERIOD</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon"><?php echo "$po_start_date - $po_end_date"; ?></td>
		</tr>
		
		<tr>
			<td colspan="5"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>AR REFERENCE</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ @$receivable_data['control_number'] }}</td>
		</tr>
		
		<tr>
			<td colspan="5"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>PAYMENT TERMS</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ @$receivable_data['payment_term'] }}</td>
		</tr>
		

		<tr>
			<td colspan="10"  width="50%" style="horizontal-align:center;text-align:left;"></td>
		</tr>
		
		
		</table>
		<br>
		<table class="" width="100%" cellspacing="0" cellpadding="1" >
		
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="15%"><b>ACCOUNT NAME</b></td>
			<td colspan="9" align="left" width="85%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $client_data['client_name'] }}</td>
			
		</tr>
		
		<tr style="font-size:12px;">
		
			<td colspan="1" align="left" width="15%"><b>TIN</b></td>
			<td colspan="9" align="left" width="85%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $client_data['client_tin'] }}</td>	
			
		</tr>		
		
		<tr style="font-size:12px;">
		
			<td colspan="1" align="left" width="15%"><b>ADDRESS</b></td>
			<td colspan="9" align="left" width="85%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $client_data['client_address'] }}</td>	
			
			
		</tr>
		
		
		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" >
		<tr style="font-size:12px;">
			<td colspan="10">&nbsp;</td>
		</tr>
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
				<td align="center" colspan="3" style="border-bottom:1px solid #000;">{{@$user_data->user_real_name}}</td>
				<td align="left" colspan="5"></td>
			</tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2"></td>
				<td align="center" colspan="3">{{@$user_data->user_job_title}}</td>
				<td align="left" colspan="5"></td>
			</tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="6"></td>
				<td align="right" colspan="2">Received by:</td>
				<td align="center" colspan="2" style="border-bottom:1px solid #000;">&nbsp;</td>
				
			</tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2">Reviewed by:</td>
				<td align="center" colspan="3" style="border-bottom:1px solid #000;">&nbsp;</td>
				<td align="right" colspan="3">Date:</td>
				<td align="center" colspan="2" style="border-bottom:1px solid #000;">&nbsp;</td>
			</tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2"></td>
				<td align="center" colspan="3" nowrap>Pricing and Sales In-Charge</td>
				<td align="right" colspan="3">Time:</td>
				<td align="center" colspan="2" style="border-bottom:1px solid #000;">&nbsp;</td>
			</tr>

			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="10">&nbsp;</td>
			</tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2" nowrap>Billing For Release:</td>
				<td align="center" colspan="3" style="border-bottom:1px solid #000;">&nbsp;</td>
				<td align="right" colspan="3"></td>
				<td align="center" colspan="2">&nbsp;</td>
			</tr>
			
			<tr class="data_tr" style="font-size:12px;">
				<td align="left" colspan="2"></td>
				<td align="center" colspan="3" nowrap>Accounting In-Charge</td>
				<td align="right" colspan="3"></td>
				<td align="center" colspan="2">&nbsp;</td>
			</tr>
			
			<tr style="font-size:12px;"><td colspan="10">&nbsp;</td></tr>
			<tr style="font-size:12px;font-style: italic;"><td colspan="10">This billing statement is not valid for claims of taxes. Please refer to Sales Invoice issued.</td></tr>
		</tbody>
		</table>
		
</body>
</html>