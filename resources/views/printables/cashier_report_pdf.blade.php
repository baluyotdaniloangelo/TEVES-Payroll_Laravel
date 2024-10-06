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
			$logo = $branch_header['branch_logo'];
		?>
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%">
				<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:112px;">
			</td>
			<td colspan="7" width="40%" style="horizontal-align:center;text-align:left;"><b style="font-size:18px;"><?=$branch_header['branch_name'];?></b></td>
			<td colspan="2" nowrap align="left" width="50%" style="font-size:12px; background-color: pink; text-align:center; font-weight:bold; color:#000; border-top-left-radius:30px;border-bottom-left-radius:30px;"><b>{{ $title }}</b></td>
		</tr>
		
		<tr>
			<td colspan="5"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;"><?=$branch_header['branch_address'];?></div>
			</td>
			<?php
				$_report_date	=	date_create($CashiersReportData[0]['report_date']);
				$report_date 	= 	strtoupper(date_format($_report_date,"M/d/Y"));
			?>
			<td colspan="1" align="left" width="12%" style="font-size:12px; font-weight:bold;"><b>DATE</b></td>
			<td colspan="3" align="left" width="38%" style="font-size:12px; order-bottom:solid 1px gray;" class="td_colon"><?=$report_date;?></td>
		</tr>		
		
		<tr>
			<td colspan="5"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;">VAT REG. TIN : <?=$branch_header['branch_tin'];?></div>
			</td>
			<td colspan="1" align="left" width="12%" style="font-size:12px; font-weight:bold;;"><b>SHIFT</b></td>
			<td colspan="3" align="left" width="38%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $CashiersReportData[0]['shift'] }}</td>
		</tr>
		
		<tr>
			<td colspan="5"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;"><?=$branch_header['branch_owner'];?> - <?=$branch_header['branch_owner_title'];?></div>
			</td>
			<td colspan="2" align="left" width="10%" style="font-size:12px;"><b></b></td>
			<td colspan="2" align="left" width="40%" style="font-size:12px;" class=""></td>
		</tr>
		
		</table>
		
		<br>
		<table class="" width="100%" cellspacing="0" cellpadding="1">
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="30%"><b>CASHIER ON DUTY</b></td>
			<td colspan="9" align="left" style="border-bottom:1px solid #000;" width="70%" class="td_colon">{{ $CashiersReportData[0]['cashiers_name'] }}</td>			
		</tr>
	
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="30%"><b>FORECOURT ATTENDANT</b></td>
			<td colspan="9" align="left" style="border-bottom:1px solid #000;" width="70%" class="td_colon">{{ $CashiersReportData[0]['forecourt_attendant'] }}</td>	
				</tr>
	
		<tr style="font-size:12px;">
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		</table>
		
		<table cellspacing="0" width="100%">
		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" colspan="3" rowspan="2">PRODUCT </td>
		  <td style="font-size:11px; border:1px solid #000;" colspan="4">TOTALIZER READING</td>
		  <td style="font-size:11px; border:1px solid #000;" rowspan="2">CALIBRATION</td>
		  <td style="font-size:11px; border:1px solid #000;" rowspan="2">SALES IN LITERS</td>
		  <td style="font-size:11px; border:1px solid #000;" rowspan="2">PUMP PRICE</td>
		  <td style="font-size:11px; border:1px solid #000;" rowspan="2">PESO  SALES</td>
		</tr>
  
		<tr style="text-align: center; font-size:11px; background-color: #c6e0b4;" > 
		  <td style="font-size:11px; border:1px solid #000;" colspan="2">BEGINNING</td>
		  <td style="font-size:11px; border:1px solid #000;" colspan="2">CLOSING</td>
		</tr>
		
		<!--Load Part 1-->
			<?php 
			$p1_no_premium = 1;
			$order_quantity_P1_premium_95 = 0;
			$product_price_P1_premium_95 = 0;
			$order_total_amount_P1_premium_95 = 0;
			?>
			
			@foreach ($data_P1_premium_95 as $data_P1_premium_95_cols)
			<tr class="data_tr" style="text-align: center; font-size:11px;">
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_premium_95_cols->product_name}}</td>
				<td nowrap style="border:1px solid #000;"><?=$p1_no_premium;?></td>
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_premium_95_cols->beginning_reading}}</td>
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_premium_95_cols->closing_reading}}</td>
				<td nowrap style="border:1px solid #000;">{{$data_P1_premium_95_cols->calibration}}</td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_premium_95_cols['order_quantity'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_premium_95_cols['product_price'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_premium_95_cols['order_total_amount'],2,".",",");?></td>
			</tr>
			<?php 
			
			$order_quantity_P1_premium_95 += $data_P1_premium_95_cols->order_quantity;
			$product_price_P1_premium_95 += $data_P1_premium_95_cols->product_price;
			$order_total_amount_P1_premium_95 += $data_P1_premium_95_cols->order_total_amount;
			
			$p1_no_premium++; 
			
			?>
			@endforeach
		
		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000; text-align:right; " colspan="8">TOTAL </td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($order_quantity_P1_premium_95,2,".",",");?></td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($product_price_P1_premium_95,2,".",",");?></td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($order_total_amount_P1_premium_95,2,".",",");?></td>
		</tr>
		
		<tr>
		  <td colspan="11">&nbsp;</td>
		</tr>
		
			<?php 
			$p1_no_super_regular = 1;
			$order_quantity_P1_super_regular = 0;
			$product_price_P1_super_regular = 0;
			$order_total_amount_P1_super_regular = 0;
			?>
			@foreach ($data_P1_super_regular as $data_P1_super_regular_cols)
			<tr class="data_tr" style="text-align: center; font-size:11px;">
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_super_regular_cols->product_name}}</td>
				<td nowrap style="border:1px solid #000;"><?=$p1_no_super_regular;?></td>
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_super_regular_cols->beginning_reading}}</td>
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_super_regular_cols->closing_reading}}</td>
				<td nowrap style="border:1px solid #000;">{{$data_P1_super_regular_cols->calibration}}</td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_super_regular_cols['order_quantity'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_super_regular_cols['product_price'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_super_regular_cols['order_total_amount'],2,".",",");?></td>
			</tr>
			<?php 
			
			$order_quantity_P1_super_regular += $data_P1_super_regular_cols->order_quantity;
			$product_price_P1_super_regular += $data_P1_super_regular_cols->product_price;
			$order_total_amount_P1_super_regular += $data_P1_super_regular_cols->order_total_amount;
			
			$p1_no_super_regular++; 
			?>
			@endforeach
		
		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000; text-align:right; " colspan="8">TOTAL </td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($order_quantity_P1_super_regular,2,".",",");?></td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($product_price_P1_super_regular,2,".",",");?></td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($order_total_amount_P1_super_regular,2,".",",");?></td>
		</tr>
		
		<tr>
		  <td colspan="11">&nbsp;</td>
		</tr>
		
			<?php 
			$p1_no_diesel = 1;
			$order_quantity_P1_diesel = 0;
			$product_price_P1_diesel = 0;
			$order_total_amount_P1_diesel = 0;
			?>
			@foreach ($data_P1_diesel as $data_P1_diesel_cols)
			<tr class="data_tr" style="text-align: center; font-size:11px;">
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_diesel_cols->product_name}}</td>
				<td nowrap style="border:1px solid #000;"><?=$p1_no_diesel;?></td>
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_diesel_cols->beginning_reading}}</td>
				<td nowrap style="border:1px solid #000;" colspan="2">{{$data_P1_diesel_cols->closing_reading}}</td>
				<td nowrap style="border:1px solid #000;">{{$data_P1_diesel_cols->calibration}}</td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_diesel_cols['order_quantity'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_diesel_cols['product_price'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P1_diesel_cols['order_total_amount'],2,".",",");?></td>
			</tr>
			<?php 
			
			$order_quantity_P1_diesel += $data_P1_diesel_cols->order_quantity;
			$product_price_P1_diesel += $data_P1_diesel_cols->product_price;
			$order_total_amount_P1_diesel += $data_P1_diesel_cols->order_total_amount;
			
			$p1_no_diesel++; 
			?>
			@endforeach
		
		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000; text-align:right; " colspan="8">TOTAL </td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($order_quantity_P1_diesel,2,".",",");?></td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($product_price_P1_diesel,2,".",",");?></td>
		  <td style="font-size:11px; border:1px solid #000;"><?=number_format($order_total_amount_P1_diesel,2,".",",");?></td>
		</tr>
		
		</table>
		<br>
		<table cellspacing="0" width="100%">

        <tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" colspan="5">OTHER SALES (Lubricants/Car Care Products/Others)</td>
		</tr>

		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
          <td style="font-size:11px; border:1px solid #000;" width="10%">#</td>
          <td style="font-size:11px; border:1px solid #000;">Product Description</td>
		  <td style="font-size:11px; border:1px solid #000;">Quantity</td>
		  <td style="font-size:11px; border:1px solid #000;">Unit Price</td>
          <td style="font-size:11px; border:1px solid #000;">Amount</td>
		</tr>
            <?php
            $other_sales_no = 1;
            $other_sales_total = 0;
            ?>
            @foreach ($data_P2 as $data_P2_cols)
			<tr class="data_tr" style="text-align: center; font-size:11px;">
				<td nowrap style="border:1px solid #000;"><?=$other_sales_no;?></td>
                <td nowrap style="border:1px solid #000;">{{$data_P2_cols->product_name}}</td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P2_cols['order_quantity'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P2_cols['product_price'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_P2_cols['order_total_amount'],2,".",",");?></td>
			</tr>
            <?php
            $other_sales_no++;
            $other_sales_total += $data_P2_cols->order_total_amount;
            ?>
			@endforeach

		</table>
		<br>
		
		<table cellspacing="0" width="100%">
		
		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" colspan="4">MISCELLANEOUS ITEMS</td>
		</tr>
		
        <tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" colspan="4">SALES ORDER - CREDIT SALES</td>
		</tr>

		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" width="10%">#</td>
		  <td style="font-size:11px; border:1px solid #000;">Product</td>
		  <td style="font-size:11px; border:1px solid #000;">Liters</td>
          <td style="font-size:11px; border:1px solid #000;">Amount</td>
		</tr>
			<?php
			$sc = 1;
			$total_sales_credit =0;
			?>
            @foreach ($data_SALES_CREDIT as $data_SALES_CREDIT_cols)
			<tr class="data_tr" style="text-align: center; font-size:11px; ">
				<td nowrap style="border:1px solid #000;"><?=$sc;?></td>
				<td nowrap style="border:1px solid #000;">{{$data_SALES_CREDIT_cols->product_name}}</td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_SALES_CREDIT_cols['order_quantity'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_SALES_CREDIT_cols['order_total_amount'],2,".",",");?></td>
			</tr>
			<?php 
			$sc++; 
			$total_sales_credit += $data_SALES_CREDIT_cols->order_total_amount;
			?>
			
			@endforeach

		</table>
		<br>
		<table cellspacing="0" width="100%">
		
        <tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" colspan="5">DISCOUNTS ( WHOLE SALE - FUEL)</td>
		</tr>

		<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" width="10%">#</td>
		  <td style="font-size:11px; border:1px solid #000;">Reference No.</td>
		  <td style="font-size:11px; border:1px solid #000;">Liters</td>
          <td style="font-size:11px; border:1px solid #000;">Unit Price</td>
		  <td style="font-size:11px; border:1px solid #000;">Amount</td>
		</tr>
			
			<?php
			$dis = 1;
			$total_discount = 0;
			?>

            @foreach ($data_DISCOUNTS as $data_DISCOUNTS_cols)
			<tr class="data_tr" style="text-align: center; font-size:11px;">
				<td nowrap style="border:1px solid #000;"><?=$dis;?></td>
				<td nowrap style="border:1px solid #000;">{{$data_DISCOUNTS_cols->reference_no}}</td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_DISCOUNTS_cols['order_quantity'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_DISCOUNTS_cols['unit_price'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_DISCOUNTS_cols['order_total_amount'],2,".",",");?></td>
			</tr>
			<?php
			$dis++; 
			$total_discount += $data_DISCOUNTS_cols->order_total_amount;
			?>
			@endforeach

		</table>
		<br>
		<table cellspacing="0" width="100%">
		
        <tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
		  <td style="font-size:11px; border:1px solid #000;" colspan="4"> OTHERS Lubricants discounts / Money Cash Out / Misload</td>
		</tr>

		<tr style="text-align:center; font-size:11px; border:1px solid #000; " >
		  <td style="font-size:11px; border:1px solid #000;" width="10%">#</td>
		  <td style="font-size:11px; border:1px solid #000;">Reference No.</td>
		  <td style="font-size:11px; border:1px solid #000;">Liters / Pieces</td>
		  <td style="font-size:11px; border:1px solid #000;">Amount</td>
		</tr>
			
			<?php
			$other_msc = 1;
			$total_others_msc = 0;
			?>

            @foreach ($data_OTHERS as $data_OTHERS_cols)
			<tr class="data_tr" style="text-align: center; font-size:11px;">
				<td nowrap style="border:1px solid #000;" width="10%"><?=$other_msc;?></td>
				<td nowrap style="border:1px solid #000;">{{$data_OTHERS_cols->reference_no}}</td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_OTHERS_cols['order_quantity'],2,".",",");?></td>
				<td nowrap style="border:1px solid #000;"><?=number_format($data_OTHERS_cols['order_total_amount'],2,".",",");?></td>
				
			</tr>
			<?php
			$other_msc++; 
			$total_others_msc += $data_OTHERS_cols->order_total_amount;
			?>
			@endforeach

		</table>

		</table>

		<br>
	   <?php
            $total_fuel_sales = $order_total_amount_P1_premium_95 + $order_total_amount_P1_super_regular + $order_total_amount_P1_diesel;
        ?>
		<table cellspacing="0" width="100%">		
                        <tr>
		                  <td width="45%">
								<table cellspacing="0" width="100%">		
								<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
								  <td style="font-size:11px; border:1px solid #000;" colspan="2">SUMMARY</td>
								</tr>

								<tr>
								   <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">FUEL SALES</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($total_fuel_sales,2,".",",");?></td>
								</tr>

								<tr>
								   <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">OTHER SALES</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($other_sales_total,2,".",",");?></td>
								</tr>

								<tr>
								   <td style="font-size:11px; border:1px solid #000; text-align:left; background-color: #c6e0b4;" colspan="2">MISCELLANEOUS ITEMS</td>
								   
								</tr>

								<tr>
								   <td style="font-size:11px; border:1px solid #000; text-align:right; background-color: #c6e0b4;">SALES ORDER - CREDIT SALES</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($total_sales_credit,2,".",",");?></td>
								</tr>

								<tr>
								   <td style="font-size:11px; border:1px solid #000; text-align:right; background-color: #c6e0b4;">DISCOUNTS ( WHOLE SALE - FUEL)</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($total_discount,2,".",",");?></td>
								</tr>

								<tr>
								   <td style="font-size:11px; border:1px solid #000; text-align:right; background-color: #c6e0b4;">OTHERS</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($total_others_msc,2,".",",");?></td>
								</tr>
								<?php
								$theoretical_sales_total = ($total_fuel_sales + $other_sales_total) - ($total_sales_credit+$total_discount+$total_others_msc)
								?>
								<tr>
								   <td style="font-size:11px; border:1px solid #000; text-align:left; background-color: #c6e0b4;">THEORETICAL SALES</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($theoretical_sales_total,2,".",",");?></td>
								</tr>
								<?php
								
								$one_thousand_deno 		= $data_Cash_on_hand[0]->one_thousand_deno;
								$five_hundred_deno 		= $data_Cash_on_hand[0]->five_hundred_deno;
								$two_hundred_deno 		= $data_Cash_on_hand[0]->two_hundred_deno;
								$one_hundred_deno		= $data_Cash_on_hand[0]->one_hundred_deno;
								$fifty_deno 			= $data_Cash_on_hand[0]->fifty_deno;
								$twenty_deno 			= $data_Cash_on_hand[0]->twenty_deno;
								$ten_deno 				= $data_Cash_on_hand[0]->ten_deno;
								$five_deno 				= $data_Cash_on_hand[0]->five_deno;
								$one_deno 				= $data_Cash_on_hand[0]->one_deno;
								$twenty_five_cent_deno 	= $data_Cash_on_hand[0]->twenty_five_cent_deno;
								$cash_drop 				= $data_Cash_on_hand[0]->cash_drop;
								
								$one_thousand_deno_total 	= $one_thousand_deno * 1000;
								$five_hundred_deno_total 	= $five_hundred_deno * 500;
								$two_hundred_deno_total 	= $two_hundred_deno * 200;
								$one_hundred_deno_total 	= $one_hundred_deno * 100;
								
								$fifty_deno_total 			= $fifty_deno * 50;
								$twenty_deno_total 			= $twenty_deno * 20;
								$ten_deno_total 			= $ten_deno * 10;
								$five_deno_total 			= $five_deno * 5;
								$one_deno_total 			= $one_deno * 1;
								$twenty_five_cent_deno_total = $twenty_five_cent_deno * 0.25;
								
								$total_cash_on_hand = 
									$cash_drop + 
									$one_thousand_deno_total + 
									$five_hundred_deno_total + 
									$two_hundred_deno_total + 
									$one_hundred_deno_total +
									$fifty_deno_total + 
									$twenty_deno_total +
									$ten_deno_total + 
									$five_deno_total +
									$one_deno_total +
									$twenty_five_cent_deno_total;
									
								$short_over = $total_cash_on_hand - ($theoretical_sales_total);
								?>
								<tr>
								   <td style="font-size:11px; border:1px solid #000; text-align:left; background-color: #c6e0b4;">CASH ON HAND</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($total_cash_on_hand,2,".",",");?></td>
								</tr>

								<tr>
								   <td style="font-size:11px; border:1px solid #000; text-align:left; background-color: #c6e0b4;">CASH - SHORT/OVER</td>
								   <td style="font-size:11px; border:1px solid #000; text-align:right;"><?=number_format($short_over,2,".",",");?></td>
								</tr>
								<tr>
								   <td style="font-size:11px; border:1px solid #fff; text-align:left;" colspan=2>&nbsp;</td>
								</tr>
								<tr>
								   <td style="font-size:11px; border:1px solid #fff; text-align:left;" colspan=2>&nbsp;</td>
								</tr>
								<tr>
								   <td style="font-size:11px; border:1px solid #fff; text-align:left;" colspan=2>&nbsp;</td>
								</tr>
								<tr>
								   <td style="font-size:11px; border:1px solid #fff; text-align:left;" colspan=2>&nbsp;</td>
								</tr>
								</table>
							</td>
							<td width="10%">
							</td>
							<td width="45%">
							<table cellspacing="0" width="100%">		
							<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
							  <td style="font-size:11px; border:1px solid #000;" colspan="3">CASH ON HAND SUMMARY</td>
							</tr>
							<tr style="text-align:center; font-size:11px; border:1px solid #000; background-color: #c6e0b4;" >
							  <td style="font-size:11px; border:1px solid #000;">Deno</td>
							  <td style="font-size:11px; border:1px solid #000;">Quantity</td>
							  <td style="font-size:11px; border:1px solid #000;">Amount</td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">1000</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$one_thousand_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($one_thousand_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">500</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$five_hundred_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($five_hundred_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">200</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$two_hundred_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($two_hundred_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">100</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$one_hundred_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($one_hundred_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">50</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$fifty_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($fifty_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">20</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$twenty_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($twenty_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">10</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$ten_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($ten_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">5</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$five_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($five_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">1</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$one_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($one_deno_total,2,".",",");?></td>
							</tr>
							<tr style="text-align:right; font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;">0.25</td>
							  <td style="font-size:11px; border:1px solid #000;"><?=$twenty_five_cent_deno;?></td>
							  <td style="font-size:11px; border:1px solid #000;"><?=number_format($twenty_five_cent_deno_total,2,".",",");?></td>
							</tr>
							<tr style="font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;" colspan='2'>Total cash on hand</td>
							  <td style="text-align:right; font-size:11px; border:1px solid #000;"><?=number_format($total_cash_on_hand,2,".",",");?></td>
							</tr>
							<tr style="font-size:11px; border:1px solid #000; " >
							  <td style="font-size:11px; border:1px solid #000; background-color: #c6e0b4;" colspan='2'>Cash drop</td>
							  <td style="text-align:right; font-size:11px; border:1px solid #000;"><?=number_format($cash_drop,2,".",",");?></td>
							</tr>
							</table>
							
							</td>
							</tr>
		</table>
		
       <br>
	   		<table cellspacing="0" width="100%">
			
					<tr class='report'>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;" colspan='10'>Inventory</th>
					</tr>
					
					<tr class='report'>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">#</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Product</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Tank Name</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Tank Capacity</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Beginning Inventory</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Sales in Liters</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Delivery</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Ending Inventory</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Book Stock</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Variance</th>
					</tr>
					<?php
					$PH6_inventory_count = 1;
					?>
					@foreach ($data_PH6_inventory as $data_PH6_inventory_cols)
					<tr class="data_tr" style="text-align: center; font-size:11px;">
						
						<td nowrap style="border:1px solid #000;" width="10%"><?=$PH6_inventory_count;?></td>
						<td nowrap style="border:1px solid #000;"><?=$data_PH6_inventory_cols['product_name'];?></td>
						<td nowrap style="border:1px solid #000;"><?=$data_PH6_inventory_cols['tank_name'];?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=$data_PH6_inventory_cols['tank_capacity'];?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH6_inventory_cols['beginning_inventory'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH6_inventory_cols['sales_in_liters'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH6_inventory_cols['delivery'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH6_inventory_cols['ending_inventory'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH6_inventory_cols['book_stock'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH6_inventory_cols['variance'],2,".",",");?></td>
						
					</tr>
					<?php
					$PH6_inventory_count++;
					?>
					@endforeach
					
		</table>
		<br>
	   		<table cellspacing="0" width="100%">
			
					<tr class='report'>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;" colspan='11'>Dipstick Inventory</th>
					</tr>
					
					<tr class='report'>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">#</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Product</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Tank Name</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Tank Capacity</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Beginning Inventory</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Sales in Liters</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">UGT Pumping</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Delivery</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Ending Inventory</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Book Stock</th>
						<td style="font-size:11px; border:1px solid #000; text-align:center; background-color: #c6e0b4;">Variance</th>
					</tr>
					<?php
					$PH7_inventory_count = 1;
					?>
					@foreach ($data_PH7_inventory as $data_PH7_inventory_cols)
					<tr class="data_tr" style="text-align: center; font-size:11px;">
						
						<td nowrap style="border:1px solid #000;" width="10%"><?=$PH7_inventory_count;?></td>
						<td nowrap style="border:1px solid #000;"><?=$data_PH7_inventory_cols['product_name'];?></td>
						<td nowrap style="border:1px solid #000;"><?=$data_PH7_inventory_cols['tank_name'];?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=$data_PH7_inventory_cols['tank_capacity'];?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH7_inventory_cols['beginning_inventory'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH7_inventory_cols['sales_in_liters'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH7_inventory_cols['ugt_pumping'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH7_inventory_cols['delivery'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH7_inventory_cols['ending_inventory'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH7_inventory_cols['book_stock'],2,".",",");?></td>
						<td nowrap style="border:1px solid #000; text-align:right;"><?=number_format($data_PH7_inventory_cols['variance'],2,".",",");?></td>
						
					</tr>
					<?php
					$PH7_inventory_count++;
					?>
					@endforeach
					
		</table>
</body>
</html>
