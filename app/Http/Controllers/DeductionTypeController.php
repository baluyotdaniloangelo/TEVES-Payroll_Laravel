<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\DeductionTypeModel;
use Session;
use Validator;
use DataTables;

class DeductionTypeController extends Controller
{
	
	/*Load DeductionType Interface*/
	public function DeductionType(){

		if(Session::has('loginID')){	
			$title = 'Deduction Type';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			$client_data = DeductionTypeModel::all();		
			
			$active_link = 'deduction_type';
			
			return view("payroll.deduction_type", compact('data','title', 'deduction_type'));
		}
		
	}   

	/* Deduction Type List for Employee Deduction Logs Interface*/
	public function getDeductionTypeList_for_selection(){

		if(Session::has('loginID')){	
		
			$deduction_type_data = DeductionTypeModel::all();
			return response()->json($deduction_type_data);	
			
		}
		
	} 

	/*Fetch DeductionType List using Datatable*/
	public function getDeductionTypeList(Request $request)
    {
		//$list = DeductionTypeModel::get();
		if ($request->ajax()) {
			$data = DeductionTypeModel::select(
			'deduction_id',
			'deduction_description');
			return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function($row){
						
						$actionBtn = '
							<div class="dropdown dropdown-action">
								<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
									<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
										<a class="dropdown-item" href="#" data-id="'.$row->deduction_id.'" id="edit_DeductionType"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
										<a class="dropdown-item" href="#" data-id="'.$row->deduction_id.'" id="delete_DeductionType"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
									</div>
							</div>';
					
						return $actionBtn;
					})
					->rawColumns(['action'])
					->make(true);
		}
    }

	/*Fetch DeductionType Information*/
	public function deduction_type_info(Request $request){
		
		$DeductionID = $request->DeductionID;
		$data = DeductionTypeModel::find($DeductionID, ['deduction_id', 'deduction_description']);
		return response()->json($data);

	}

	/*Delete DeductionType Information*/
	public function delete_deduction_type_confirmed (Request $request){

		$DeductionID = $request->DeductionID;
		DeductionTypeModel::find($DeductionID)->delete();
		
		return 'Deleted';

	} 
	
	/*Create/Update DeductionType Information*/
	public function submit_deduction_type_post(Request $request){
		
		$deduction_id = $request->deduction_id;
		
		$request->validate([
		  'deduction_description'   			=> ['required',Rule::unique('teves_payroll_deduction_type_table')->where( 
											fn ($query) =>$query
												->where('deduction_description', $request->deduction_description)
												->where('deduction_id', '<>',  $deduction_id )											
										)]
        ]);
		
			if($deduction_id==0){
				
				$DeductionType = new DeductionTypeModel();
				$DeductionType->deduction_description 		= $request->deduction_description;
				$DeductionType->created_by_user_idx 		= Session::get('loginID');
				
				$result = $DeductionType->save();
				
				if($result){
					return response()->json(['success'=>'Created']);
				}
				else{
					return response()->json(['success'=>'Error on Insert DeductionType Information']);
				}
				
			}else{
				
				$DeductionType = new DeductionTypeModel();
				$DeductionType = DeductionTypeModel::find($deduction_id);
				$DeductionType->deduction_description 		= $request->deduction_description;
				$DeductionType->updated_by_user_idx 		= Session::get('loginID');
				
				$result = $DeductionType->update();
				
				if($result){
					return response()->json(['success'=>'Updated']);
				}
				else{
					return response()->json(['success'=>'Error on Insert DeductionType Information']);
				}
								
			}
			
	}

}
