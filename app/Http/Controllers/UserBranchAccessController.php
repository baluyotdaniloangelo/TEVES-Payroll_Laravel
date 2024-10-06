<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserAccountModel;
use App\Models\UserBranchAccessModel;
use App\Models\BranchModel;
use Hash;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Storage;

class UserBranchAccessController extends Controller
{

	/*Fetch Site List using Datatable*/
	public function getUserBranchAccess(Request $request)
    {
		
		$userID = $request->UserID;
		
		if ($request->ajax()) {
		
		$user_site_access_data = BranchModel::leftJoin('teves_user_branch_access', function($q) use ($userID)
        {
            $q->on('teves_branch_table.branch_id', '=', 'teves_user_branch_access.branch_idx')
				->where('teves_user_branch_access.user_idx', '=', $userID);
        })
              		->get([
					'teves_branch_table.branch_id',
					'teves_user_branch_access.user_idx',
					'teves_user_branch_access.branch_idx',
					'teves_branch_table.branch_code',
					'teves_branch_table.branch_name'
					]);

		return DataTables::of($user_site_access_data)
				->addIndexColumn()
                ->addColumn('action', function($row){
                    
				     $user_id 			= $row->user_idx;
					 $branch_id 		= $row->branch_id;
					 $access_verified 	= $row->branch_idx;
										
							if($access_verified != NULL){
								
								$chk_status = "checked='checked'";
								
							}else{
								
								$chk_status = "";
								
							}
					
					$actionBtn = "<input type='checkbox' name='site_checklist' value='".$branch_id."' id='CheckboxGroup1_".$branch_id."' ".$chk_status."/>";
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
			UserBranchAccessModel::where('user_idx', $userID)->delete();

			if($site_list_ids!=''){
				
			/*LIST OF SITE ID's*/		
			foreach ($site_list_arr as $site_list_ids_row):

				@$site_id = $site_list_ids_row; 
				
				/*Re Insert Updated List*/
			
				$NewUserSiteAccess = new UserBranchAccessModel();
				$NewUserSiteAccess->makeHidden(['user_name']);
				$NewUserSiteAccess->user_idx 				= $userID;
				$NewUserSiteAccess->branch_idx 				= $site_id;
				$NewUserSiteAccess->created_by_user_idx 	= Session::get('loginID');
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
