<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAccountModel;
use App\Models\UserSiteAccessModel;
use App\Models\SiteModel;
use Hash;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Storage;

class UserSiteAccessController extends Controller
{

	/*Fetch Site List using Datatable*/
	public function getUserSiteAccess(Request $request)
    {
		
		$userID = $request->UserID;
		
		if ($request->ajax()) {
		
		$user_site_access_data = SiteModel::leftJoin('user_access_group', function($q) use ($userID)
        {
            $q->on('meter_site.site_id', '=', 'user_access_group.site_idx')
				->where('user_idx', '=', $userID);
        })
			->leftjoin('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
			->leftjoin('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
			->leftjoin('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
              		->get([
					'meter_site.site_id',
					'user_access_group.user_idx',
					'user_access_group.site_idx',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off'
					]);

		return DataTables::of($user_site_access_data)
				->addIndexColumn()
                ->addColumn('action', function($row){
                    
				     $user_id 			= $row->user_idx;
					 $site_id 			= $row->site_id;
					 $access_verified 	= $row->site_idx;
										
							if($access_verified != NULL){
								
								$chk_status = "checked='checked'";
								
							}else{
								
								$chk_status = "";
								
							}
					
					$actionBtn = "<input type='checkbox' name='site_checklist' onclick='enableUpdateUserAccess();' value='".$site_id."' id='CheckboxGroup1_".$site_id."' ".$chk_status."/>";
                    return $actionBtn;
                })
				
				->rawColumns(['action'])
                ->make(true);
				
		}
		
    }

	public function add_user_access_post(Request $request){
		
			$userID = $request->userID;
			$site_items = $request->site_items;
	
			$site_list_ids = $site_items;
			@$site_list_arr = explode(",", $site_list_ids);

			/*RESET*/
			UserSiteAccessModel::where('user_idx', $userID)->delete();

			if($site_list_ids!=''){
				
			/*LIST OF SITE ID's*/		
			foreach ($site_list_arr as $site_list_ids_row):

				@$site_id = $site_list_ids_row; 
				
				/*Re Insert Updated List*/
			
				$NewUserSiteAccess = new UserSiteAccessModel();
				$NewUserSiteAccess->makeHidden(['user_name']);
				$NewUserSiteAccess->user_idx 				= $userID;
				$NewUserSiteAccess->site_idx 			= $site_id;
				$NewUserSiteAccess->created_by_user_idx 	= Session::get('loginID');
				$NewUserSiteAccess->access_list_src 		= 'CAMR';
				$result = $NewUserSiteAccess->save();
			
			endforeach; 
	
				if($result){
					return response()->json(['success'=>'User Site Access Updated!']);
				}
				else{
					return response()->json(['success'=>'User Site Access Information']);
				}
			
			}
			else{
				
				return response()->json(['success'=>'User Site Access Removed!']);
			
			}
	
	}

}
