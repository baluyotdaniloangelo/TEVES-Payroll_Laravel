<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DivisionModel;
use App\Models\User;
use Hash;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Storage;

class DivisionController extends Controller
{
	
	/*Load DivisionList Interface*/
	public function division(){
		
			$title = 'Division List';
			$data = array();
			if(Session::has('loginID')){
				$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			}
	
			return view("amr.division", compact('data','title'));
		
	}   
	
	/*Fetch Site List using Datatable*/
	public function getDivisionList(Request $request)
    {

		$Division = DivisionModel::get();
		if ($request->ajax()) {
		
		$data= DivisionModel::select(
		'division_id',
		'division_code',
		'division_name',
        'created_at',
        'updated_at'
		);		

		return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('action', function($row){	
					
						$actionBtn = '
						<div class="action_table_menu_switch">
						<a href="#" data-id="'.$row->division_id.'" class="bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editDivision" title="Update Division Information"></a>
						<a href="#" data-id="'.$row->division_id.'" class="bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteDivision" title="Delete Division Information"></a>
						</div>';
					
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

	/*Fetch DivisionList Information*/
	public function division_info(Request $request){

		$DivisionID = $request->DivisionID;
		$data = DivisionModel::find($DivisionID, ['division_name','division_code']);
		return response()->json($data);
		
	}

	/*Delete DivisionList Information*/
	public function delete_division_confirmed(Request $request){
		
			$DivisionID = $request->DivisionID;
			DivisionModel::find($DivisionID)->delete();
			return 'Deleted';
		
	} 

	public function create_division_post(Request $request){
		
		$request->validate([
			'division_code'      	=> 'required|unique:meter_division_table,division_code',
			'division_name'      	=> 'required|unique:meter_division_table,division_name',
        ], 
        [
			'division_code.required' => 'Division Code is Required',
			'division_name.required' => 'Division Name is Required',
        ]
		);

			#$data = $request->all();
			#insert
					
			$DivisionList = new DivisionModel();
			$DivisionList->division_code 				= $request->division_code;
			$DivisionList->division_name 				= $request->division_name;
			$DivisionList->created_by_user_idx 			= Session::get('loginID');
			$result = $DivisionList->save();
			
			if($result){
				return response()->json(['success'=>'Division Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Division Information']);
			}
	}
	
	public function update_division_post(Request $request){
		
			
					$request->validate([
					  'division_code'  => 'required|unique:meter_division_table,division_code,'.$request->DivisionID.',division_id',
					  'division_name'  => 'required|unique:meter_division_table,division_name,'.$request->DivisionID.',division_id',
					], 
					[
						'division_code.required' => 'Division Code is Required',
						'division_name.required' => 'Division Name is Required',
					]
					);
			
			#$data = $request->all();
			#insert		
			$DivisionList = new DivisionModel();
			$DivisionList = DivisionModel::find($request->DivisionID);
			$DivisionList->division_code 					= $request->division_code;
			$DivisionList->division_name 					= $request->division_name;
			$DivisionList->modified_by_user_idx 			= Session::get('loginID');
			
			$result = $DivisionList->update();
			
			if($result){
				return response()->json(['success'=>'Division Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Update Division Information']);
			}
	
	}
	
}
