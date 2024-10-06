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
				$_billing_date=date_create($receivable_data[0]['billing_date']);
				$billing_date = strtoupper(date_format($_billing_date,"M/d/Y"));
			?>
			
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%"><img src="{{public_path('client_logo/logo.jpg')}}" style="width:112px;"></td>
			<td colspan="6" width="30%" style="horizontal-align:center;text-align:left;"><b style="font-size:18px;">TEVES GASOLINE STATION</b></td>
			<td colspan="3" nowrap align="center" width="60%" style="font-size:12px; background-color: purple; text-align:center; font-weight:bold; color:#fff; border-top-left-radius:30px;border-bottom-left-radius:30px; width:50px"><b>{{ $title }}</b></td>
		</tr>
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;">San Juan, Madrid Surigao del Sur</div>
			</td>
			<td colspan="3" align="left" width="20%" style="font-size:11px; font-weight:bold;;"><b>BILLING DATE</b></td>
			<td colspan="3" align="left" width="30%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon"><?=$billing_date;?></td>
		</tr>		
		<?php
				$_print_date=date_create(date('Y-m-d'));
				$print_date = strtoupper(date_format($_print_date,"M/d/Y"));
		?>
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;">VAT REG. TIN : 740-213-285-001</div>
			</td>
			<td colspan="3" align="left" width="25%" style="font-size:11px; font-weight:bold;"><b>DATE PRINTED</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon"><?=$print_date;?></td>
		</tr>
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;">GLEZA F. TEVES - Proprietress</div>
			</td>
			<td colspan="3" align="left" width="25%" style="font-size:11px; font-weight:bold;"><b>AR REFERENCE</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:11px; border-bottom:solid 1px gray;" class="td_colon">{{ $receivable_data[0]['control_number'] }}</td>
		</tr>
		<tr>
			<td colspan="10"  width="50%" style="horizontal-align:center;text-align:left;"></td>
		</tr>
		
		<tr>
			<td colspan="10"  width="50%" style="horizontal-align:center;text-align:left;"></td>
		</tr>
		

		<tr>
			<td colspan="10"  width="50%" style="horizontal-align:center;text-align:left;"></td>
		</tr>
		
		</table>