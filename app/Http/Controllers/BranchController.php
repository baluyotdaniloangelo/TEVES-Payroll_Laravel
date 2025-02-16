<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\BranchModel;
use Session;
use Validator;
use DataTables;

class BranchController extends Controller
{
	
	/*Load Branch Interface*/
	public function branch(){

		if(Session::has('loginID')){	
			$title = 'Branch';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			return view("payroll.Branch", compact('data','title'));
		}
		
	}   

	/* Branch List for Employee Logs Interface*/
	public function getBranchList_for_item_selection(){

		if(Session::has('loginID')){	
		
			$branch_data = BranchModel::all();
			return response()->json($branch_data);	
			
		}
		
	}  
	
	
	/*Fetch Branch List using Datatable*/
	public function getBranchList(Request $request)
    {
		//$list = BranchModel::get();
		if ($request->ajax()) {
			$data = BranchModel::select(
			'branch_id',
			'branch_code',
			'branch_name',
			'branch_initial',
			'branch_tin',
			'branch_address',
			'branch_contact_number',
			'branch_owner',
			'branch_owner_title');
			return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function($row){
						
						$actionBtn = '
							<div class="dropdown dropdown-action">
								<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
									<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
										<a class="dropdown-item" href="#" data-id="'.$row->branch_id.'" id="branch_department_management" title="Branch Department Maintenance"><i class="fa-solid fa-eye m-r-5"></i> Department</a>
										<a class="dropdown-item" href="#" data-id="'.$row->branch_id.'" id="edit_branch"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
										<a class="dropdown-item" href="#" data-id="'.$row->branch_id.'" id="delete_branch"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
									</div>
							</div>';
					
						return $actionBtn;
					})
					->rawColumns(['action'])
					->make(true);
		}
    }

	/*Fetch Branch Information*/
	public function branch_info(Request $request){
		
		$branchID = $request->branchID;
		$data = BranchModel::find($branchID, ['branch_id', 'branch_code', 'branch_name', 'branch_initial', 'branch_tin', 'branch_address', 'branch_contact_number', 'branch_owner', 'branch_owner_title', 'branch_logo']);
		return response()->json($data);

	}

	/*Delete Branch Information*/
	public function delete_branch_confirmed(Request $request){

		$branchID = $request->branchID;
		BranchModel::find($branchID)->delete();
		
		return 'Deleted';

	} 
	
	/*Create/Update Branch Information*/
	public function submit_branch_post(Request $request){
		
		$branch_id = $request->branch_id;
		
		$request->validate([
		  'branch_code'      		=> ['required',Rule::unique('teves_branch_table')->where( 
											fn ($query) =>$query
												->where('branch_code', $request->branch_code)
												->where('branch_id', '<>',  $branch_id )											
										)],
		  'branch_name'   			=> ['required',Rule::unique('teves_branch_table')->where( 
											fn ($query) =>$query
												->where('branch_name', $request->branch_name)
												->where('branch_id', '<>',  $branch_id )											
										)],
		  'branch_initial'   		=> ['required',Rule::unique('teves_branch_table')->where( 
											fn ($query) =>$query
												->where('branch_initial', $request->branch_initial)
												->where('branch_id', '<>',  $branch_id )											
										)]
        ]);
		
			if($branch_id==0){
				
				$branch = new BranchModel();
				$branch->branch_code 			= $request->branch_code;
				$branch->branch_name 			= $request->branch_name;
				$branch->branch_initial 		= $request->branch_initial;
				$branch->branch_tin 			= $request->branch_tin;
				$branch->branch_address 		= $request->branch_address;
				$branch->branch_contact_number 	= $request->branch_contact_number;
				$branch->branch_owner 			= $request->branch_owner;
				$branch->branch_owner_title 	= $request->branch_owner_title;
				$branch->created_by_user_idx 	= Session::get('loginID');
				
				$result = $branch->save();
				
				/*Get Last ID*/
				//$last_transaction_id = $branch->branch_id;
				
				if($result){
					return response()->json(['success'=>'Created']);
				}
				else{
					return response()->json(['success'=>'Error on Insert Branch Information']);
				}
				
			}else{
				
				$branch = new BranchModel();
				$branch = BranchModel::find($branch_id);
				$branch->branch_code 			= $request->branch_code;
				$branch->branch_name 			= $request->branch_name;
				$branch->branch_initial 		= $request->branch_initial;
				$branch->branch_tin 			= $request->branch_tin;
				$branch->branch_address 		= $request->branch_address;
				$branch->branch_contact_number 	= $request->branch_contact_number;
				$branch->branch_owner 			= $request->branch_owner;
				$branch->branch_owner_title 	= $request->branch_owner_title;
				$branch->updated_by_user_idx 	= Session::get('loginID');
				
				$result = $branch->update();
				
				if($result){
					return response()->json(['success'=>'Updated']);
				}
				else{
					return response()->json(['success'=>'Error on Insert Branch Information']);
				}
								
			}
			
	}

}
