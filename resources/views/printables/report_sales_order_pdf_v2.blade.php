<!DOCTYPE html>

<html>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title }}</title>
	
	<style>
		body {
			font-family: Calibri, Candara, Sergoe, "Sergoe UI", Optima, Arial, sans-serif;
		}
		.data_thead {
			background-color: #000000;
			color: #fff;
		}
		.data_th {
			padding: 5px;
			font-size: 10px;
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
			left:-10px;
		}
	</style>
	
</head>
<body>
    
	<table class="" width="100%" cellspacing="0" cellpadding="1">
		
			<?php
				$_sales_order_date=date_create($sales_order_data[0]['sales_order_date']);
				$sales_order_date = strtoupper(date_format($_sales_order_date,"M/d/Y"));
				
				$logo = $branch_header['branch_logo'];
			?>
		
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%">
			<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:112px;">
			</td>
			<td colspan="5" width="50%" style="horizontal-align:center;text-align:left;">
				<b style="font-size:16px;"><?=$branch_header['branch_name'];?></b><br>
			</td>
			<td colspan="4" align="left" width="40%" style="font-size:12px; background-color: orange; text-align:center; font-weight:bold; color:#000; border-top-left-radius:30px;border-bottom-left-radius:30px;"><b>{{ $title }}</b></td> 
		</tr>
		
		<tr>
			<td colspan="3"  width="50%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:11px;"><?=$branch_header['branch_address'];?></div>
			</td>
			<td colspan="2" align="left" width="17%" style="font-size:11px; font-weight:bold; color:red;"><b>CONTROL NO.</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; color:red; border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_control_number'] }}</td>

		</tr>		
		<tr>
			<td colspan="3"  width="50%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:11px;">VAT REG. TIN : <?=$branch_header['branch_tin'];?></div>
			</td>
			<td colspan="2" align="left" width="17%" style="font-size:11px; font-weight:bold;"><b>DATE GENERATED</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon"><?=$sales_order_date;?></td>
		</tr>
		<tr>
			<td colspan="3"  width="50%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:11px;"><?=$branch_header['branch_owner'];?> - <?=$branch_header['branch_owner_title'];?></div>
			</td>
			<td colspan="2" align="left" width="17%" style="font-size:11px; font-weight:bold;"><b>D.R. NO.</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_dr_number'] }}</td>
		</tr>
		
		<tr>
			<td colspan="4"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="17%" style="font-size:11px; font-weight:bold;"><b>P.O. NO.</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_po_number'] }}</td>
		</tr>
		
		<tr>
			<td colspan="4"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="17%" style="font-size:11px; font-weight:bold;"><b>SALES INVOICE</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_or_number'] }}</td>
		</tr>

		<tr>
			<td colspan="4" width="40%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="27%" style="font-size:11px; font-weight:bold;"><b>COLLECTION RECEIPT</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_collection_receipt'] }}</td>
		</tr>
		
		<tr>
			<td colspan="4"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="17%" style="font-size:11px; font-weight:bold;"><b>CHARGE INVOICE</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_charge_invoice'] }}</td>
		</tr>
		
		<tr>
			<td colspan="4"  width="50%" style="horizontal-align:center;text-align:left;"></td>
			<td colspan="2" align="left" width="17%" style="font-size:11px; font-weight:bold;"><b>PAYMENT TERM</b></td>
			<td colspan="4" align="left" width="23%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_payment_term'] }}</td>
		</tr>

		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" >
		<tr style="font-size:10px;">
			<td colspan="10" style="height:7px !important;" width="100%"></td>
		</tr>
		<tr style="font-size:11px;">
			<td colspan="2" align="left" width="5%"><b>SOLD TO</b></td>
			<td colspan="8" align="left" width="95%" style="border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['client_name'] }}</td>
		</tr>
		<tr style="font-size:11px;">
			<td colspan="2" align="left" width="5%"><b>TIN NO.</b></td>
			<td colspan="8" align="left" width="95%" style="border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['client_tin'] }}</td>
		</tr>	
		<tr style="font-size:11px;">
			<td colspan="2" align="left" width="5%"><b>ADDRESS</b></td>
			<td colspan="8" align="left" width="95%" style="border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['client_address'] }}</td>
		</tr>
		
		<tr style="font-size:11px;">
			<td colspan="2" align="left" width="5%"><b>DELIVERED TO</b></td>
			<td colspan="8" align="left" width="95%" style="border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_delivered_to'] }}</td>
		</tr>
		
		<tr style="font-size:11px;">
			<td colspan="2" align="left" width="5%"><b>ADDRESS</b></td>
			<td colspan="8" align="left" width="90%" style="border-bottom:solid 1px gray;" class="td_colon">{{ $sales_order_data[0]['sales_order_delivered_to_address'] }}</td>
		</tr>
		<tr style="font-size:10px;">
			<td colspan="10" style="height:7px !important;" width="100%"></td>
		</tr>
		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" >
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr>
		<tr style="font-size:12px;border:0 solid #000;">		

			<td colspan="2" width="20%" align="center" style="border-right:1px solid gray; background-color: #c6e0b4; font-weight:bold; height:15px !important;">DESCRIPTION</td>		
			<td colspan="2" width="20%" nowrap align="center" style="border-right:1px solid gray; background-color: #c6e0b4; font-weight:bold;">QUANTITY</td>
			<td colspan="1" width="10%" align="center" style="border-right:1px solid gray; background-color: #c6e0b4; font-weight:bold;">UNIT</td>		
			<td colspan="3" width="30%" nowrap align="center" style="border-right:1px solid gray; background-color: #c6e0b4; font-weight:bold;">UNIT PRICE</td>
			<td colspan="2" width="20%" align="center" style="border:0px solid #000; background-color: #c6e0b4; font-weight:bold;">AMOUNT</td>		
					
		</tr>									
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr>	
			<?php 
			$no = 1;
			$total_liters = 0;
			?>
			@foreach ($sales_order_component as $sales_order_component_cols)
			<?php
			
			if($sales_order_component_cols->product_unit_measurement=='L' || $sales_order_component_cols->product_unit_measurement=='l'){
				$total_liters += $sales_order_component_cols->order_quantity;
			}else{
				$total_liters += 0;
			}
			
			$component_height = 160 / count($sales_order_component);
			
			?>
			<tr class="data_tr" style="font-size:12px;">
				<td colspan="2" align="left"  style="height:<?=$component_height;?>px !important;border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;"><?php echo "$sales_order_component_cols->product_name"; ?></td>
				<td colspan="2" align="right" nowrap style="height:<?=$component_height;?>px !important;border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;"><?=number_format($sales_order_component_cols->order_quantity,2,".",",");?></td>
				<td colspan="1" align="center" nowrap style="height:<?=$component_height;?>px !important;border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;">{{$sales_order_component_cols->product_unit_measurement}}</td>
				<td colspan="3" align="right" nowrap style="height:<?=$component_height;?>px !important;border-left:0px solid #000; border-bottom:solid 1px gray; padding:10px;"><?=number_format($sales_order_component_cols->product_price,2,".",",");?></td>
				<td colspan="2" align="right" nowrap style="height:<?=$component_height;?>px !important;border-left:0px solid #000; border-right:0px solid #000; border-bottom:solid 1px gray;"><?=number_format($sales_order_component_cols->order_total_amount,2,".",",");?></td>
			</tr>
			<?php 
			$no++; 
			?>
			
			@endforeach

		<tr style="font-size:12px;">
			<td colspan="6"></td>
			<td colspan="2" align="left" style="border-left: 0px solid #000; font-weight:bold; height:20px !important;">Gross Amount </td>
			<td colspan="2" align="right" style="background-color: #fff; border-right: 0px solid #000; border-bottom: 0px solid #000;">
			<?=number_format($sales_order_data[0]['sales_order_gross_amount'],2);?>
		</tr>
		
		<tr style="font-size:12px;">
			<td colspan="6"></td>
			<td colspan="2" align="left" style="border-left: 0px solid #000; font-weight:bold; height:20px !important;">Net Amount </td>
			<td colspan="2" align="right" style="background-color: #fff; border-right: 0px solid #000; border-bottom: 0px solid #000;">
			<?=number_format($sales_order_data[0]['sales_order_net_amount'],2);?>
		</tr>
		
		<tr style="font-size:12px;">
			<td colspan="6"></td>
			<td colspan="2" align="left" style="border-left: 0px solid #000; font-weight:bold; height:20px !important;">Withholding Tax (<?=$sales_order_data[0]['sales_order_withholding_tax'];?>%) </td>
			<td colspan="2" align="right" style="background-color: #fff; border-right: 0px solid #000; border-bottom: 0px solid #000;">
			<?=number_format($sales_order_data[0]['sales_order_net_amount']*$sales_order_data[0]['sales_order_withholding_tax']/100,2);?>
		</tr>		

		<tr style="font-size:12px;">			
			<td colspan="6"></td>
			<td colspan="2" align="left" style="border-left: 0px solid #000; font-weight:bold; height:20px !important;">Total Due </td>
			<td colspan="2" align="right" style="background-color: #fff; border-right: 0px solid #000; border-bottom:double;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> 
			<?=number_format($sales_order_data[0]['sales_order_total_due'],2);?>
		</tr>		
		
		<tr>
			<td colspan="10" style="border-left:0px solid #000;border-right:0px solid #000;border-bottom:0px solid #000;"></td>
		</tr>
		
		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" align="center" style="border-top:1px solid gray; border-left:0px solid #000; border-right:0px solid #000; border-bottom:1px solid gray; height:40px !important;font-style: italic;">In Words</td>
			<td colspan="8" style="border-top:1px solid gray; border-bottom:1px solid gray; text-align:center;">&nbsp;<?php echo strtoupper($amount_in_words); ?></td>
		</tr>

		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="3" align="center" style="border-right:1px solid gray; background-color: #c6e0b4; font-weight:bold; height:15px !important;">DELIVERY METHOD</td>	
			<td colspan="4" nowrap align="center" style="border-right:1px solid gray; background-color: #c6e0b4; font-weight:bold;">HAULER | DRIVER</td>
			<td colspan="3" nowrap align="center" style="border:0px solid #000; background-color: #c6e0b4; font-weight:bold;">REQUIRED DATE</td>
		</tr>
			
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr> 
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="3" align="center" style="border-left:0px solid #000; border-bottom:solid 1px gray; height: 20px; padding:10px;">{{$sales_order_data[0]->sales_order_delivery_method}}</td>
			<td colspan="4" align="center"  style="border-left:0px solid #000; border-bottom:solid 1px gray; height: 20px; padding:10px;">{{$sales_order_data[0]->sales_order_hauler}}</td>
			<?php
				$_sales_order_required_date=date_create($sales_order_data[0]['sales_order_required_date']);
				$sales_order_required_date = strtoupper(date_format($_sales_order_required_date,"M/d/Y"));
			?>
			<td colspan="3" align="center" style="border-left:0px solid #000; border-bottom:solid 1px gray; height: 20px; padding:10px;"><?=$sales_order_required_date;?></td>	
		</tr>
		
		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
	  	</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" >			
		<tr style="font-size:10px;border:0 solid #000;">
			<td colspan="6" align="left" style="border-top:1px solid gray; border-left:1px solid gray; border-right:0px solid gray; border-bottom:0px solid gray; font-style: italic; height:32px !important;">Instructions;</td>
			<td colspan="4" align="center" style="border:1px solid gray; font-weight:bold;">NOTE</td>	
		</tr>
	    
		<tr style="font-size:10px;border:0 solid #000;" rowspan="6">
			<td colspan="6" align="center" style="border-top:0px solid gray; border-left:1px solid gray; border-right:0px solid gray; border-bottom:1px solid gray; height:160px !important; text-align: justify;">{{$sales_order_data[0]->sales_order_instructions}}</td>
			<td colspan="4" align="left" style="border-top:0px solid gray; border-left:1px solid gray; border-right:1px solid gray; border-bottom:1px solid gray; height:90px !important; height:100px !important;">{{$sales_order_data[0]->sales_order_note}}</td>
		</tr>
		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		<tr style="font-size:10px;">
			<td colspan="10" align="left" style="border-top:1px solid gray;border-right:1px solid gray;  border-left:1px solid gray; border-bottom:0px solid gray; font-style: italic; height:30px !important;">&nbsp;Prepared by:</td>	
				
		</tr>
		<tr style="font-size:10px;">
			<td colspan="10" align="center" style="border-bottom:1px solid gray;border-right:1px solid gray;  border-left:1px solid gray;">&nbsp;{{$user_data->user_real_name}}<br></td>	
				
		</tr>
		<tr style="font-size:10px;font-style: italic;">
			<td colspan="10" style="">This Sales Order is not valid for claims of taxes. Please refer to sales invoice / official receipts issued.<br>
			This is a system-generated. No Signature is Required.</td>
		</tr>
		</table>
		
		
</body>
</html>