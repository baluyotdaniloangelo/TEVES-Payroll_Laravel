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
				$_billing_date=date_create($so_data[0]['billing_date']);
				$billing_date = strtoupper(date_format($_billing_date,"M/d/Y"));
				$logo = 1;
			?>
			
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%">
				<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:20px;">
			</td>
			<td colspan="9" width="30%" style="horizontal-align:center;text-align:left;"><b style="font-size:10px;"><?=$so_header['branch_name'];?></b></td>
					</tr>
		
		<tr>
			<td colspan="9"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;"><?=$so_header['branch_address'];?></div>
			</td>
			
		</tr>		
		<?php
				$_print_date=date_create(date('Y-m-d'));
				$print_date = strtoupper(date_format($_print_date,"M/d/Y"));
		?>
		<tr>
			<td colspan="9"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;">VAT REG. TIN : <?=$so_header['branch_tin'];?></div>
			</td>
		</tr>
		
		
		
		</table>

		<br>
		<table class="" width="100%" cellspacing="0" cellpadding="1" >	
		<?php
				$_billing_date=date_create($so_data[0]['billing_date']);
				$billing_date = strtoupper(date_format($_billing_date,"M/d/Y"));
		?>
		<tr style="font-size:8px;">
			<td colspan="1" align="left" width="10%"><b>ACCOUNT NAME</b></td>	
			<td colspan="9" align="left" width="90%" style="border-bottom:solid 1px gray;" class="td_colon">{{ $so_data[0]['client_name'] }}</td>		
		</tr>
		<?php
				$_print_date=date_create(date('Y-m-d'));
				$print_date = strtoupper(date_format($_print_date,"M/d/Y"));
		?>
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="10%"><b>TIN</b></td>	
			<td colspan="9" align="left" width="90%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $so_data[0]['client_tin'] }}</td>
		</tr>
		
		<tr style="font-size:12px;">		
			<td colspan="1" align="left" width="20%"><b>ADDRESS</b></td>	
			<td colspan="9" align="left" width="80%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $so_data[0]['client_address'] }}</td>			
		</tr>

		<tr style="font-size:12px;">
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		</table>
		

		
</body>
</html>