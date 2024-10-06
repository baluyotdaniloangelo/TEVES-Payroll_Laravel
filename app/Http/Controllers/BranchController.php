<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\BranchModel;
use Session;
use Validator;
use DataTables;

class BranchController extends Controller
{
	
	/*Load Branch Interface*/
	public function branch(){

		
		if(Session::has('loginID') && Session::get('UserType')=="Admin"){
			
			$title = 'Branch';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			$client_data = BranchModel::all();		
			
			return view("pages.branch", compact('data','title'));
		}
		

	}   

	/*Fetch Branch List using Datatable*/
	public function getBranchList(Request $request)
    {
		$list = BranchModel::get();
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
						<div align="center" class="action_table_menu_client">
						<a href="#" data-id="'.$row->branch_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editbranch"></a>
						<a href="#" data-id="'.$row->branch_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deletebranch"></a>
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
		
		/*Delete Price for Branch*/	
		DB::delete("delete from teves_product_branch_price_table WHERE branch_idx=?",[$branchID]);	
		
		return 'Deleted';

	} 
	
	/*Create Branch Information*/
	public function create_branch_post(Request $request){
		
		$request->validate([
          'branch_code'      		=> 'required|unique:teves_branch_table,branch_code',
		  'branch_name'   			=> 'required',
		  'branch_initial'   		=> 'required|unique:teves_branch_table,branch_initial',
		  'branch_tin'   	 		=> 'required|unique:teves_branch_table,branch_tin'
        ], 
        [
			'branch_code.required' 		=> 'Branch Code is required',
			'branch_name.required' 		=> 'Branch Name is required',
			'branch_initial.required' 	=> 'Branch Initial is Required',
			'branch_tin.required' 		=> 'Branch TIN is Required'
        ]
		);
		
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
			$last_transaction_id = $branch->branch_id;
			
			/*Create Entry at Product Price Per Branch*/
			
			DB::insert("INSERT INTO teves_product_branch_price_table (product_idx,branch_idx,branch_price)
			SELECT b.product_id,a.branch_id,b.product_price FROM teves_branch_table AS a,teves_product_table AS b
			WHERE a.branch_id=?",[$last_transaction_id]);	
			
			if($result){
				return response()->json(['success'=>'Branch Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Branch Information']);
			}
			
	}

	/*Update Branch Information*/
	public function update_branch_post(Request $request){
		
		$request->validate([
          'branch_code'      	=> 'required|unique:teves_branch_table,branch_code,'.$request->branchID.',branch_id',
		  'branch_name'   		=> 'required',
		  'branch_initial'   	=> 'required|unique:teves_branch_table,branch_initial,'.$request->branchID.',branch_id',
		  'branch_tin'   	 	=> 'required|unique:teves_branch_table,branch_tin,'.$request->branchID.',branch_id'
        ], 
        [
			'branch_code.required' => 'Branch Code is required',
			'branch_name.required' => 'Branch Name is required',
			'branch_initial.required' => 'Branch Initial is Required',
			'branch_tin.required' => 'Branch TIN is Required'
        ]
		);
			
			$branch = new BranchModel();
			$branch = BranchModel::find($request->branchID);
			$branch->branch_code 			= $request->branch_code;
			$branch->branch_name 			= $request->branch_name;
			$branch->branch_initial 		= $request->branch_initial;
			$branch->branch_tin 			= $request->branch_tin;
			$branch->branch_address 		= $request->branch_address;
			$branch->branch_contact_number 	= $request->branch_contact_number;
			$branch->branch_owner 			= $request->branch_owner;
			$branch->branch_owner_title 	= $request->branch_owner_title;
			$branch->modified_by_user_idx 	= Session::get('loginID');
			
			$result = $branch->update();
			if($result){
				return response()->json(['success'=>'Branch Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Update Branch Information']);
			}
	}
}
