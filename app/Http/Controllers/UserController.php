<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\UserAccountModel;
use Hash;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Storage;

/*Email Function*/
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
	
	/*Load UserList Interface*/
	public function user(){
			
			if(Session::has('loginID')){	
			
				$title = 'User List';

				$data = array();
				$data = User::where('user_id', '=', Session::get('loginID'))->first();
				return view("payroll.user", compact('data','title'));
			
			}
		
	}   
	
	/*Fetch Site List using Datatable*/
	public function getUserList(Request $request)
    {

		$user = UserAccountModel::get();
		if ($request->ajax()) {
		
		$data= UserAccountModel::select(
		'user_id',
		'user_job_title',
		'user_real_name',
		'user_name',
		'user_type',
		'user_email_address',
		'user_branch_access_type',
        'created_at',
        'updated_at'
		);		

		return DataTables::of($data)
				->addIndexColumn()
				->addColumn('action', function($row){	
					
					if($row->user_branch_access_type=='ALL'){
						
						$actionBtn = '
						<div class="action_table_menu_switch">
						<a href="#" data-id="'.$row->user_id.'" class="ri-edit-circle-fill btn_icon_table btn_icon_table_edit" id="editUser"></a>
						<a href="#" data-id="'.$row->user_id.'" class="ri-delete-bin-2-fill btn_icon_table btn_icon_table_delete" id="deleteUser"></a>
						</div>';
						
					}
					
					else{

                        $actionBtn = '
					    <div class="dropdown dropdown-action">
						    <a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
							    <div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
								    <a class="dropdown-item" href="#" data-id="'.$row->user_id.'" id="edit_employee_logs" id="UserAccess" onclick="UpdateUserAccess('.$row->user_id.')" ><i class="fa-solid fa-building  m-r-5"></i> Branch Access</a>
                                    <a class="dropdown-item" href="#" data-id="'.$row->user_id.'" id="edit_employee_logs"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
								    <a class="dropdown-item" href="#" data-id="'.$row->user_id.'" id="delete_employee_logs"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
							    </div>
					    </div>';
					
						/*$actionBtn = '
						<div class="action_table_menu_switch">
						<a href="#" data-id="'.$row->user_id.'" class="ri-edit-circle-fill btn_icon_table btn_icon_table_edit" id="editUser"></a>
						<a href="#" data-id="'.$row->user_id.'" class="ri-delete-bin-2-fill btn_icon_table btn_icon_table_delete" id="deleteUser"></a>
						<a href="#" data-id="'.$row->user_id.'" class="bi bi-building btn_icon_table btn_icon_table_view" id="UserAccess" onclick="UpdateUserAccess('.$row->user_id.')" title="Add User Site Access"></a>
						</div>';*/
					
					}
					
                    return $actionBtn;
                
				})
				->addColumn('created_at_dt_format', function($row){						
                    return $row->created_at;
				})
				
				->addColumn('updated_at_dt_format', function($row){		
				
                    if($row->updated_at=="0000-00-00 00:00:00"){
						return "$row->updated_at";
					}else{
						return "0000-00-00 00:00:00";
					}

				})
				
				->rawColumns(['action','created_at_dt_format','updated_at_dt_format'])
                ->make(true);
				
		}	
    }


	/*Fetch UserList Information*/
	public function user_info(Request $request){

		$UserID = $request->UserID;
		$data = UserAccountModel::find($UserID, ['user_name','user_real_name','user_type', 'user_job_title', 'user_email_address', 'user_branch_access_type']);
		return response()->json($data);
		
	}

	/*Delete UserList Information*/
	public function delete_user_confirmed(Request $request){
		
			$userID = $request->userID;
			UserAccountModel::find($userID)->delete();
			return 'Deleted';
		
	} 

	public function create_user_post(Request $request){
		
		$request->validate([
		  'user_real_name'  		=> 'required|unique:user_tb,user_real_name',
		  'user_name'      			=> 'required|unique:user_tb,user_name',
		  'user_password'			=> 'required|min:6|max:20',
		  'user_type'    			=> 'required',
		  'user_email_address'   	=> ['required',Rule::unique('user_tb')->where( 
												fn ($query) =>$query
													->where('user_email_address', $request->user_email_address)
													->where('user_email_address', '!=', '')
												)]
        ], 
        [
			'user_real_name.required'	 	=> 'Name is required',
			'user_name.required' 			=> 'User Name is Required',
			'user_password.required' 		=> 'Password is Required',
			'user_email_address.required' 	=> 'Email Address is Required',
			'user_type.required' 			=> 'User Type is Required'
        ]
		);
					
			$UserList = new UserAccountModel();
			$UserList->user_real_name 			= $request->user_real_name;
			$UserList->user_job_title 			= $request->user_job_title;
			$UserList->user_name 				= $request->user_name;
			$UserList->user_email_address 		= $request->user_email_address;
			$UserList->user_password 			= hash::make($request->user_password);
			$UserList->user_type 				= $request->user_type;
			$UserList->user_branch_access_type 	= $request->user_access;
			
			$result = $UserList->save();
			
			/*Get Last ID*/
			$last_transaction_id = $UserList->user_id;
				
			if($result){
				
						/*Call Email Function to Send Mail*/
						if($request->user_email_address!=''){
						
							$title 			= 'Teves Billing System Portal:User Account Created';
							$body 			= 'Your User Account has been successfully created. Please use the provided Credentials below.';
							$name 			= $request->user_real_name;
							$user_id 		= $last_transaction_id;
							$user_name 		= $request->user_name;
							$user_password 	= $request->user_password;
							
							Mail::to($request->user_email_address)->send(new ResetPassword($title, $body, $name, $user_id, $user_name, $user_password));
						}
				
				return response()->json(array('success' => "User Information successfully created!", 'user_id' => $last_transaction_id), 200);
				
			}
			else{
				return response()->json(['success'=>'Error on Insert User Information']);
			}
			
	}
	
	public function update_user_post(Request $request){
			
			if($request->user_password!=''){		
					$request->validate([
					  'user_real_name'  		=> 'required|unique:user_tb,user_real_name,'.$request->userID.',user_id',
					  'user_name'      			=> 'required|unique:user_tb,user_name,'.$request->userID.',user_id',
					  'user_email_address'   	=> ['required',Rule::unique('user_tb')->where( 
												fn ($query) =>$query
													->where('user_email_address', $request->user_email_address)
													->where('user_email_address', '!=', '')
													->where('user_id','!=', $request->userID) 
												)],
					  'user_password'   		=> 'required|min:6|max:20',
					  'user_type'    			=> 'required',
					], 
					[
						'user_real_name.required' 		=> 'Name is Required',
						'user_name.required' 			=> 'User Name is Required',
						'user_password.required' 		=> 'Password is Required',
						'user_email_address.required' 	=> 'Email Address is Required',
						'user_type.required' 			=> 'User Type is Required'
					]
					);
			}
			else{
					$request->validate([
					  'user_real_name'  		=> 'required|unique:user_tb,user_real_name,'.$request->userID.',user_id',
					  'user_name'      			=> 'required|unique:user_tb,user_name,'.$request->userID.',user_id',
					  'user_email_address'   	=> ['required',Rule::unique('user_tb')->where( 
												fn ($query) =>$query
													->where('user_email_address', $request->user_email_address)
													->where('user_email_address', '!=', '')
													->where('user_id','!=', $request->userID) 
												)],
					  'user_type'    			=> 'required',
					], 
					[
						'user_real_name.required' 		=> 'Name is Required',
						'user_name.required' 			=> 'User Name is Required',
						'user_email_address.required' 	=> 'Email Address is Required',
						'user_type.required' 			=> 'User Type is Required'
					]
					);
			}			
			
			
			$UserList = new UserAccountModel();
			$UserList = UserAccountModel::find($request->userID);
			$UserList->user_job_title 					= $request->user_job_title;
			$UserList->user_real_name 					= $request->user_real_name;
			$UserList->user_name 						= $request->user_name;
			if($request->user_password!=''){ $UserList->user_password 	= hash::make($request->user_password); }/*Kung BInago Lang Password saka ma update*/
			$UserList->user_type 						= $request->user_type;
			$UserList->user_email_address 				= $request->user_email_address;
			$UserList->user_branch_access_type 			= $request->user_access;
			$UserList->updated_by_user_id 				= Session::get('loginID');
			
			$result = $UserList->update();
			
			if($request->user_password!=''){ 
			
				$UserList->user_password 	= hash::make($request->user_password); 
						
						/*Call Email Function*/
						if($request->user_email_address!=''){
						
							$title 			= 'Teves Billing System Portal:Reset Password';
							$body 			= 'Your Password has been changed successfully. Please use below password to log in.';
							$name 			= $request->user_real_name;
							$user_id 		= $request->userID;
							$user_name 		= $request->user_name;
							$user_password 	= $request->user_password;

							Mail::to($request->user_email_address)->send(new ResetPassword($title, $body, $name, $user_id, $user_name, $user_password));
							
						}
						
			}/*Kung BInago Lang Password saka ma update*/
			
			if($result){
				return response()->json(['success'=>'User Information successfully updated!']);
				/*Send Email*/
			}
			else{
				return response()->json(['success'=>'Error on Update User Information']);
			}
	
	}

	public function user_account_post(Request $request){
		
			// if($request->user_password!=''){		
					// $request->validate([
					  // 'user_real_name'  => 'required|unique:user_tb,user_real_name,'.$request->userID.',user_id',
					  // 'user_name'      	=> 'required|unique:user_tb,user_name,'.$request->userID.',user_id',
					  // 'user_password'   => 'required|min:6|max:20'
					// ], 
					// [
						// 'user_real_name.required' => 'Name is required',
						// 'user_name.required' => 'User Name is Required',
						// 'user_password.required' => 'Password is Required'
					// ]
					// );
			// }
			// else{
					// $request->validate([
					  // 'user_real_name'  => 'required|unique:user_tb,user_real_name,'.$request->userID.',user_id',
					  // 'user_name'      	=> 'required|unique:user_tb,user_name,'.$request->userID.',user_id',
					// ], 
					// [
						// 'user_real_name.required' => 'Name is required',
						// 'user_name.required' => 'User Name is Required'
					// ]
					// );
			// }

			if($request->user_password!=''){		
					$request->validate([
					
					  'user_real_name'  		=> 'required|unique:user_tb,user_real_name,'.$request->userID.',user_id',
					  'user_name'      			=> 'required|unique:user_tb,user_name,'.$request->userID.',user_id',
					  'user_password'   		=> 'required|min:6|max:20',
					  'user_email_address'   	=> 'unique:user_tb,user_email_address,'.$request->userID.',user_id',
					  
					], 
					[
					
					 'user_real_name.required' => 'Name is required',
					 'user_name.required' => 'User Name is Required',
					 'user_password.required' => 'Password is Required'
					
					]
					);
			}
			else{
					$request->validate([
					  'user_real_name'  	=> 'required|unique:user_tb,user_real_name,'.$request->userID.',user_id',
					  'user_name'      		=> 'required|unique:user_tb,user_name,'.$request->userID.',user_id',
					  'user_email_address'  => 'unique:user_tb,user_email_address,'.$request->userID.',user_id',
					], 
					[
						'user_real_name.required' => 'Name is required',
						'user_name.required' => 'User Name is Required'
					]
					);
			}
	
	
	
	
			// $UserList = new UserAccountModel();
			// $UserList = UserAccountModel::find($request->userID);
			// $UserList->user_real_name 	= $request->user_real_name;
			// $UserList->user_name 		= $request->user_name;
			// if($request->user_password!=''){ $UserList->user_password 	= hash::make($request->user_password); }/*Kung BInago Lang Password saka ma update*/
			
			// $result = $UserList->update();
			
				
			$UserList = new UserAccountModel();
			$UserList = UserAccountModel::find($request->userID);
			
			$UserList->user_real_name 			= $request->user_real_name;
			$UserList->user_name 				= $request->user_name;
			$UserList->user_email_address 		= $request->user_email_address;
			
			/*Kung Binago Lang Password saka ma update*/
			if($request->user_password!=''){ 
						
						$UserList->user_password 	= hash::make($request->user_password); 
						
						/*Call Email Function*/
						if($request->user_email_address!=''){
				
							$title 			= 'Teves Billing System Portal: Password Reset';
							$body 			= 'Your password has been changed successfully. Please use below password to log in.';
							$name 			= $request->user_real_name;
							$user_id 		= $request->userID;
							$user_name 		= $request->user_name;
							$user_password 	= $request->user_password;
							
							Mail::to($request->user_email_address)->send(new ResetPassword($title, $body, $name, $user_id, $user_name, $user_password));
							
						}
				
			}
			
			$UserList->updated_by_user_id 	= Session::get('loginID');
			$result = $UserList->update();
			
			
			
			if($result){
				return response()->json(['success'=>'Account Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Account User Information']);
			}
	
	}

}
