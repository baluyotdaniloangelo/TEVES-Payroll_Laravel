<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\User;
use App\Models\ProductModel;
use App\Models\TevesBranchModel;
use Session;
use Validator;
use DataTables;

class ProductController extends Controller
{
	
	/*Load Product Interface*/
	public function product(){
		
		if(Session::has('loginID') && Session::get('UserType')=="Admin"){
			
			$title = 'Product';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			
			$product_data = ProductModel::all();
			return view("pages.product", compact('data','title'));
		
		}
		
	}   
	
	/*Fetch Product List using Datatable*/
	public function getProductList(Request $request)
    {

		$list = ProductModel::get();
		if ($request->ajax()) {

    	$data = ProductModel::select(
		'product_id',
		'product_name',
		'product_price',
		'product_unit_measurement');
		

		return DataTables::of($data)
				->addIndexColumn()
                ->addColumn('action', function($row){
					
					
					$actionBtn = '<div align="center" class="action_table_menu_Product">
					<a href="update_product_information?productID='.$row->product_id.'&tab=branchprice" data-id="'.$row->product_id.'" class="btn-warning btn-circle btn-sm bi bi-pencil-fill btn_icon_table btn_icon_table_edit" id="" title="Edit Product Information, Add Price per branch and Tank"></a>
					<a href="#" data-id="'.$row->product_id.'" class="btn-danger btn-circle btn-sm bi-trash3-fill btn_icon_table btn_icon_table_delete" id="deleteProduct"></a>
					</div>';
					
                    return $actionBtn;

                })
				
				->rawColumns(['action'])
                ->make(true);
		}	
    }

	/*Fetch Product Information*/
	public function product_info(Request $request){

		$productID = $request->productID;
		$data = ProductModel::find($productID, ['product_name','product_price','product_unit_measurement']);
		return response()->json($data);
		
	}

	/*Delete Product Information*/
	public function delete_product_confirmed(Request $request){

		$productID = $request->productID;
		ProductModel::find($productID)->delete();
		return 'Deleted';
		
	} 

	public function create_product_post(Request $request){

		$request->validate([
          'product_name'      		=> 'required|unique:teves_product_table,product_name',
		  'product_price'      		=> 'required'
        ], 
        [
			'product_name.required' => 'Product Name is required',
			'product_price.required' => 'Price is Required'
        ]
		);

			$Product = new ProductModel();
			
			$Product->product_name 						= $request->product_name;
			$Product->product_price 					= $request->product_price;
			$Product->product_unit_measurement 			= $request->product_unit_measurement;
			
			$result = $Product->save();
			
			/*Get Last ID*/
			$last_transaction_id = $Product->product_id;
			
			/*Create Entry at Product Price Per Branch*/
			
			DB::insert("INSERT INTO teves_product_branch_price_table (product_idx,branch_idx,branch_price)
			SELECT b.product_id,a.branch_id,b.product_price FROM teves_branch_table AS a,teves_product_table AS b
			WHERE b.product_id=?",[$last_transaction_id]);
			
			if($result){
				return response()->json(array('success' => "Product Information Successfully Created!, System will proceed to Update the Pricing per Branch", 'productID' => $last_transaction_id), 200);
		
				return response()->json(['success'=>'Product Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Product Information']);
			}
	}

	public function update_product_post(Request $request){
		
		$request->validate([
          'product_name'      		=> 'required|unique:teves_product_table,product_name,'.$request->productID.',product_id',
		  'product_price'      		=> 'required'
        ], 
        [
			'product_name.required' => 'Product Name is required',
			'product_price.required' => 'Price is Required'
        ]
		);
	
			$Product = new ProductModel();
			$Product = ProductModel::find($request->productID);
			
			$Product->product_name 						= $request->product_name;
			$Product->product_price 					= $request->product_price;
			$Product->product_unit_measurement 			= $request->product_unit_measurement;
						
			$result = $Product->update();
			
			if($result){
				return response()->json(['success'=>'Product Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Update Product Information']);
			}
	}
	
	/*Pricing List*/
	public function get_product_pricing_per_branch(Request $request){		

			$data =  ProductModel::Join('teves_product_branch_price_table', 'teves_product_table.product_id', '=', 'teves_product_branch_price_table.product_idx')
              		->Join('teves_branch_table', 'teves_branch_table.branch_id', '=', 'teves_product_branch_price_table.branch_idx')
					->where('teves_product_table.product_id', $request->productID)
					->orderBy('teves_product_branch_price_table.branch_price_id', 'asc')
					->get([
						'teves_product_branch_price_table.branch_price_id',
						'teves_branch_table.branch_code',
						'teves_product_branch_price_table.branch_price',
					]);
		
			return response()->json($data);			
	}

	public function save_branches_product_pricing_post(Request $request){		
			
			$request->validate([
			'branch_price'  	=> 'required'
			], 
			[
				'branch_price.required' 	=> 'Branch Price is Required'
			]
			);
			
			$branch_price_id 	= $request->branch_price_id;
			$branch_price 		= $request->branch_price;
			
			if($branch_price!=''){
				for($count = 0; $count < count($branch_price_id); $count++)
				{
					
						$branch_price_id_item 		= $branch_price_id[$count];
						$branch_price_item 			= number_format($branch_price[$count],2, '.', '');
						
						if(Session::get('UserType')=="Admin"){
								
							DB::table('teves_product_branch_price_table')
							->where('branch_price_id', $branch_price_id_item)
							->update(['branch_price' => $branch_price_item]);
							
						}					
				}		
			
			return response()->json(array('success' => "Receivable Payment Successfully Updated!"), 200);
			
			}							
	}		
	
	/*Pricing List*/
	public function get_product_list_pricing_per_branch(Request $request){		

			$raw_query_product = "SELECT a.product_id,a.product_name,ifnull(b.branch_price,a.product_price) AS product_price ,c.branch_code FROM teves_product_table AS a
			LEFT JOIN teves_product_branch_price_table b ON b.product_idx = a.product_id LEFT JOIN teves_branch_table c ON c.branch_id = b.branch_idx
			WHERE c.branch_id = ?";			
			$product_data = DB::select("$raw_query_product", [$request->branch_idx]);

			return response()->json($product_data);			
	}
	
	/*Load Form Form Interface*/
	public function update_product_information(Request $request){
		
		if(Session::has('loginID')){
			
			$productID = $request->productID;
			$tab = $request->tab;
			
			$title = 'Update Product Information';
			$data = array();
			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();/*User Data*/
			
			$teves_branch = TevesBranchModel::all();
			
			return view("pages.update_product_information_form", compact('data','title','teves_branch', 'tab', 'productID'));
		
		}
		
	}  		
	
}
