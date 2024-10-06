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
				$logo = $receivable_header['branch_logo'];
			?>
			
		<tr>
			<td nowrap style="horizontal-align:top;text-align:left;" align="center" colspan="1" rowspan="4" width="10%">
			<img src="{{public_path('client_logo/')}}<?=$logo;?>" style="width:112px;">
			</td>
			<td colspan="7" width="40%" style="horizontal-align:center;text-align:left;"><b style="font-size:18px;"><?=$receivable_header['branch_name'];?></b></td>
			<td colspan="2" nowrap align="left" width="50%" style="font-size:12px; background-color: yellowgreen; text-align:center; font-weight:bold; color:#000; border-top-left-radius:30px;border-bottom-left-radius:30px;"><b>{{ $title }}</b></td>
		</tr>
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;"><?=$receivable_header['branch_address'];?></div>
			</td>
			<td colspan="3" align="left" width="20%" style="font-size:12px; font-weight:bold; color:red;"><b>CONTROL NO.</b></td>
			<td colspan="3" align="left" width="30%" style="font-size:12px; color:red; border-bottom:solid 1px gray;" class="td_colon">{{ $receivable_data[0]['control_number'] }}</td>
		</tr>		
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;">VAT REG. TIN : <?=$receivable_header['branch_tin'];?></div>
			</td>
			<td colspan="3" align="left" width="20%" style="font-size:12px; font-weight:bold;;"><b>BILLING DATE</b></td>
			<td colspan="3" align="left" width="30%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon"><?=$billing_date;?></td>
		</tr>
		
		<tr>
			<td colspan="3"  width="40%" style="horizontal-align:center;text-align:left;">
			<div style="font-size:10px;"><?=$receivable_header['branch_owner'];?> - <?=$receivable_header['branch_owner_title'];?></div>
			</td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; font-weight:bold;"><b>PAYMENT TERM</b></td>
			<td colspan="3" align="left" width="25%" style="font-size:12px; border-bottom:solid 1px gray;" class="td_colon">{{ $receivable_data[0]['payment_term'] }}</td>
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
		<br>
		<table class="" width="100%" cellspacing="0" cellpadding="1" >	
		
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="20%"><b>ACCOUNT NAME</b></td>	
			<td colspan="9" align="left" width="80%" style=" border-bottom:solid 1px gray;" class="td_colon">{{ $receivable_data[0]['client_name'] }}</td>	
		</tr>
		
		<tr style="font-size:12px;">
			<td colspan="1" align="left" width="20%"><b>TIN</b></td>	
			<td colspan="9" align="left" width="80%" style=" border-bottom:solid 1px gray;" class="td_colon">{{ $receivable_data[0]['client_tin'] }}</td>	
		</tr>
		
		<tr style="font-size:12px;">		
			<td colspan="1" align="left" width="20%"><b>ADDRESS</b></td>	
			<td colspan="9" align="left" width="80%" style=" border-bottom:solid 1px gray;" class="td_colon">{{ $receivable_data[0]['client_address'] }}</td>			
		</tr>

		<tr style="font-size:12px;">
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" style="table-layout:fixed;">
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr>
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="6" align="center" style="border:0px solid #000;  background-color: #c6e0b4; font-weight:bold; height:25px !important;">DESCRIPTION</td>		
			<td colspan="4" nowrap align="center" style="border-left:1px solid gray;    background-color: #c6e0b4; font-weight:bold;">AMOUNT</td>		
		</tr>									
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" nowrap align="center" style="border:0px solid gray; background-color: #c6e0b4; font-weight:bold; height:5px !important; "></td>
		</tr>
		<tr style="font-size:12px;">
			
			<td colspan="6" align="left" style="border-left:0px solid #000;height: 140px; padding:10px;">{{ $receivable_data[0]['receivable_description'] }}</td>
			<td colspan="4" align="center" style="border-right:0px solid #000;height: 140px;border-bottom:solid 1px gray;"><?=number_format($receivable_data[0]['receivable_amount'],2);?></td>			
		
		</tr>
		
		<tr style="font-size:12px;">
			<td colspan="3" align="right" style="border-left: 0px solid #000;"></td>
			<td colspan="3" align="center" style="background-color: #c6e0b4; font-weight:bold; height:25px !important;">TOTAL DUE </td>
			<td colspan="4" align="center" style="background-color: #c6e0b4; border-right: 0px solid #000; border-bottom:double;"><span style="font-family: DejaVu Sans; sans-serif;">&#8369;</span> 
			<?=number_format($receivable_data[0]['receivable_amount'],2);?>
		</tr>		

		<tr>
			<td colspan="10" style="border-left:0px solid #000;border-right:0px solid #000;border-bottom:0px solid #000;">&nbsp;</td>
		</tr>
		
		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" align="center" style="border-top:1px solid gray; border-left:0px solid #000; border-right:0px solid #000; border-bottom:1px solid gray; height:50px !important;font-style: italic;">In Words</td>
			<td colspan="8" style="border-top:1px solid gray; border-bottom:1px solid gray; text-align:center;">&nbsp;<?php echo strtoupper($amount_in_words); ?></td>
		</tr>
		
		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
		
		<tr style="font-size:12px;border:0px solid #000;font-style: italic;">
			<td colspan="10" align="left"style="border-top:1px solid gray; border-bottom:1px solid gray; height:50px !important;">
			By signing below, you acknowledge to have a pending payable as stated above. And you receive the</br>
			<b>ORIGINAL ATTACHMENTS SUBJECT FOR RE-CHECKING</b>
			</td>
		</tr>
		
		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>	
		</table>
		
		<table class="" width="100%" cellspacing="0" cellpadding="1" style="table-layout:fixed;">		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" align="center" style="border:1px solid gray; font-weight:bold; background-color: #c6e0b4; height:25px !important;">MODE OF PAYMENT</td>			
		</tr>		
		 
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="5" align="center" style="border-bottom:1px solid gray; border-left: 1px solid gray; font-weight:bold; height:25px !important;">Check / Bank Deposit</td>		
			<td colspan="5" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray; font-weight:bold;">Cash</td>		
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" nowrap align="left" style="border-left:1px solid gray; height:25px !important;">Check Name :</td>	
			<td colspan="3" align="center" style="border-bottom:1px solid gray;"></td>				
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray;">Denomination</td>
			<td colspan="1" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;">Quantity</td>
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;">Amount</td>			
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" nowrap align="left" style="border-left:1px solid gray; height:25px !important;">Check Amount :</td>	
			<td colspan="3" align="center" style="border-bottom:1px solid gray;"></td>				
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray;"></td>
			<td colspan="1" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>	
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" nowrap align="left" style="border-left:1px solid gray; height:25px !important;">Check Date :</td>	
			<td colspan="3" align="center" style="border-bottom:1px solid gray;"></td>				
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray;"></td>
			<td colspan="1" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>		
		</tr>

		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" nowrap align="left" style="border-left:1px solid gray; height:25px !important;">Check No. :</td>	
			<td colspan="3" align="center" style="border-bottom:1px solid gray;"></td>				
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray;"></td>
			<td colspan="1" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>		
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" align="left" style="border-left:1px solid gray; height:25px !important;">Bank :</td>	
			<td colspan="3" align="center" style="border-bottom:1px solid gray;"></td>				
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray;"></td>
			<td colspan="1" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>			
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="2" nowrap align="left" style="border-left:1px solid gray; height:25px !important;">Bank Address/Branch :</td>	
			<td colspan="3" align="center" style="border-bottom:1px solid gray;"></td>				
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray;"></td>
			<td colspan="1" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>			
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="1" align="left" style="border-left:1px solid gray; height:25px !important;">&nbsp;</td>	
			<td colspan="4" align="center" style="border-bottom:0px solid #000;"></td>				
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:1px solid gray; border-right:1px solid gray;"></td>
			<td colspan="1" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>
			<td colspan="2" nowrap align="center" style="border-bottom:1px solid gray; border-left:0px solid #000; border-right:1px solid gray;"></td>		
		</tr>
		
		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="5" align="left" style="border-left:1px solid gray;border-top:1px solid gray; height:25px !important;">For withheld client :</td>	
			<td colspan="3" nowrap align="center" style="border-bottom:0px solid #000; border-left:1px solid gray; border-right:1px solid gray; font-weight:bold;">TOTAL CASH</td>			
			<td colspan="2" nowrap align="center" style="border-bottom:0px solid #000; border-left:0px solid #000; border-right:1px solid gray;"></td>			
		</tr>

		<tr style="font-size:12px;border:0 solid #000;">
			<td colspan="10" align="left" style="border:1px solid gray; font-weight:bold; height:25px !important;">TOTAL PAYMENT :</td>		
		</tr>
		
		<tr>
			<td colspan="10" style="height:5.66px !important;"></td>
		</tr>
	  
		<tr style="font-size:12px;">
			<td colspan="3" align="left" style="border-top:1px solid gray;border-right:1px solid gray;  border-left:1px solid gray; font-style: italic; height:25px !important;">&nbsp;Billing Prepared by:</td>	
			<td colspan="3" nowrap align="left" style="border-top:1px solid gray;border-right:1px solid gray; font-style: italic;">&nbsp;Billing Released by:</td>			
			<td colspan="4" nowrap align="left" style="border-top:1px solid gray; border-right:1px solid gray; font-style: italic;">&nbsp;Billing Received by:</td>			
		</tr>
			
		<tr>
			<td colspan="3" style="border-left:1px solid gray; border-right:1px solid gray;">&nbsp;</td>	
			<td colspan="3" style="border-right:1px solid gray;">&nbsp;</td>			
			<td colspan="4" style="border-right:1px solid gray;">&nbsp;</td>	
		</tr>
		
		<tr>
			<td colspan="3" style="border-left:1px solid gray; border-right:1px solid gray;">&nbsp;</td>	
			<td colspan="3" style="border-right:1px solid gray;">&nbsp;</td>			
			<td colspan="4" style="border-right:1px solid gray;">&nbsp;</td>	
		</tr>
		
		<tr style="font-size:12px;">
			<td colspan="3" align="center" style="border-left:1px solid gray; border-right:1px solid gray; text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$user_data->user_real_name}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
			<td colspan="3" align="center" style="border-left:0px solid #000; border-right:1px solid gray; text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>				
			<td colspan="4" align="center" style="border-left:0px solid #000; border-right:1px solid gray; text-decoration:underline;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>	
		</tr>
	
		<tr style="font-size:12px;">
			<td colspan="3" align="center" style="border-left:1px solid gray; border-right:1px solid gray;">{{$user_data->user_job_title}}</td>	
			<td colspan="3" align="center" style="border-right:1px solid gray;">Office Staff</td>			
			<td colspan="4" align="center" style="border-right:1px solid gray;">Signature over Printed Name</td>	
		</tr>
	
		<tr style="font-size:12px;">
			<td colspan="3" align="center" style="border-left:1px solid gray; border-right:1px solid gray;"></td>	
			<td colspan="3" align="center" style="border-right:1px solid gray;"></td>			
			<td colspan="4" align="left" style="border-right:1px solid gray;">&nbsp;Date    :</td>	
		</tr>
		
		<tr style="font-size:12px;">
			<td colspan="3" align="center" style="border-left:1px solid gray; border-right:1px solid gray;border-bottom:1px solid gray;"></td>	
			<td colspan="3" align="center" style="border-right:1px solid gray;border-bottom:1px solid gray;"></td>			
			<td colspan="4" align="left" style="border-right:1px solid gray;border-bottom:1px solid gray;">&nbsp;Time    :</td>	
		</tr>
		
		</table>
		
</body>
</html>