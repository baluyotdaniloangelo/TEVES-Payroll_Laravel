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
			left:-5px;
		}
</style>
</head>
<body>
    
	<table class="" width="100%" cellspacing="0" cellpadding="1">
		
			<?php
				$_purchase_order_date=date_create($purchase_order_data[0]['purchase_order_date']);
				$purchase_order_date = strtoupper(date_format($_purchase_order_date,"M/d/Y"));
				
				$logo = $branch_header['branch_logo'];
			?>
			
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%">
				<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:112px;">
			</td>
			<td colspan="7" width="40%" style="horizontal-align:center;text-align:left;"><b style="font-size:18px;"><?=$branch_header['branch_name'];?></b></td>
			<td colspan="2" nowrap align="left" width="50%" style="font-size:12px; background-color: yellowgreen; text-align:center; font-weight:bold; color:#000; border-top-left-radius:30px;border-bottom-left-radius:30px;"><b>{{ $title }}</b></td>
		</tr>
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;"><?=$branch_header['branch_address'];?></div>
			</td>
			<td colspan="3" align="left" width="20%" style="font-size:12px; font-weight:bold; color:red;"><b>CONTROL NO.</b></td>
			<td colspan="3" align="left" width="30%" style="font-size:12px; color:red; border-bottom:solid 1px gray;" class="td_colon">{{ $purchase_order_data[0]['purchase_order_control_number'] }}</td>
		</tr>		
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;">VAT REG. TIN : <?=$branch_header['branch_tin'];?></div>
			</td>
			<td colspan="3" align="left" width="20%" style="font-size:12px; font-weight:bold;;"><b>DATE</b></td>
			<td colspan="3" align="left" width="30%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon"><?=$purchase_order_date;?></td>
		</tr>
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;"><?=$branch_header['branch_owner'];?> - <?=$branch_header['branch_owner_title'];?></div>
			</td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>SALES ORDER NO.</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $purchase_order_data[0]['purchase_order_sales_order_number'] }}</td>
		</tr>
		
		<tr>
			<td colspan="4"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>COLLECTION RECEIPT NO.</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $purchase_order_data[0]['purchase_order_collection_receipt_no'] }}</td>
		</tr>
		
		<tr>
			<td colspan="4"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>SALES INVOICE NO.</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $purchase_order_data[0]['purchase_order_official_receipt_no'] }}</td>
		</tr>

		<tr>
			<td colspan="4"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>DELIVERY RECEIPT NO.</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $purchase_order_data[0]['purchase_order_delivery_receipt_no'] }}</td>
		</tr>
		
		</table>

		<br>
		<table class="" width="100%" cellspacing="0" cellpadding="1" >	
		
		<tr>
			<td colspan="4" align="left" width='20%' style="font-size:12px; font-weight:bold;">SUPPLIER</td>
			<td colspan="6" align="left" width='80%' style="font-size:12px; border-bottom:1px solid #000;" class="td_colon">{{ $purchase_order_data[0]['supplier_name'] }}</td>			
		</tr>
		
		<tr>
			<td colspan="4" align="left" style="font-size:12px; font-weight:bold;">TIN</td>
			<td colspan="6" align="left" style="font-size:12px; border-bottom:1px solid #000;" class="td_colon">{{ $purchase_order_data[0]['supplier_tin'] }}</td>			
		</tr>
		
		<tr>
			<td colspan="4" align="left" style="font-size:12px; font-weight:bold;">ADDRESS</td>
			<td colspan="6" align="left" style="font-size:12px; border-bottom:1px solid #000; " class="td_colon">{{ $purchase_order_data[0]['supplier_address'] }}</td>			
		</tr>

		</table>
		<br>
		<table class="" width="100%" cellspacing="0" cellpadding="1" >	
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" align="center" style="border-top:0px solid #000; border-left:0px solid #000; border-right:0px solid #000; font-weight:bold; background-color: #c6e0b4; height:25px !important;">PAYMENT DETAILS</td>			
		</tr>		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr> 
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="3" align="center" style="border-top:0px solid #000; border-left:0px solid gray; border-right:0px solid #000; border-bottom:0px solid #000; font-weight:bold; height:25px !important; background-color: #c6e0b4;">BANK</td>
			<td colspan="2" align="center" style="border-top:0px solid #000; border-left:1px solid gray; border-right:0px solid #000; border-bottom:0px solid #000; font-weight:bold; background-color: #c6e0b4;">DATE OF PAYMENT</td>
			<td colspan="3" align="center" style="border-top:0px solid #000; border-left:1px solid gray; border-right:0px solid #000; border-bottom:0px solid #000; font-weight:bold; background-color: #c6e0b4;">REFERENCE NO.</td>
			<td colspan="2" align="center" style="border-top:0px solid #000; border-left:1px solid gray; border-right:0px solid #000; border-bottom:0px solid #000; font-weight:bold; background-color: #c6e0b4;">AMOUNT</td>			
		</tr> 
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr>
			<?php 
			$no_p = 1;
			?>
			
			@foreach ($purchase_payment_component as $purchase_payment_component_cols)
					
			<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="3" align="center" style="border-top:0px solid #000; border-left:0px solid #000; border-right:0px solid #000; border-bottom:0px solid #000;height:25px !important;">{{$purchase_payment_component_cols->purchase_order_bank}}</td>
				<?php
				$_purchase_order_date_of_payment=date_create($purchase_payment_component_cols['purchase_order_date_of_payment']);
				$purchase_order_date_of_payment = strtoupper(date_format($_purchase_order_date_of_payment,"M/d/Y"));
				?>
			<td colspan="2" align="center" style="border-top:0px solid #000; border-left:0px solid #000; border-right:0px solid #000; border-bottom:0px solid #000;"><?=$purchase_order_date_of_payment;?></td>
			<td colspan="3" align="center" style="border-top:0px solid #000; border-left:0px solid #000; border-right:0px solid #000; border-bottom:0px solid #000;">{{$purchase_payment_component_cols->purchase_order_reference_no}}</td>
			<td colspan="2" align="right" style="border-top:0px solid #000; border-left:0px solid #000; border-right:0px solid #000; border-bottom:0px solid #000;">	
			<?=number_format($purchase_payment_component_cols['purchase_order_payment_amount'],4);?>
			</td>			
			</tr>
			<?php 
			$no_p++; 
			?>
			
			@endforeach

		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" > 	  	
			
		<tr style="font-size:12px;">
			<td colspan="10" align="left" style="border-top:1px solid gray;border-right:1px solid gray;  border-left:1px solid gray; border-bottom:0px solid #000; font-style: italic; height:25px !important;">&nbsp;Prepared by:</td>	
				
		</tr>
		<tr style="font-size:12px;">
			<td colspan="10" align="center" style="border-bottom:1px solid gray;border-right:1px solid gray;  border-left:1px solid gray;">&nbsp;{{$user_data->user_real_name}}<br></td>	
				
		</tr>
		<tr style="font-size:12px;font-style: italic;">
			<td colspan="10" style="">This is a system-generated. No Signature is Required.</td>
		</tr>
		</table>
		
</body>
</html>