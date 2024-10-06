<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SupplierModel;
use Session;
use Validator;
use DataTables;

class SupplierController extends Controller
{
	
	/*Load supplier Interface*/
	public function supplier(){
		
		
		
		if(Session::has('loginID') && Session::get('UserType')=="Admin"){
			
			$title = 'Supplier';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			$supplier_data = SupplierModel::all();	
			return view("pages.supplier", compact('data','title'));
		}
		
		
	
	}   
	
	/*Fetch supplier List using Datatable*/
	public function getSupplierList(Request $request)
    {
		$list = SupplierModel::get();
		if ($request->ajax()) {
			$data = SupplierModel::select(
			'supplier_id',
			'supplier_name',
			'supplier_address',
			'supplier_tin',
			'default_less_percentage',
			'default_net_percentage',
			'default_vat_percentage',
			'default_withholding_tax_percentage',
			'default_payment_terms');
			return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function($row){
						$actionBtn = '
						<div align="center" class="action_table_menu_supplier">
						<a href="#" data-id="'.$row->supplier_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="editsupplier"></a>
						<a href="#" data-id="'.$row->supplier_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deletesupplier"></a>
						</div>';
						return $actionBtn;
					})
					->rawColumns(['action'])
					->make(true);
		}
    }

	/*Fetch supplier Information*/
	public function supplier_info(Request $request){
		$supplierID = $request->supplierID;
		$data = SupplierModel::find($supplierID, ['supplier_name','supplier_address','supplier_tin','default_less_percentage','default_net_percentage','default_vat_percentage','default_withholding_tax_percentage','default_payment_terms']);
		return response()->json($data);
	}

	/*Delete supplier Information*/
	public function delete_supplier_confirmed(Request $request){
		$supplierID = $request->supplierID;
		SupplierModel::find($supplierID)->delete();
		return 'Deleted';
	} 

	public function create_supplier_post(Request $request){
		
		$request->validate([
          'supplier_name'      => 'required|unique:teves_supplier_table,supplier_name',
		  'supplier_address'   => 'required',
		  'supplier_tin'    => 'required'
        ], 
        [
			'supplier_name.required' => 'Supplier Name is required',
			'supplier_address.required' => 'Address is Required',
			'supplier_tin.required' => 'TIN is Required'
        ]
		);
			$supplier = new SupplierModel();
			$supplier->supplier_name 			= $request->supplier_name;
			$supplier->supplier_address 		= $request->supplier_address;
			$supplier->supplier_tin 			= $request->supplier_tin;
			
			$supplier->default_less_percentage 			= $request->default_less_percentage;
			$supplier->default_net_percentage 			= $request->default_net_percentage;
			$supplier->default_vat_percentage 			= $request->default_vat_percentage;
			$supplier->default_withholding_tax_percentage = $request->default_withholding_tax_percentage;
			$supplier->default_payment_terms 				= $request->default_payment_terms;
			
			$result = $supplier->save();
			if($result){
				return response()->json(['success'=>'Supplier Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert supplier Information']);
			}
	}

	public function update_supplier_post(Request $request){
		
		$request->validate([
          'supplier_name'      	=> 'required|unique:teves_supplier_table,supplier_name,'.$request->supplierID.',supplier_id',
		  'supplier_address'    => 'required',
		  'supplier_tin'      	=> 'required'
        ], 
        [
			'supplier_name.required'	=> 'Supplier Name is required',
			'supplier_address.required' => 'Address is Required',
			'supplier_tin.required' 	=> 'TIN is Required'
        ]
		);
		
			$supplier = new SupplierModel();
			$supplier = SupplierModel::find($request->supplierID);
			$supplier->supplier_name 			= $request->supplier_name;
			$supplier->supplier_address 		= $request->supplier_address;
			$supplier->supplier_tin 			= $request->supplier_tin;
			
			$supplier->default_less_percentage 			= $request->default_less_percentage;
			$supplier->default_net_percentage 			= $request->default_net_percentage;
			$supplier->default_vat_percentage 			= $request->default_vat_percentage;
			$supplier->default_withholding_tax_percentage = $request->default_withholding_tax_percentage;
			$supplier->default_payment_terms 				= $request->default_payment_terms;
			
			
			$result = $supplier->update();
			if($result){
				return response()->json(['success'=>'supplier Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Update supplier Information']);
			}
			
	}
}
