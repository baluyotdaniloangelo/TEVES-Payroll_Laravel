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
		
			$title = 'User List';
			$data = array();
			if(Session::has('loginID')){
				$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			}
	
			return view("amr.user", compact('data','title'));
		
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
		'user_email_address',
		'user_type',
		'user_access',
        'created_at',
        'updated_at'
		);		

		return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('action', function($row){	
					
					if($row->user_access=='ALL'){
						
						$actionBtn = '
						<div class="action_table_menu_switch">
						<a href="#" data-id="'.$row->user_id.'" class="bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editUser" title="Update User Information"></a>
						<a href="#" data-id="'.$row->user_id.'" class="bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteUser" title="Delete User Information"></a>
						</div>';
						
					}
					
					else{
					
						$actionBtn = '
						<div class="action_table_menu_switch">
						<a href="#" data-id="'.$row->user_id.'" class="bi bi-building btn_icon_table btn_icon_table_view" id="UserAccess" onclick="UpdateUserAccess('.$row->user_id.')" title="Add User Site Access"></a>
						<a href="#" data-id="'.$row->user_id.'" class="bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editUser" title="Update User Information"></a>
						<a href="#" data-id="'.$row->user_id.'" class="bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteUser" title="Delete User Information"></a>
						</div>';
					
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
						return "$row->updated_at";
					}

				})
				
				->rawColumns(['action','created_at_dt_format','updated_at_dt_format'])
                ->make(true);
		}	
    }

	/*Fetch UserList Information*/
	public function user_info(Request $request){

		$UserID = $request->UserID;
		$data = UserAccountModel::find($UserID, ['user_name','user_real_name','user_email_address','user_type', 'user_access', 'user_job_title']);
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
		  'user_email_address'   	=> ['required',Rule::unique('user_tb')->where( 
												fn ($query) =>$query
													->where('user_email_address', $request->user_email_address)
													->where('user_email_address', '!=', '')
												)],
		  'user_password'   		=> 'required|min:6|max:20',
		  'user_type'    			=> 'required',
        ], 
        [
			'user_real_name.required'	=> 'Name is Required',
			'user_name.required' 		=> 'User Name is Required',
			'user_password.required' 	=> 'Password is Required',
			'user_email_address.required' 		=> 'Email Address is Required',
			'user_type.required' 		=> 'User Type is Required'
        ]
		);

			#$data = $request->all();
			#insert
					
			$UserList = new UserAccountModel();
			$UserList->user_real_name 				= $request->user_real_name;
			$UserList->user_job_title 				= $request->user_job_title;
			$UserList->user_name 					= $request->user_name;
			$UserList->user_email_address 			= $request->user_email_address;
			$UserList->user_password 				= hash::make($request->user_password);
			$UserList->user_type 					= $request->user_type;
			$UserList->user_access 					= $request->user_access;
			$UserList->created_by_user_idx 			= Session::get('loginID');
			$result = $UserList->save();
				
			/*Get Last ID*/
			$last_transaction_id = $UserList->user_id;
				
			if($result){
				
						/*Call Email Function to Send Mail*/
						if($request->user_email_address!=''){
						
							$title 			= 'Centralized Automated Meter Reading: User Account Created';
							$body 			= 'Your User Account for Centralized Automated Meter Reading(OPUS) has been created successfully. Please use the provided Credentials below.';
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
			
			#$data = $request->all();
			#insert		
			$UserList = new UserAccountModel();
			$UserList = UserAccountModel::find($request->userID);
			$UserList->user_job_title 					= $request->user_job_title;
			$UserList->user_real_name 					= $request->user_real_name;
			$UserList->user_name 						= $request->user_name;
			$UserList->user_email_address 				= $request->user_email_address;
			
			if($request->user_password!=''){ 
			
				$UserList->user_password 	= hash::make($request->user_password); 
						
						/*Call Email Function*/
						if($request->user_email_address!=''){
						
							$title 			= 'Centralized Automated Meter Reading: Password Reset';
							$body 			= 'Your Password has been changed successfully. Please use below password to log in.';
							$name 			= $request->user_real_name;
							$user_id 		= $request->userID;
							$user_name 		= $request->user_name;
							$user_password 	= $request->user_password;

							Mail::to($request->user_email_address)->send(new ResetPassword($title, $body, $name, $user_id, $user_name, $user_password));
							
						}
						
			
			}/*Kung BInago Lang Password saka ma update*/
			
			$UserList->user_type 						= $request->user_type;
			$UserList->user_access 						= $request->user_access;
			$UserList->modified_by_user_idx 			= Session::get('loginID');
			$result = $UserList->update();
			
			if($result){
				return response()->json(['success'=>'User Information successfully updated!']);
				/*Send Email*/
			}
			else{
				return response()->json(['success'=>'Error on Update User Information']);
			}
	
	}

	public function user_account_post(Request $request){

			if($request->user_password!=''){		
					$request->validate([
					
					  'user_real_name'  		=> 'required|unique:user_tb,user_real_name,'.$request->userID.',user_id',
					  'user_name'      			=> 'required|unique:user_tb,user_name,'.$request->userID.',user_id',
					  'user_password'   		=> 'required|min:6|max:20',
					  'user_email_address'   	=> ['required',Rule::unique('user_tb')->where( 
												fn ($query) =>$query
													->where('user_email_address', $request->user_email_address)
													->where('user_email_address', '!=', '')
													->where('user_id','!=',$request->userID) 
												)],
					  
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
					  'user_email_address'   	=> ['required',Rule::unique('user_tb')->where( 
												fn ($query) =>$query
													->where('user_email_address', $request->user_email_address)
													->where('user_email_address', '!=', '')
													->where('user_id', '!=', $request->userID) 
												)],
					], 
					[
						'user_real_name.required' => 'Name is required',
						'user_name.required' => 'User Name is Required'
					]
					);
			}

	
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
				
							$title 			= 'Centralized Automated Meter Reading: Password Reset';
							$body 			= 'Your password has been changed successfully. Please use below password to log in.';
							$name 			= $request->user_real_name;
							$user_id 		= $request->userID;
							$user_name 		= $request->user_name;
							$user_password 	= $request->user_password;
							
							Mail::to($request->user_email_address)->send(new ResetPassword($title, $body, $name, $user_id, $user_name, $user_password));
							
						}
				
			}
			
			
			$UserList->modified_by_user_idx 	= Session::get('loginID');
			$result = $UserList->update();
			
			if($result){
				return response()->json(['success'=>'Account Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Account User Information']);
			}
	
	}

}
