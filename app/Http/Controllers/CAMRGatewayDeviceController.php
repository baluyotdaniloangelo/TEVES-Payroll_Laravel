<?php

namespace App\Http\Controllers;
//use Illuminate\Support\Facades\Request;

use Illuminate\Http\Request;
use App\Models\MeterDataModel;
use App\Models\GatewayModel;
use App\Models\MeterModel;

class CAMRGatewayDeviceController extends Controller
{
	/*Check Server Time*/
	public function check_time(Request $request){

    $server_time=date('Y-m-d H:i:s');
	echo $server_time;
		
	}	
	
	/*********************************************************************/
	/*Check CSV Update*/
	public function csv_update_status(Request $request){
		
		$mac_address = request()->segment(5);
		
		$data = GatewayModel::select('update_rtu')->where('gateway_mac', $mac_address)->get();
		
		echo $data[0]['update_rtu'];
		
	}
	
	/*CSV/Meter List Download*/
	public function csv_download(Request $request){
		
		$mac_address = request()->segment(5);
		$clientIpAddress = $request->ip();
		
		$gateway_info = GatewayModel::select('rtu_id','update_rtu')->where('gateway_mac', $mac_address)->get();
		
		$gatewayID = $gateway_info[0]['rtu_id'];
		$update_rtu = $gateway_info[0]['update_rtu'];
		
		if ($update_rtu==1) {	
		
		/*
		$gateway_meter_list = MeterModel::select(
        'meter_name',
        'meter_status',
		'meter_config_file',
		'meter_default_name'
		)
		->where('rtu_idx', $gatewayID)
		->where('meter_status', 'Active')
		->get();
		*/
		
		$gateway_meter_list = MeterModel::join('meter_configuration_file', 'meter_configuration_file.config_id', '=', 'meter_details.config_idx')
				->where('meter_details.rtu_idx', $gatewayID)
				->where('meter_details.meter_status', 'Active')
				->get([
					'meter_details.meter_id',
					'meter_details.meter_name',
					'meter_details.meter_default_name',
					'meter_configuration_file.config_file'
					]);		
		
		foreach ($gateway_meter_list as $meter_list_col){
	 
					 if($meter_list_col['meter_name']==$meter_list_col['meter_default_name']){
						 $meter_name = $meter_list_col['meter_name'];
						 $addressable_meter = $meter_list_col['meter_default_name'];
						 }
					 elseif($meter_list_col['meter_default_name']=='1'){
						 $meter_name = '1';
						 $addressable_meter = $meter_list_col['meter_default_name'];
						 }
					 else{
						 $meter_name = $meter_list_col['meter_name'];
						 $addressable_meter = $meter_list_col['meter_default_name'];
						 }
					 
					 echo "$meter_name,$meter_list_col[config_file],$addressable_meter\n";
				
				}

		}		

	}	
	
	/*Reset CSV/Meter List Download Status*/
	public function csv_update_status_reset(Request $request){
		
		$mac_address = request()->segment(5);
		$clientIpAddress = $request->ip();
		
		$gateway_info = GatewayModel::select('rtu_id')->where('gateway_mac', $mac_address)->get();
		
		$gatewayID = $gateway_info[0]['rtu_id'];
		
			$site = new GatewayModel();
			$site = GatewayModel::find($gatewayID);
			$site->update_rtu = 0;
			$result = $site->update();
	
	}
	
	/*********************************************************************/
	/*Check Site Code Update*/
	public function site_code_update_status(Request $request){
		
		$mac_address = request()->segment(5);
		
		$data = GatewayModel::select('update_rtu_location')->where('gateway_mac', $mac_address)->get();
		
		echo $data[0]['update_rtu_location'];
		
	}	
	
	/*Download Site Code Download Status*/
	public function site_code_download(Request $request){
		
		$mac_address = request()->segment(5);
		$clientIpAddress = $request->ip();
		
		$gateway_info = GatewayModel::select('rtu_id','update_rtu_location','site_code')->where('gateway_mac', $mac_address)->get();
		
		$gatewayID = $gateway_info[0]['rtu_id'];
		$update_rtu_location = $gateway_info[0]['update_rtu_location'];
		$_rtu_site_code = $gateway_info[0]['site_code'];
		
		if ($update_rtu_location==1) {	
		
			$rtu_site_code = '"' . $_rtu_site_code . '"';
			
			echo "location = $rtu_site_code\n";

		}		

	}	
	
	/*Reset Site Code Download Status*/
	public function site_code_update_status_reset(Request $request){
		
		$mac_address = request()->segment(5);
		$clientIpAddress = $request->ip();
		
		$gateway_info = GatewayModel::select('rtu_id')->where('gateway_mac', $mac_address)->get();
		
		$gatewayID = $gateway_info[0]['rtu_id'];
		
			$site = new GatewayModel();
			$site = GatewayModel::find($gatewayID);
			$site->update_rtu_location = 0;
			$result = $site->update();
	
	}	
	
	/*********************************************************************/
	/*SSH Status*/
	public function gateway_ssh(Request $request){
		
		$mac_address = request()->segment(5);
		
		$data = GatewayModel::select('update_rtu_ssh')->where('gateway_mac', $mac_address)->get();
		
		echo $data[0]['update_rtu_ssh'];
	
	}	 
	
	/*********************************************************************/
	/*Force Load Profile*/
	public function force_load_profile_status(Request $request){
		
		$mac_address = request()->segment(5);
		
		$data = GatewayModel::select('update_rtu_force_lp')->where('gateway_mac', $mac_address)->get();
		
		echo $data[0]['update_rtu_force_lp'];
	
	}	
	
	/*Reset Force Load Profile Status*/
	public function force_load_profile_status_reset(Request $request){
		
		$mac_address = request()->segment(5);
		$clientIpAddress = $request->ip();
		
		$gateway_info = GatewayModel::select('rtu_id')->where('gateway_mac', $mac_address)->get();
		
		$gatewayID = $gateway_info[0]['rtu_id'];
		
			$site = new GatewayModel();
			$site = GatewayModel::find($gatewayID);
			$site->update_rtu_force_lp = 0;
			$result = $site->update();
	
	}	
	
	/*********************************************************************/
	/*Save Reading of Meters*/
	public function http_post_server(Request $request){
		
	$save_to_meter_data = $request->save_to_meter_data;
	$meter_id = $request->meter_id;	
	$location = $request->location;	
	$_datetime = $request->datetime;
	$datetime = str_replace('%20',' ',$_datetime);	
	
	$vrms_a = $request->vrms_a + 0;	
	$vrms_b = $request->vrms_b + 0;	
	$vrms_c = $request->vrms_c + 0;	
	
	$irms_a = $request->irms_a + 0;	
	$irms_b = $request->irms_b + 0;	
	$irms_c = $request->irms_c + 0;	
	
	$freq = $request->freq + 0;	
	$pf = $request->pf + 0;	
	$watt = $request->watt + 0;	
	
	$va = $request->va + 0;	
	$var = $request->var + 0;	
	
	$wh_del = $request->wh_del + 0;	
	$wh_rec = $request->wh_rec + 0;	
	$wh_net = $request->wh_net + 0;	
	$wh_total = $request->wh_total + 0;	
	
	$varh_neg = $request->varh_neg + 0;
	$varh_pos = $request->varh_pos + 0;	
	$varh_net = $request->varh_net + 0;
	$varh_total = $request->varh_total + 0;
	$vah_total = $request->vah_total + 0;
	
	$max_rec_kw_dmd = $request->max_rec_kw_dmd + 0;
	$max_rec_kw_dmd_time = $request->max_rec_kw_dmd_time;
	$max_del_kw_dmd = $request->max_del_kw_dmd + 0;
	$max_del_kw_dmd_time = $request->max_del_kw_dmd_time;
	
	$max_pos_kvar_dmd = $request->max_pos_kvar_dmd + 0;
	$max_pos_kvar_dmd_time = $request->max_pos_kvar_dmd_time;
	$max_neg_kvar_dmd = $request->max_neg_kvar_dmd + 0;
	$max_neg_kvar_dmd_time = $request->max_neg_kvar_dmd_time;
	
	$v_ph_angle_a = $request->v_ph_angle_a + 0;
	$v_ph_angle_b = $request->v_ph_angle_b + 0;
	$v_ph_angle_c = $request->v_ph_angle_c + 0;
	
	$i_ph_angle_a = $request->i_ph_angle_a + 0;
	$i_ph_angle_b = $request->i_ph_angle_b + 0;
	$i_ph_angle_c = $request->i_ph_angle_c + 0;
	
	$relay_status = $request->relay_status + 0;
	
	$gateway_mac = $request->gateway_mac;
	$soft_rev = $request->soft_rev;
		
	/*Save to Meter Data*/	
	if($save_to_meter_data == 1){
			
	$MeterData = new MeterDataModel();
			
	$MeterData->location 					= $location;
	$MeterData->meter_id 					= $meter_id;
	$MeterData->datetime 					= $datetime;
	$MeterData->vrms_a 						= $vrms_a;
	$MeterData->vrms_b 						= $vrms_b;
	$MeterData->vrms_c 						= $vrms_c;
	$MeterData->irms_a 						= $irms_a;
	$MeterData->irms_b 						= $irms_b;
	$MeterData->irms_c 						= $irms_c;
	$MeterData->freq 						= $freq;
	$MeterData->pf 							= $pf;
	$MeterData->watt 						= $watt;
	$MeterData->va 							= $va;
	$MeterData->var 						= $var;
	$MeterData->wh_del 						= $wh_del;
	$MeterData->wh_rec 						= $wh_rec;
	$MeterData->wh_net 						= $wh_net;
	$MeterData->wh_total 					= $wh_total;
	$MeterData->varh_neg 					= $varh_neg;
	$MeterData->varh_pos 					= $varh_pos;
	$MeterData->varh_net 					= $varh_net;
	$MeterData->varh_total 					= $varh_total;
	$MeterData->vah_total 					= $vah_total;
	$MeterData->max_rec_kw_dmd 				= $max_rec_kw_dmd;
	$MeterData->max_rec_kw_dmd_time 		= $max_rec_kw_dmd_time;
	$MeterData->max_del_kw_dmd 				= $max_del_kw_dmd;
	$MeterData->max_del_kw_dmd_time 		= $max_del_kw_dmd_time;
	$MeterData->max_pos_kvar_dmd 			= $max_pos_kvar_dmd;
	$MeterData->max_pos_kvar_dmd_time 		= $max_pos_kvar_dmd_time;
	$MeterData->max_neg_kvar_dmd 			= $max_neg_kvar_dmd;
	$MeterData->max_neg_kvar_dmd_time		= $max_neg_kvar_dmd_time;
	$MeterData->v_ph_angle_a 				= @$v_ph_angle_a;
	$MeterData->v_ph_angle_b 				= @$v_ph_angle_b;
	$MeterData->v_ph_angle_c 				= @$v_ph_angle_c;
	$MeterData->i_ph_angle_a 				= @$i_ph_angle_a;
	$MeterData->i_ph_angle_b 				= @$i_ph_angle_b;
	$MeterData->i_ph_angle_c 				= @$i_ph_angle_c;
	$MeterData->gateway_mac 					= @$gateway_mac;
	$MeterData->soft_rev 					= @$soft_rev;
	$MeterData->relay_status 				= @$relay_status;
			
	$result = $MeterData->save();

    $server_time=date('Y-m-d H:i:s');
	echo "OK, $server_time";	
	
	}
	/*Save to Live Meter Data*/
	else{
		
	$MeterData = new MeterDataModel();
	$MeterData->makeHidden(['gateway_mac']);
					
	$MeterData->location 					= $location;
	$MeterData->meter_id 					= $meter_id;
	$MeterData->datetime 					= $datetime;
	$MeterData->vrms_a 						= $vrms_a;
	$MeterData->vrms_b 						= $vrms_b;
	$MeterData->vrms_c 						= $vrms_c;
	$MeterData->irms_a 						= $irms_a;
	$MeterData->irms_b 						= $irms_b;
	$MeterData->irms_c 						= $irms_c;
	$MeterData->freq 						= $freq;
	$MeterData->pf 							= $pf;
	$MeterData->watt 						= $watt;
	$MeterData->va 							= $va;
	$MeterData->var 						= $var;
	$MeterData->wh_del 						= $wh_del;
	$MeterData->wh_rec 						= $wh_rec;
	$MeterData->wh_net 						= $wh_net;
	$MeterData->wh_total 					= $wh_total;
	$MeterData->varh_neg 					= $varh_neg;
	$MeterData->varh_pos 					= $varh_pos;
	$MeterData->varh_net 					= $varh_net;
	$MeterData->varh_total 					= $varh_total;
	$MeterData->vah_total 					= $vah_total;
	$MeterData->max_rec_kw_dmd 				= $max_rec_kw_dmd;
	$MeterData->max_rec_kw_dmd_time 		= $max_rec_kw_dmd_time;
	$MeterData->max_del_kw_dmd 				= $max_del_kw_dmd;
	$MeterData->max_del_kw_dmd_time 		= $max_del_kw_dmd_time;
	$MeterData->max_pos_kvar_dmd 			= $max_pos_kvar_dmd;
	$MeterData->max_pos_kvar_dmd_time 		= $max_pos_kvar_dmd_time;
	$MeterData->max_neg_kvar_dmd 			= $max_neg_kvar_dmd;
	$MeterData->max_neg_kvar_dmd_time		= $max_neg_kvar_dmd_time;
	$MeterData->v_ph_angle_a 				= $v_ph_angle_a;
	$MeterData->v_ph_angle_b 				= $v_ph_angle_b;
	$MeterData->v_ph_angle_c 				= $v_ph_angle_c;
	$MeterData->i_ph_angle_a 				= $i_ph_angle_a;
	$MeterData->i_ph_angle_b 				= $i_ph_angle_b;
	$MeterData->i_ph_angle_c 				= $i_ph_angle_c;
	$MeterData->soft_rev 					= $soft_rev;
	$MeterData->relay_status 				= $relay_status;
			
	$result = $MeterData->save();

    $server_time=date('Y-m-d H:i:s');
	echo "OK, $server_time";	
		
	}
	
   
	
	}
    
}
