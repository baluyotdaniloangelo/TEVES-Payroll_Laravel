<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\GatewayModel;
use App\Models\MeterModel;
use App\Models\ConfigurationFileModel;
use App\Models\SiteModel;

use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;

class CAMRGatewayController extends Controller
{

	/*Fetch Gateway List using Datatable*/
	public function getGateway(Request $request)
    {

		$siteID = $request->siteID;
		$gateway = GatewayModel::get();
		if ($request->ajax()) {	
		
		$data = GatewayModel::join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_rtu.location_idx')
						->where('site_idx', $siteID)
						->get([
						'meter_rtu.rtu_id',
						'meter_rtu.gateway_sn',
						'meter_rtu.gateway_mac',
						'meter_rtu.gateway_ip',
						'meter_rtu.update_rtu',
						'meter_rtu.update_rtu_location',
						'meter_rtu.update_rtu_ssh',
						'meter_rtu.update_rtu_force_lp',
						'meter_rtu.idf_number',
						'meter_rtu.switch_name',
						'meter_rtu.idf_port',
						'meter_rtu.last_log_update',
						'meter_rtu.soft_rev',
						'meter_rtu.location_idx',
						'meter_location_table.location_code']);
		
		return DataTables::of($data)
				->addIndexColumn()
				
				->addColumn('update', function($row){
                    
					$update_rtu 			= $row->update_rtu;
					$update_rtu_location 	= $row->update_rtu_location;
					$update_rtu_ssh 		= $row->update_rtu_ssh;
					$update_rtu_force_lp 	= $row->update_rtu_force_lp;
					
					if($update_rtu==1){
						$csv_update_input_value = '<input class="form-check-input disablecsvUpdate" type="checkbox" data-id="'.$row->id.'" checked>';
					}else{
						$csv_update_input_value = '<input class="form-check-input enablecsvUpdate" type="checkbox" data-id="'.$row->id.'">';
					}

					if($update_rtu_location==1){
						$location_update_input_value = '<input class="form-check-input disablesitecodeUpdate" type="checkbox" data-id="'.$row->id.'" checked>';
					}else{
						$location_update_input_value = '<input class="form-check-input enablesitecodeUpdate" type="checkbox" data-id="'.$row->id.'">';
					}
					
					/*Add this option for Client that uses 3g/4g/5g Gateway or those with cloud server that DEC has access*/
					/* 	
					
					if($update_rtu_ssh==1){
						$ssh_input_value = '<input class="form-check-input disableSSH" type="checkbox" data-id="'.$row->id.'" checked>';
					}else{
						$ssh_input_value = '<input class="form-check-input enableSSH" type="checkbox" data-id="'.$row->id.'">';
					}		
					
					if($update_rtu_force_lp==1){
						$force_lp_input_value = '<input class="form-check-input disableLP" type="checkbox" data-id="'.$row->id.'" checked>';
					}else{
						$force_lp_input_value = '<input class="form-check-input enableLP" type="checkbox" data-id="'.$row->id.'">';
					}	
					
					<b style="color:red; margin:1px; margin-left: 5px; margin-right: 5px;">|</b>
						  <li class="breadcrumb-item">
							<div class="form-check form-switch">
								'.$ssh_input_value.'
								<label class="form-check-label" title="Click to Enable Remote SSH">SSH</label>
							</div>
						  </li>
						  <b style="color:red; margin:1px; margin-left: 5px; margin-right: 5px;">|</b>
						  <li class="breadcrumb-item">
							<div class="form-check form-switch">
								'.$force_lp_input_value.'
								<label class="form-check-label" title="Force to Download Load Profile">LP</label>
							</div>
						  </li>
					*/									
					$updateBtn = '
					<nav class="d-flex justify-content-center gateway_config">
						<ol class="breadcrumb">
						
						  <li class="breadcrumb-item">
							<div class="form-check form-switch">
								'.$csv_update_input_value.'
								<label class="form-check-label" title="Click to Update Gateway Meter Batch File">CSV</label>
							</div>
						  </li>
						  <b style="color:red; margin:1px; margin-left: 5px; margin-right: 5px;">|</b>
						  <li class="breadcrumb-item">
						  	<div class="form-check form-switch">
								'.$location_update_input_value.'
								<label class="form-check-label" title="Click to Update Gateway Building Code">Building</label>
							</div>
						  </li>
						  
						  
						</ol>
					</nav>
					
					';
                    return $updateBtn;
					
                })				
				
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_gateway">
					<!--<a href="#" data-id="'.$row->id.'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view" id="UploadGatewayMeter"></a>-->
					<a href="#" data-id="'.$row->id.'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view" id="ViewGateway"></a>
					<a href="#" data-id="'.$row->id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="EditGateway"></a>
					<a href="#" data-id="'.$row->id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="DeleteGateway"></a>
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
	public function getGatewayPerBLGandEEROOM(Request $request)
    {

		$siteID 		= $request->siteID;
		
		$location_id 	= $request->location_id;
		
		$gateway = GatewayModel::get();
		if ($request->ajax()) {	
		
		$data = GatewayModel::join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_rtu.location_idx')
						->where('meter_rtu.site_idx', $siteID)
						->where(function ($r) use($location_id) {
							if ($location_id) {
							   $r->where('meter_rtu.location_idx', $location_id);
							}
							})
						->get([
						'meter_rtu.rtu_id',
						'meter_rtu.gateway_sn',
						'meter_rtu.gateway_mac',
						'meter_rtu.gateway_ip',
						'meter_rtu.update_rtu',
						'meter_rtu.update_rtu_location',
						'meter_rtu.update_rtu_ssh',
						'meter_rtu.update_rtu_force_lp',
						'meter_rtu.idf_number',
						'meter_rtu.switch_name',
						'meter_rtu.idf_port',
						'meter_rtu.last_log_update',
						'meter_rtu.soft_rev',
						'meter_rtu.location_idx',
						'meter_location_table.location_code',
						'meter_location_table.location_description']);
		
		return DataTables::of($data)
				->addIndexColumn()
				
				->addColumn('update', function($row){
                    
					$update_rtu 			= $row->update_rtu;
					$update_rtu_location 	= $row->update_rtu_location;
					$update_rtu_ssh 		= $row->update_rtu_ssh;
					$update_rtu_force_lp 	= $row->update_rtu_force_lp;
					
					if($update_rtu==1){
						$csv_update_input_value = '<input class="form-check-input disablecsvUpdate" type="checkbox" data-id="'.$row->rtu_id.'" checked>';
					}else{
						$csv_update_input_value = '<input class="form-check-input enablecsvUpdate" type="checkbox" data-id="'.$row->rtu_id.'">';
					}

					if($update_rtu_location==1){
						$location_update_input_value = '<input class="form-check-input disablesitecodeUpdate" type="checkbox" data-id="'.$row->rtu_id.'" checked>';
					}else{
						$location_update_input_value = '<input class="form-check-input enablesitecodeUpdate" type="checkbox" data-id="'.$row->rtu_id.'">';
					}
					
					/*Add this option for Client that uses 3g/4g/5g Gateway or those with cloud server that DEC has access*/
					/* 	
					
					if($update_rtu_ssh==1){
						$ssh_input_value = '<input class="form-check-input disableSSH" type="checkbox" data-id="'.$row->id.'" checked>';
					}else{
						$ssh_input_value = '<input class="form-check-input enableSSH" type="checkbox" data-id="'.$row->id.'">';
					}		
					
					if($update_rtu_force_lp==1){
						$force_lp_input_value = '<input class="form-check-input disableLP" type="checkbox" data-id="'.$row->id.'" checked>';
					}else{
						$force_lp_input_value = '<input class="form-check-input enableLP" type="checkbox" data-id="'.$row->id.'">';
					}	
					
					<b style="color:red; margin:1px; margin-left: 5px; margin-right: 5px;">|</b>
						  <li class="breadcrumb-item">
							<div class="form-check form-switch">
								'.$ssh_input_value.'
								<label class="form-check-label" title="Click to Enable Remote SSH">SSH</label>
							</div>
						  </li>
						  <b style="color:red; margin:1px; margin-left: 5px; margin-right: 5px;">|</b>
						  <li class="breadcrumb-item">
							<div class="form-check form-switch">
								'.$force_lp_input_value.'
								<label class="form-check-label" title="Force to Download Load Profile">LP</label>
							</div>
						  </li>
					*/									
					$updateBtn = '
					<nav class="d-flex justify-content-center gateway_config_s2">
						<ol class="breadcrumb">
						
						  <li class="breadcrumb-item">
							<div class="form-check form-switch">
								'.$csv_update_input_value.'
								<label class="form-check-label" title="Click to Update Gateway Meter Batch File">CSV</label>
							</div>
						  </li>
						  <b style="color:red; margin:1px; margin-left: 5px; margin-right: 5px;">|</b>
						  <li class="breadcrumb-item">
						  	<div class="form-check form-switch">
								'.$location_update_input_value.'
								<label class="form-check-label" title="Click to Update Gateway Building Code">Building</label>
							</div>
						  </li>
						  
						  
						</ol>
					</nav>
					
					';
                    return $updateBtn;
					
                })				
				
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_gateway">
					<!--<a href="#" data-id="'.$row->rtu_id.'" class="btn-info btn-circle btn-sm bi bi-upload btn_icon_table btn_icon_table_view" id="UploadGatewayMeter"></a>-->
					<a href="#" data-id="'.$row->rtu_id.'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view" id="ViewGateway"></a>
					<a href="#" data-id="'.$row->rtu_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="EditGateway"></a>
					<a href="#" data-id="'.$row->rtu_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="DeleteGateway"></a>
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
	public function ReloadGatewayOption(Request $request)
    {

		$siteID 		= $request->siteID;
		
		$location_id 	= $request->location_id;
		
		$data = GatewayModel::join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_rtu.location_idx')
						->where('meter_rtu.site_idx', $siteID)
						->where(function ($r) use($location_id) {
							if ($location_id) {
							   $r->where('meter_rtu.location_idx', $location_id);
							}
							})
						->get([
						'meter_rtu.rtu_id',
						'meter_rtu.gateway_sn',
						'meter_rtu.gateway_mac',
						'meter_rtu.gateway_ip']);
		
		return response()->json($data);
		
    }
	
	public function getOfflineGateway(Request $request)
    {

		$siteID = $request->siteID;
		$gateway = GatewayModel::get();
		
		if ($request->ajax()) {	
		
		$raw_query_offline = "SELECT
				 `meter_rtu`.`rtu_id`,
				  `meter_rtu`.`gateway_sn`,
				   `meter_rtu`.`gateway_mac`,
					 `meter_rtu`.`gateway_ip`,
					  `meter_rtu`.`idf_number`,
					   `meter_rtu`.`switch_name`,
					   `meter_rtu`.`idf_port`,
						 `meter_rtu`.`last_log_update`,
						  `meter_rtu`.`soft_rev`,
							  `meter_rtu`.`location_idx`,
							   `meter_location_table`.`location_code` ,
							   `meter_location_table`.`location_description` 
					from `meter_rtu`
					 inner join `meter_location_table` on `meter_location_table`.`location_id` = `meter_rtu`.`location_idx`
					  where `meter_rtu`.`site_idx` = ?
					   and DATEDIFF(NOW(), meter_rtu.last_log_update) >= 1 or (meter_rtu.last_log_update = '0000-00-00 00:00:00' AND meter_rtu.site_idx = ?)";	
					   
		$offline_gateway_data = DB::select("$raw_query_offline", [$siteID,$siteID]);
		
		return DataTables::of($offline_gateway_data)
				->addIndexColumn()
				
                ->addColumn('action', function($row){
                    
					$last_log_update = $row->last_log_update;
					
						/*FROM LOGS*/
						$_date_format = strtotime($last_log_update);
						$date_format = date('Y-m-d H:i:s',$_date_format);		
										
					$actionBtn = '
					<div align="center" class="action_table_menu_gateway">
					<a href="#" data-id="'.$row->rtu_id.'" class="btn-info btn-circle btn-sm bi bi-eye-fill btn_icon_table btn_icon_table_view" id="ViewGateway" title="View Affected Meters"></a>
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
							$statusBtn = $last_log_update;
						}else{
							$statusBtn = $last_log_update;
						}		
										
					$actionBtn = '
					<div align="center" class="row_status_table_gateway">
					'.$statusBtn.'
					</div>';
                    return $actionBtn;
                })
				
				->rawColumns(['status'])
                ->make(true);
		
		}	
		
    }
	
	/*Fetch Site Information*/
	public function gateway_info(Request $request){

		$gatewayID = $request->gatewayID;
		
		$data = GatewayModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_rtu.site_idx')
						->join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_rtu.location_idx')
						->where('meter_rtu.rtu_id', $gatewayID)
						->get([
						'meter_rtu.rtu_id',
						'meter_rtu.gateway_sn',
						'meter_rtu.gateway_mac',
						'meter_rtu.gateway_ip',
						'meter_rtu.update_rtu',
						'meter_rtu.update_rtu_location',
						'meter_rtu.update_rtu_ssh',
						'meter_rtu.update_rtu_force_lp',
						'meter_rtu.idf_number',
						'meter_rtu.connection_type',
						'meter_rtu.switch_name',
						'meter_rtu.idf_port',
						'meter_rtu.last_log_update',
						'meter_rtu.soft_rev',
						'meter_rtu.gateway_description',
						'meter_building_table.building_code',
						'meter_building_table.building_description',
						'meter_rtu.location_idx',
						'meter_location_table.location_code',
						'meter_location_table.location_description']);
		
		return response()->json($data[0]);
		
	}

	/*Delete Site Information*/
	public function delete_gateway_confirmed(Request $request){

		$gatewayID = $request->gatewayID;
		GatewayModel::find($gatewayID)->delete();
		
		/*Delete Meters*/
		MeterModel::where('rtu_idx', $gatewayID)->delete();
		
		return 'Deleted';
		
	} 
 
	public function create_gateway_post(Request $request){

		$request->validate([
          'gateway_sn'      	=> 'required|unique:meter_rtu,gateway_sn',
		  'gateway_mac'      	=> 'required|unique:meter_rtu,gateway_mac',
		  'gateway_ip'      	=> 'required|unique:meter_rtu,gateway_ip',
		  'location_id'   		=> 'required',
        ], 
        [
			'gateway_sn.required' => 'Gateway Serial Number is Required',
			'gateway_mac.required' => 'MAC Address is Required',
			'gateway_ip.required' => 'IP Address/Sim # is Required',
			'location_id.required' => 'Area/EE Room is Required'
        ]
		);

			$data = $request->all();
			#insert Gateway
					 
			$gateway = new GatewayModel();
			$gateway->makeHidden(['update_rtu','update_rtu_location','update_rtu_ssh','update_rtu_force_lp']);
			$gateway->site_idx 					= $request->siteID;
			$gateway->site_code 				= $request->site_code;
			$gateway->gateway_sn 				= $request->gateway_sn;
			$gateway->gateway_mac 				= $request->gateway_mac;
			$gateway->gateway_ip 				= $request->gateway_ip;
			$gateway->idf_number 				= $request->idf_name;
			$gateway->switch_name 				= $request->idf_switch;
			$gateway->idf_port 					= $request->idf_port;
			$gateway->location_idx 				= $request->location_id;
			$gateway->gateway_description 		= $request->gateway_description;
			$gateway->connection_type 			= $request->connection_type;
			$gateway->created_by_user_idx 		= Session::get('loginID');
			$gateway->update_rtu_location		= 1;
			$result = $gateway->save();
			
			if($result){
				return response()->json(['success'=>'Gateway Information successfully created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Gateway Information']);
			}
	}

	public function update_gateway_post(Request $request){

		$request->validate([
          'gateway_sn'     	=> ['required',Rule::unique('meter_rtu')->where( 
										fn ($query) =>$query
											->where('gateway_sn', $request->gateway_sn)
											->where('rtu_id', '<>',  $request->gatewayID )											
										)],
		  'gateway_mac'    	=> ['required',Rule::unique('meter_rtu')->where( 
										fn ($query) =>$query
											->where('gateway_ip', $request->gateway_sn)
											->where('rtu_id', '<>',  $request->gatewayID )
										)],
		  'gateway_ip'    	=> ['required',Rule::unique('meter_rtu')->where( 
										fn ($query) =>$query
											->where('gateway_mac', $request->gateway_sn)
											->where('rtu_id', '<>',  $request->gatewayID )
										)],
		  'location_id'   		=> 'required',
        ], 
        [
			'gateway_sn.required' => 'Gateway Serial Number is Required',
			'gateway_mac.required' => 'MAC Address is Required',
			'gateway_ip.required' => 'IP Address/Sim # is Required',
			'location_id.required' => 'Area/EE Room is Required'
        ]
		);		

			$data = $request->all();
			#update
					
			$gateway = new GatewayModel();
			$gateway = GatewayModel::find($request->gatewayID);
			$gateway->makeHidden(['update_rtu','update_rtu_location','update_rtu_ssh','update_rtu_force_lp']);
			$gateway->site_code 				= $request->site_code;
			$gateway->gateway_sn 				= $request->gateway_sn;
			$gateway->gateway_mac 				= $request->gateway_mac;
			$gateway->gateway_ip 				= $request->gateway_ip;
			$gateway->idf_number 				= $request->idf_name;
			$gateway->switch_name 				= $request->idf_switch;
			$gateway->idf_port 					= $request->idf_port;
			$gateway->location_idx 				= $request->location_id;
			$gateway->gateway_description 		= $request->gateway_description;
			$gateway->connection_type 			= $request->connection_type;
			$gateway->modified_by_user_idx 		= Session::get('loginID');
			$result = $gateway->update();
			
			if($result){
				return response()->json(['success'=>'Gateway Information successfully updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Update Gateway Information']);
			}
	}

//https://www.webslesson.info/2019/11/csv-import-using-ajax-progress-bar-in-php.html
	public function import_meters(Request $request){

		   // $request->validate([
				// 'csv_file'			=>[
                    // 'required',
                    // 'mimetypes:text/plain,text/csv',
					// 'max:10048'	
                // ],
				
           // ],[
				// 'csv_file.required' 				=> 'CSV file is Required',
				
           // ]);
		   
$request->validate([
				'csv_file'	=> 'required|mimes:csv,txt'
				
           ],[
				'csv_file.required' 				=> 'CSV file is Required',
				
           ]);
           //if(!$validator->passes()){
          //     return response()->json(['error'=>1,'error'=>$validator->errors()->toArray()]);
         //  }else{
			 
			   if ($request->hasFile('csv_file')) {
				   
					   $path = 'files/';
					   $file = $request->file('csv_file');
					   @$file_name = time().'_'.@$file->getClientOriginalName();

						//$upload = $file->storeAs($path, $file_name);
						$upload = $file->storeAs($path, $file_name, 'public');
						
						//$name = $request->file('payment_image_reference')->getClientOriginalName();			
						//$path = $request->file('image')->store('public/images');
						//file('file/'. $new_file_name, FILE_SKIP_EMPTY_LINES);
						$path = $request->file('csv_file')->getRealPath();    
						//$csv_content = file_get_contents($path);
						$csv_count_line = file($path, FILE_SKIP_EMPTY_LINES);
						
						$csv_content = file_get_contents($path, FILE_SKIP_EMPTY_LINES);
						
						//$content_to_import = preg_split("\n", $csv_content);
						
						$total_line = count($csv_count_line);
						
						$import_gateway_idx 		= $request->import_gateway_idx;		
						$import_gateway_site_idx 	= $request->import_gateway_site_idx;
						$import_gateway_site_code 	= $request->import_gateway_site_code;
						
						foreach($csv_count_line as $lines_row)
						{				

							$cols = preg_split("/[\t,]/", $lines_row);

							if(count($cols)<10){
									/*show errors*/
							}
							else{
								
								/*Location Code, Meter Name, Tenant/Meter Tagging, Meter Brand, Meter Type, Status, Configuration File, Alternate Address, Meter Role, Multiplier, Remarks */
								$location_code		 		= @$cols[0];
								$meter_name		 			= @$cols[1];
								$tenant_name			 	= @$cols[2];
								$meter_brand		 		= @$cols[3];
								$meter_type			 		= @$cols[4];
								$meter_status		 		= @$cols[5];
								
								if($meter_status=='read'||$meter_status=='ACTIVE'||$meter_status=='READ'){
									$_meter_status = 'ACTIVE';
								}else{
									$_meter_status = 'INACTIVE';
								}
								
								$configuration_file		 	= @$cols[6];
								$alternate_address		 	= @$cols[7];
								
									if($alternate_address==''){
										$meter_default_name = $meter_name;
										$meter_name_addressable = 1;
									}else{
										$meter_default_name = $alternate_address;
										$meter_name_addressable = 0;
									}
								
								$meter_role			 		= @$cols[8];
								$multiplier			 		= @$cols[9];
								$meter_remarks		 		= @$cols[10];//k
								
								/*Get Location Info*/
								$raw_query_location_info = "SELECT location_id FROM meter_location_table WHERE location_code = ?";			
								$location_info  = DB::select("$raw_query_location_info", [$location_code]);
								$location_id	= @$location_info[0]->location_id;
								
								/*Get Meter Config Info*/
								$raw_query_configuration_info = "SELECT config_id FROM meter_configuration_file WHERE config_file = ?";			
								$configfile_info  = DB::select("$raw_query_configuration_info", [$configuration_file]);
								$config_id	= @$configfile_info[0]->config_id;
								
								$meter = new MeterModel();
								$meter->makeHidden(['meter_load_profile', 'last_log_update', 'last_wh_total', 'soft_rev', 'meter_reading']);
								
								$meter->site_idx 						= $import_gateway_site_idx;		
								$meter->site_code 						= $import_gateway_site_code;	

								$meter->rtu_idx 						= $import_gateway_idx;	
								
								$meter->meter_name 						= $meter_name;
								$meter->meter_name_addressable 			= $meter_name_addressable;
								$meter->meter_default_name 				= $meter_default_name;
								$meter->customer_name 					= $tenant_name;
								$meter->config_idx		 				= $config_id;
								$meter->meter_type 						= $meter_type;
								$meter->meter_brand 					= $meter_brand;
								$meter->meter_multiplier 				= $multiplier;
								$meter->meter_role 						= $meter_role;
								$meter->location_idx 					= $location_id;
								$meter->rtu_idx			 				= $import_gateway_idx;
								$meter->meter_status 					= $_meter_status;
								$meter->meter_remarks 					= $meter_remarks;	
								$meter->created_by_user_idx 			= Session::get('loginID');
								
								$result = $meter->save();
								
							}
							
						}
						
						return response()->json(array('success' => "CSV File Successfully Imported!", 'total_line' => @$total_line), 200);

               }
           }
   

	/*Enable CSV Update for Gateway*/
	public function enablecsvUpdate(Request $request){
		
			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu = 1;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'CSV Enabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Enabling CSV Update']);
			}
	}

	/*Disable CSV Update for Gateway*/
	public function disablecsvUpdate(Request $request){
		
			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu = 0;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'CSV Disabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Disabling CSV Update']);
			}
	
	}

	/*Enable CSV Update for Gateway*/
	public function enablesitecodeUpdate(Request $request){
		
			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu_location = 1;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'Site Code Update Enabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Enabling Site Code Update']);
			}
	}

	/*Disable CSV Update for Gateway*/
	public function disablesitecodeUpdate(Request $request){		

			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu_location = 0;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'Site Code Update Disabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Disabling Site Code Update']);
			}
			
	}
	
	/*Enable SSH for Gateway*/
	public function enableSSH(Request $request){
		
			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu_ssh = 1;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'SSH Enabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Enabling SSH']);
			}
			
	}

	/*Disable SSH for Gateway*/
	public function disableSSH(Request $request){		

			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu_ssh = 0;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'SSH Disabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Disabling SSH']);
			}
			
	}	
	
	/*Enable Force Load Profile*/
	public function enableLP(Request $request){
		
			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu_force_lp = 1;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'Force Load Profile Enabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Enabling Force Load Profile']);
			}
			
	}

	/*Disable Force Load Profile*/
	public function disableLP(Request $request){		

			$site = new GatewayModel();
			$site = GatewayModel::find($request->gatewayID);
			$site->update_rtu_force_lp = 0;
			$result = $site->update();
			
			if($result){
				return response()->json(['success'=>'Force Load Profile Disabled!']);
			}
			else{
				return response()->json(['success'=>'Error on Disabling Force Load Profile']);
			}
			
	}	
	
	
	/*End of Controller*/
}