<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\ProductTankModel;
use App\Models\TevesBranchModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Validation\Rule;
class ProductTankController extends Controller
{
	
	/*Tank List*/
	public function get_product_tank(Request $request){		

			$data =  ProductTankModel::RightJoin('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_product_tank_table.branch_idx')
					->RightJoin('teves_product_table', 'teves_product_table.product_id', '=', 'teves_product_tank_table.product_idx')
					->where('teves_product_tank_table.product_idx', $request->productID)
					->orderBy('teves_product_tank_table.tank_id', 'asc')
					->get([
						'teves_product_tank_table.tank_id',
						'teves_product_tank_table.branch_idx',
						'teves_product_tank_table.tank_name',
						'teves_product_tank_table.tank_capacity',
						'teves_branch_table.branch_name',
						'teves_branch_table.branch_code',
						'teves_product_table.product_unit_measurement'
					]);
		
			return response()->json($data);			
	}

	/*Tank List per Branch*/
	public function get_product_tank_per_branch(Request $request){		

			$data =  ProductTankModel::RightJoin('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_product_tank_table.branch_idx')
					->RightJoin('teves_product_table', 'teves_product_table.product_id', '=', 'teves_product_tank_table.product_idx')
					->where('teves_product_tank_table.product_idx', $request->productID)
					->where('teves_product_tank_table.branch_idx', $request->branchID)
					->orderBy('teves_product_tank_table.tank_id', 'asc')
					->get([
						'teves_product_tank_table.tank_id',
						'teves_product_tank_table.branch_idx',
						'teves_product_tank_table.tank_name',
						'teves_product_tank_table.tank_capacity',
						'teves_branch_table.branch_name',
						'teves_branch_table.branch_code',
						'teves_product_table.product_unit_measurement'
					]);
		
			return response()->json($data);			
	}

	public function create_tank_post(Request $request){

		$request->validate([
          'tank_name'      	=> ['required',Rule::unique('teves_product_tank_table')->where( 
									fn ($query) =>$query
										->where('product_idx', $request->product_idx)
										->where('tank_name', $request->tank_name)
										->where('branch_idx', $request->branch_idx) 
									)],
		  'tank_capacity'   	=> 'required',
		  'branch_idx'   	=> 'required'
        ], 
        [
			'tank_name.required' => 'Tank Name is required',
			'tank_capacity.required' => 'Tank Capacity is Required',
			'branch_idx.required' => 'Branch is Required',
        ]
		);			

			$ProductTank = new ProductTankModel();
			$ProductTank->product_idx						= $request->product_idx;
			$ProductTank->tank_name 						= $request->tank_name;
			$ProductTank->tank_capacity 					= $request->tank_capacity;
			$ProductTank->branch_idx 						= $request->branch_idx;
			
			$result = $ProductTank->save();
			
			if($result){
				return response()->json(['success'=>'Tank Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Tank Information']);
			}
	}
	
	public function product_tank_info(Request $request){

		$tankID = $request->tankID;
		$data = ProductTankModel::find($tankID, ['branch_idx','tank_name','tank_capacity']);
		return response()->json($data);
		
	}

	public function update_tank_post(Request $request){

		$request->validate([
          'tank_name'      	=> ['required',Rule::unique('teves_product_tank_table')->where( 
									fn ($query) =>$query
										->where('product_idx', $request->product_idx)
										->where('tank_name', $request->tank_name)
										->where('branch_idx', $request->branch_idx) 
										->where('tank_id', '<>',  $request->tank_id )
									)],
		  'tank_capacity'   	=> 'required',
		  'branch_idx'   	=> 'required'
        ], 
        [
			'tank_name.required' => 'Tank Name is required',
			'tank_capacity.required' => 'Tank Capacity is Required',
			'branch_idx.required' => 'Branch is Required',
        ]
		);			

			$ProductTank = ProductTankModel::find($request->tank_id);
			$ProductTank->product_idx						= $request->product_idx;
			$ProductTank->tank_name 						= $request->tank_name;
			$ProductTank->tank_capacity 					= $request->tank_capacity;
			$ProductTank->branch_idx 						= $request->branch_idx;
			
			$result = $ProductTank->update();
			
			if($result){
				return response()->json(['success'=>'Tank Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Tank Information']);
			}
	}

	/*Delete Meter Information*/
	public function delete_product_tank_confirmed(Request $request){

		$tank_id = $request->tankID;
		ProductTankModel::find($tank_id)->delete();
		return 'Deleted';
		
	} 
 	
}
