<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\MeterModel;
use App\Models\ConfigurationFileModel;
use App\Models\GatewayModel;
use App\Models\SiteModel;

use App\Models\BuildingModel;
use App\Models\MeterLocationModel;

use Session;
use Validator;
use DataTables;

use Illuminate\Support\Facades\DB;

class CAMRMeterController extends Controller
{

	/*Fetch Gateway List using Datatable*/
	public function getMeter(Request $request)
    {

		$siteID = $request->siteID;
		$location_id 	= $request->location_id;
		
		if ($request->ajax()) {	
		
		$data = MeterModel::join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_details.location_idx')
						->join('meter_rtu', 'meter_rtu.rtu_id', '=', 'meter_details.rtu_idx')
						->join('meter_configuration_file', 'meter_configuration_file.config_id', '=', 'meter_details.config_idx')
						->where('meter_details.site_idx', $siteID)
						->where(function ($r) use($location_id) {
							if ($location_id) {
									$r->where('meter_details.location_idx', $location_id);
							}
						})
						->get([
							'meter_details.meter_id',
							'meter_details.meter_name',
							'meter_details.customer_name',
							'meter_details.meter_role',
							'meter_details.meter_status',
							'meter_details.last_log_update',
							'meter_details.meter_status',
							'meter_details.meter_remarks',
							'meter_details.meter_default_name',
							'meter_location_table.location_description',
							'meter_configuration_file.config_file',
							'meter_rtu.gateway_sn',
							'meter_location_table.location_code',
							'meter_location_table.location_description']);		

		return DataTables::of($data)
				->addIndexColumn()
	
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_gateway">
					<!--<a href="#" data-id="'.$row->meter_id.'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view" id="ViewMeter"></a>-->
					<a href="#" data-id="'.$row->meter_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="EditMeter"></a>
					<a href="#" data-id="'.$row->meter_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="DeleteMeter"></a>
					</div>';
                    return $actionBtn;
					
                })
				
				->addColumn('status', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);
						
						/*SERVER DATETIME*/
						$_server_time=date('Y-m-d H:i:s');
						$server_time_current = strtotime($_server_time);
						
						$date1=date_create("$_server_time");
						$date2=date_create("$date_format");
								
						$diff					= date_diff($date1,$date2);
						$days_last_active 		= $diff->format("%a");
						
						if($last_log_update == "0000/00/00 00:00"){
							$statusBtn = '<div style="color:black; font-weight:bold; text-align:center;" title="No Meter Connected on the Gateway/Spare">No Data</div>';
						}
						else if($diff->format("%a")<=0){
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-check-fill btn_icon_table btn_icon_table_status_online" title="Last Status Update : '.$last_log_update.'"></a>';
						}else{
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-slash-fill btn_icon_table btn_icon_table_status_offline" title="Offline Since : '.$last_log_update.'"></a>';
						}		
										
					$actionBtn = '
					<div align="center" class="row_status_table_gateway">
					'.$statusBtn.'
					</div>';
                    return $actionBtn;
                })
				
				->rawColumns(['update','status','action'])
                ->make(true);	
		}		
    }

	/*Fetch Gateway List using Datatable*/
	public function getMetersPerGateway(Request $request)
    {

		$gatewayID = $request->gatewayID;
		if ($request->ajax()) {	
		
		$data = MeterModel::join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_details.location_idx')
						->join('meter_configuration_file', 'meter_configuration_file.config_id', '=', 'meter_details.config_idx')
						->where('meter_details.rtu_idx', $gatewayID)
						->get([
							'meter_details.meter_id',
							'meter_details.meter_name',
							'meter_details.customer_name',
							'meter_details.meter_role',
							'meter_details.meter_status',
							'meter_details.last_log_update',
							'meter_details.meter_status',
							'meter_details.meter_remarks',
							'meter_details.meter_default_name',
							'meter_location_table.location_description',
							'meter_configuration_file.config_file',
							'meter_location_table.location_code',
							'meter_location_table.location_description']);		
		
		return DataTables::of($data)
				->addIndexColumn()
	
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_gateway">
					<a href="' . url('gateway_details/'.$row->meter_id) .'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view"></a>
					<a href="#" data-id="'.$row->meter_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="EditMeter"></a>
					<a href="#" data-id="'.$row->meter_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="DeletMeter"></a>
					</div>';
                    return $actionBtn;
					
                })
				
				->addColumn('status', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);
						
						/*SERVER DATETIME*/
						$_server_time=date('Y-m-d H:i:s');
						$server_time_current = strtotime($_server_time);
						
						$date1=date_create("$_server_time");
						$date2=date_create("$date_format");
								
						$diff					= date_diff($date1,$date2);
						$days_last_active 		= $diff->format("%a");
						
						if($last_log_update == "0000/00/00 00:00"){
							$statusBtn = '<div style="color:black; font-weight:bold; text-align:center;" title="No Meter Connected on the Gateway/Spare">No Data</div>';
						}
						else if($diff->format("%a")<=0){
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-check-fill btn_icon_table btn_icon_table_status_online" title="Last Status Update : '.$last_log_update.'"></a>';
						}else{
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-slash-fill btn_icon_table btn_icon_table_status_offline" title="Offline Since : '.$last_log_update.'"></a>';
						}		
										
					$actionBtn = '
					<div align="center" class="row_status_table_gateway">
					'.$statusBtn.'
					</div>';
                    return $actionBtn;
                })
				
				->rawColumns(['update','status','action'])
				
                ->make(true);	
		}		
    }


	/*Fetch Gateway List using Datatable*/
	public function getOfflineMeter(Request $request)
    {

		$siteID = $request->siteID;
		
		if ($request->ajax()) {	

		$raw_query_offline = "SELECT
						`meter_details`.`meter_id`,
						`meter_details`.`meter_name`,
						`meter_details`.`customer_name`,
						`meter_details`.`meter_role`,
						`meter_details`.`meter_status`,
						`meter_details`.`last_log_update`,
						`meter_details`.`meter_status`,
						`meter_details`.`meter_remarks`,
						`meter_details`.`meter_default_name`,
						`meter_configuration_file`.`config_file`,
						`meter_location_table`.`location_code`,
						`meter_location_table`.`location_description`,
						`meter_rtu`.`gateway_sn`
					from meter_details
						left join `meter_location_table` on `meter_location_table`.`location_id` = `meter_details`.`location_idx`
						left join `meter_rtu` on `meter_rtu`.`rtu_id` = `meter_details`.`rtu_idx`
						left join `meter_configuration_file` on `meter_configuration_file`.`config_id` = `meter_details`.`config_idx`
						where `meter_details`.`site_idx` = ?
						and DATEDIFF(NOW(), meter_details.last_log_update) >= 1 or (meter_details.last_log_update = '0000-00-00 00:00:00' AND meter_details.site_idx = ?)";	
					   
		$offline_meter_data = DB::select("$raw_query_offline", [$siteID,$siteID]);

		return DataTables::of($offline_meter_data)
				->addIndexColumn()
	
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_gateway">
					<!--<a href="#" data-id="'.$row->meter_id.'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view" id="ViewMeter"></a>-->
					<a href="#" data-id="'.$row->meter_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="EditMeter"></a>
					<a href="#" data-id="'.$row->meter_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="DeleteMeter"></a>
					</div>';
                    return $actionBtn;
					
                })
				
				->addColumn('status', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);
						
						/*SERVER DATETIME*/
						$_server_time=date('Y-m-d H:i:s');
						$server_time_current = strtotime($_server_time);
						
						$date1=date_create("$_server_time");
						$date2=date_create("$date_format");
								
						$diff					= date_diff($date1,$date2);
						$days_last_active 		= $diff->format("%a");
						
						if($last_log_update == "0000/00/00 00:00"){
							$statusBtn = '<div style="color:black; font-weight:bold; text-align:center;" title="No Meter Connected on the Gateway/Spare">No Data</div>';
						}
						else if($diff->format("%a")<=0){
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-check-fill btn_icon_table btn_icon_table_status_online" title="Last Status Update : '.$last_log_update.'"></a>';
						}else{
							$statusBtn = '<a href="#" class="btn-circle btn-sm bi bi-cloud-slash-fill btn_icon_table btn_icon_table_status_offline" title="Offline Since : '.$last_log_update.'"></a>';
						}		
										
					$actionBtn = '
					<div align="center" class="row_status_table_gateway">
					'.$statusBtn.'
					</div>';
                    return $actionBtn;
                })
				
				->rawColumns(['update','status'])
                ->make(true);	
		}		
    }

	/*Fetch Meter Information*/
	public function meter_info(Request $request){

		
		$meterID = $request->meterID;
		
		$raw_query_meter_info = "SELECT
						`meter_details`.`meter_id`,
						`meter_details`.`meter_name`,
						`meter_details`.`customer_name`,
						`meter_details`.`meter_role`,
						`meter_details`.`meter_status`,
						`meter_details`.`last_log_update`,
						`meter_details`.`meter_status`,
						`meter_details`.`meter_remarks`,
						`meter_details`.`meter_name_addressable`,
						`meter_details`.`meter_default_name`,
						`meter_details`.`meter_multiplier`,
						`meter_details`.`meter_type`,
						`meter_details`.`meter_brand`,
						`meter_details`.`config_idx`,
						`meter_configuration_file`.`config_file`,
						`meter_details`.`location_idx`,
						`meter_location_table`.`location_code`,
						`meter_location_table`.`location_description`,
						`meter_details`.`rtu_idx`,
						`meter_rtu`.`gateway_sn`
					from meter_details
						left join `meter_location_table` on `meter_location_table`.`location_id` = `meter_details`.`location_idx`
						left join `meter_rtu` on `meter_rtu`.`rtu_id` = `meter_details`.`rtu_idx`
						left join `meter_configuration_file` on `meter_configuration_file`.`config_id` = `meter_details`.`config_idx`
						where `meter_details`.`meter_id` = ?";	
					   
		$data = DB::select("$raw_query_meter_info", [$meterID]);
		
		return response()->json($data);
		
	}

	/*Delete Meter Information*/
	public function delete_meter_confirmed(Request $request){

		$meterID = $request->meterID;
		
		$raw_query_meter_info = "SELECT `rtu_idx` from meter_details where `meter_id` = ?";	
					   
		$meter_info_data = DB::select("$raw_query_meter_info", [$meterID]);

		// $meterID = $request->meterID;
		MeterModel::find($meterID)->delete();
		
			/*Enable CSV Update*/
			$enable_csv = new GatewayModel();
			$enable_csv = GatewayModel::find($meter_info_data[0]->rtu_idx);
			$enable_csv->update_rtu = 1;
			$result_enable_csv = $enable_csv->update();
		
		return 'Deleted';
		
	} 
 
	public function create_meter_post(Request $request){
		
		$request->validate([
          'meter_name'      	=> ['required',Rule::unique('meter_details')->where( 
									fn ($query) =>$query
										->where('meter_name', $request->meter_name)
										->where('site_idx', $request->siteID) 
									)],
		  'meter_model_id'   	=> 'required',
		  'meter_default_name'  => 'required',
		  'rtu_sn_number_id'   	=> 'required',
		  'location_id'   	=> 'required',
        ], 
        [
			'meter_name.required' => 'Meter Description/Serial Number is Required',
			'meter_model_id.required' => 'Configuration file is Required',
			'meter_default_name.required' => 'Alternate Address is Required',
			'rtu_sn_number_id.required' => 'Gateway is Required',
			'location_id.required' => 'Area/EE Room is Required'
        ]
		);

			#insert Meter	
			$meter = new MeterModel();
			$meter->makeHidden(['meter_load_profile', 'last_log_update', 'last_wh_total', 'soft_rev', 'meter_reading']);
			
			$meter->site_idx 						= $request->siteID;		
			$meter->site_code 						= $request->site_code;			
			$meter->meter_name 						= $request->meter_name;
			$meter->meter_name_addressable 			= $request->meter_name_addressable;
			$meter->meter_default_name 				= $request->meter_default_name;
			$meter->customer_name 					= $request->customer_name;
			$meter->config_idx		 				= $request->meter_model_id;
			$meter->meter_type 						= $request->meter_type;
			$meter->meter_brand 					= $request->meter_brand;
			$meter->meter_multiplier 				= $request->meter_multiplier;
			$meter->meter_role 						= $request->meter_role;
			$meter->location_idx 					= $request->location_id;
			$meter->rtu_idx			 				= $request->rtu_sn_number_id;
			$meter->meter_status 					= $request->meter_status;
			$meter->meter_remarks 					= $request->meter_remarks;	
			$meter->created_by_user_idx 			= Session::get('loginID');
			
			$result = $meter->save();
			
			/*Enable CSV Update*/
			$enable_csv = new GatewayModel();
			$enable_csv = GatewayModel::find($request->rtu_sn_number_id);
			$enable_csv->update_rtu = 1;
			$result_enable_csv = $enable_csv->update();
			
			if($result){
				return response()->json(['success'=>'Meter Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Meter Information']);
			}
	}
	
	public function update_meter_post(Request $request){
		
		$request->validate([
          'meter_name'      	=> ['required',Rule::unique('meter_details')->where( 
									fn ($query) =>$query
										->where('meter_name', $request->meter_name)
										->where('site_idx', $request->siteID) 
										->where('meter_id', '<>',  $request->meterID )
									)],
		  'meter_model_id'   	=> 'required',
		  'meter_default_name'  => 'required',
		  'rtu_sn_number_id'   	=> 'required',
		  'location_id'   		=> 'required',
        ], 
        [
			'meter_name.required' => 'Meter Description/Serial Number is Required',
			'meter_model_id.required' => 'Configuration file is Required',
			'meter_default_name.required' => 'Alternate Address is Required',
			'rtu_sn_number_id.required' => 'Gateway is Required',
			'location_id.required' => 'Area/EE Room is Required'
        ]
		);

			#update Meter	
			$meter = new MeterModel();
			$meter = MeterModel::find($request->meterID);
			$meter->makeHidden(['meter_load_profile', 'last_log_update', 'soft_rev']);
			
			$meter->site_code 						= $request->site_code;	
			$meter->meter_name 						= $request->meter_name;
			$meter->meter_name_addressable 			= $request->meter_name_addressable;
			$meter->meter_default_name 				= $request->meter_default_name;
			$meter->customer_name 					= $request->customer_name;
			$meter->config_idx		 				= $request->meter_model_id;
			$meter->meter_type 						= $request->meter_type;
			$meter->meter_brand 					= $request->meter_brand;
			$meter->meter_multiplier 				= $request->meter_multiplier;
			$meter->meter_role 						= $request->meter_role;
			$meter->location_idx 					= $request->location_id;
			$meter->rtu_idx			 				= $request->rtu_sn_number_id;
			$meter->meter_status 					= $request->meter_status;
			$meter->meter_remarks 					= $request->meter_remarks;	
			$meter->modified_by_user_idx 			= Session::get('loginID');
					
			$result = $meter->update();
			
			/*Enable CSV Update*/
			$enable_csv = new GatewayModel();
			$enable_csv = GatewayModel::find($request->rtu_sn_number_id);
			$enable_csv->update_rtu = 1;
			$result_enable_csv = $enable_csv->update();
			
			
			if($result){
				return response()->json(['success'=>'Meter Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Updated Meter Information']);
			}
	}
	/*End of Controller*/
}