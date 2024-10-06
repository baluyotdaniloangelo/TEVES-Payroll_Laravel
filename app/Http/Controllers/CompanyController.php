<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CompanyModel;
use App\Models\User;
use Hash;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
	
	/*Load CompanyList Interface*/
	public function Company(){
		
			$title = 'Company List';
			$data = array();
			if(Session::has('loginID')){
				$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			}
	
			return view("amr.company", compact('data','title'));
		
	}   
	
	/*Fetch Site List using Datatable*/
	public function getCompanyList(Request $request)
    {

		$Company = CompanyModel::get();
		if ($request->ajax()) {
		
		$data= CompanyModel::select(
		'company_id',
		'company_name',
        'created_at',
        'updated_at'
		);		

		return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('action', function($row){	
					
						$actionBtn = '
						<div class="action_table_menu_switch">
						<a href="#" data-id="'.$row->company_id.'" class="bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editCompany" title="Update Company Information"></a>
						<a href="#" data-id="'.$row->company_id.'" class="bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteCompany" title="Delete Company Information"></a>
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
						return $row->created_at;
					}

				})
				
				->rawColumns(['action','created_at_dt_format','updated_at_dt_format'])
                ->make(true);
		}	
    }

	/*Fetch CompanyList Information*/
	public function company_info(Request $request){

		$CompanyID = $request->CompanyID;
		$data = CompanyModel::find($CompanyID, ['company_name','company_code']);
		return response()->json($data);
		
	}

	/*Delete CompanyList Information*/
	public function delete_company_confirmed(Request $request){
		
			$CompanyID = $request->CompanyID;
			CompanyModel::find($CompanyID)->delete();
			return 'Deleted';
		
	} 

	public function create_company_post(Request $request){
		
		$request->validate([
			'company_name'      	=> 'required|unique:meter_company_table,company_name',
        ], 
        [
			'company_name.required' => 'Company Name is Required',
        ]
		);

			#$data = $request->all();
			#insert
					
			$CompanyList = new CompanyModel();
			$CompanyList->company_name 	= $request->company_name;
			$CompanyList->created_by_user_idx 			= Session::get('loginID');
			$result = $CompanyList->save();
			
			if($result){
				return response()->json(['success'=>'Company Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Company Information']);
			}
	}
	
	public function update_company_post(Request $request){
		
			
					$request->validate([
					  'company_name'  => 'required|unique:meter_company_table,company_name,'.$request->CompanyID.',company_id',
					], 
					[
						'company_name.required' => 'Company Name is Required',
					]
					);
			
			#$data = $request->all();
			#insert		
			$CompanyList = new CompanyModel();
			$CompanyList = CompanyModel::find($request->CompanyID);
			$CompanyList->company_name 	= $request->company_name;
			$CompanyList->modified_by_user_idx 			= Session::get('loginID');
			$result = $CompanyList->update();
			
			if($result){
				return response()->json(['success'=>'Company Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Update Company Information']);
			}
	
	}
	
}
