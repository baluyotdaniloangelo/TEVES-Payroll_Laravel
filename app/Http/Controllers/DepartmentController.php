<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\BranchModel;
use App\Models\DepartmentModel;
use Session;
use Validator;
use DataTables;

class DepartmentController extends Controller
{
	
	/*Load Department Interface*/
	/*Department Management can be seen on the department Page*/
	

	/*Fetch department List using Datatable*/
	public function getDepartmentList(Request $request)
    {
		//$list = DepartmentModel::get();
		// if ($request->ajax()) {
			// $data = departmentModel::select(
			// 'department_id',
			// 'department_name');
			// return DataTables::of($data)
					// ->addIndexColumn()
					// ->addColumn('action', function($row){
						
						// $actionBtn = '
							// <div class="dropdown dropdown-action">
								// <a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
									// <div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
										// <a class="dropdown-item" href="#" data-id="'.$row->department_id.'" id="edit_department"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
										// <a class="dropdown-item" href="#" data-id="'.$row->department_id.'" id="delete_department"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
									// </div>
							// </div>';
					
						// return $actionBtn;
					// })
					// ->rawColumns(['action'])
					// ->make(true);
		// }
    
		$branchID = $request->branchID;
					
		/*Using Raw Query*/
		$raw_query = "select `department_id`,`department_name` from `teves_department_table` where `branch_idx` = ?";			
		$data = DB::select("$raw_query", [$branchID]);

		return DataTables::of($data)
			->addIndexColumn()
			->addColumn('action', function($row){
						 $actionBtn = '
							 <div class="dropdown dropdown-action">
								 <a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
									 <div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
										 <a class="dropdown-item" href="#" data-id="'.$row->department_id.'" id="edit_department"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
										 <a class="dropdown-item" href="#" data-id="'.$row->department_id.'" id="delete_department"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
									 </div>
							 </div>';
					
						 return $actionBtn;
					 })
			->rawColumns(['action'])
			->make(true);
	}
	
	

	/*Fetch department Information*/
	public function department_info(Request $request){
		
		$departmentID = $request->departmentID;
		$data = DepartmentModel::find($departmentID, ['department_id', 'branch_idx', 'department_name']);
		return response()->json($data);

	}

	/*Delete department Information*/
	public function delete_department_confirmed(Request $request){

		$departmentID = $request->departmentID;
		departmentModel::find($departmentID)->delete();
		
		return 'Deleted';

	} 
	
	/*Create/Update department Information*/
	public function sumbit_department_post(Request $request){
		
		$department_id = $request->department_id;
		
		$request->validate([
		  'department_name'   			=> ['required',Rule::unique('teves_department_table')->where( 
											fn ($query) =>$query
												->where('department_name', $request->department_name)
												->where('department_id', '<>',  $department_id )											
										)]
        ]);
		
			if($department_id==0){
				
				$department = new departmentModel();
				$department->department_name 			= $request->department_name;
				$department->branch_idx 				= $request->branch_idx;
				$department->created_by_user_idx 		= Session::get('loginID');
				
				$result = $department->save();
				
				/*Get Last ID*/
				//$last_transaction_id = $department->department_id;
				
				if($result){
					return response()->json(['success'=>'Created']);
				}
				else{
					return response()->json(['success'=>'Error on Insert department Information']);
				}
				
			}else{
				
				$department = new departmentModel();
				$department = departmentModel::find($department_id);
				$department->department_name 			= $request->department_name;
				$department->branch_idx 				= $request->branch_idx;
				$department->updated_by_user_idx 		= Session::get('loginID');
				
				$result = $department->update();
				
				if($result){
					return response()->json(['success'=>'Updated']);
				}
				else{
					return response()->json(['success'=>'Error on Insert department Information']);
				}
								
			}
			
	}

}
