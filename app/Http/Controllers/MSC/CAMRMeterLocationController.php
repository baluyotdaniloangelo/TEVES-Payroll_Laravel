<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\SiteModel;
use App\Models\MeterLocationModel;
use App\Models\MeterModel;
use App\Models\GatewayModel;
use Session;
use Validator;
use Illuminate\Support\Facades\DB;

use DataTables;

class CAMRMeterLocationController extends Controller
{

	/*Fetch Site Building EE Room Location*/
	public function get_ee_room_location_accordion(Request $request)
    {
		$siteID = $request->siteID;
		
		$data =  MeterLocationModel::where('site_idx', $siteID)
				->orderBy('location_code', 'asc')
              	->get([
					'location_id',
					'location_code',
					'location_description'
					]);

		return response()->json($data);
    }

	/*Fetch Gateway List using Datatable*/
	public function get_MeterLocation(Request $request)
    {

		$siteID = $request->siteID;
		
		if ($request->ajax()) {	
		
		$data =  MeterLocationModel::where('site_idx', $siteID)
				->orderBy('location_code', 'asc')
              	->get([
					'location_id',
					'location_code',
					'location_description'
					]);
	

		return DataTables::of($data)
				->addIndexColumn()
	
                ->addColumn('action', function($row){
					
					$actionBtn = '
					<div align="center" class="action_table_menu_gateway">
					<a href="#" title="Click to Edit" data-id="'.$row->location_id.'" style="cursor: pointer;" class="btn-warning btn-circle bi bi-pencil-fill btn_icon_accordion btn_icon_table_edit" id="editMeterLocation"></a>
					<a href="#" title="Click to Delete" data-id="'.$row->location_id.'" style="cursor: pointer;" class="btn-danger btn-circle bi-trash3-fill btn_icon_accordion btn_icon_table_delete" id="deleteMeterLocation"></a>
					</div>';
					
                    return $actionBtn;
			
                })
				
				->rawColumns(['action'])
                ->make(true);	
		}		
    }

	/*Fetch Site Information*/
	public function meter_location_info(Request $request){

		$meterlocationID = $request->meterlocationID;
		
		$raw_query_location_info = "SELECT a.location_id, a.site_idx, a.location_code, a.location_description FROM meter_location_table AS a
						WHERE a.location_id = ?";			
						$location_info = DB::select("$raw_query_location_info", [$meterlocationID]);		
						return response()->json($location_info);
		
	}

	/*Delete Site Information*/
	public function delete_meter_location_confirmed(Request $request){

		$meterlocationID = $request->meterlocationID;
		MeterLocationModel::find($meterlocationID)->delete();
		
		/*Delete Meters*/
		MeterModel::where('location_idx', $meterlocationID)->delete();
		
		/*Delete Gateway*/
		GatewayModel::where('location_idx', $meterlocationID)->delete();

		return 'Deleted';
		
	} 

	public function create_meter_location_post(Request $request){

		$request->validate([
		  'location_code'      		=> ['required',Rule::unique('meter_location_table')->where( 
											fn ($query) =>$query
												->where('location_code', $request->location_code)
												->where('site_idx', $request->siteID) 
											)],
		  'location_description'    => ['required',Rule::unique('meter_location_table')->where( 
											fn ($query) =>$query
												->where('location_description', $request->location_description)
												->where('site_idx', $request->siteID) 
											)],
        ], 
        [
			'location_code.required' => 'Meter Location Code is Required',
			'location_description.required' => 'Meter Location Description is Required'
        ]
		);	
					
			$eeroomlocation = new MeterLocationModel();
			$eeroomlocation->location_code = $request->location_code;
			$eeroomlocation->location_description = $request->location_description;
			$eeroomlocation->site_idx = $request->siteID;
			$eeroomlocation->created_by_user_idx 	= Session::get('loginID');
			
			$result = $eeroomlocation->save();
			
			if($result){
				return response()->json(['success'=>'Meter Location Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Meter Location Information']);
			}
	}

	public function update_meter_location_post (Request $request){

		$request->validate([ 
		  'location_code'      		=> ['required',Rule::unique('meter_location_table')->where( 
											fn ($query) =>$query
												->where('location_code', $request->location_code)
												->where('site_idx', $request->siteID) 
												->where('location_id', '<>',  $request->meterlocationID )
											)],
		  'location_description'    => ['required',Rule::unique('meter_location_table')->where( 
											fn ($query) =>$query
												->where('location_description', $request->location_description)
												->where('site_idx', $request->siteID)
												->where('location_id', '<>',  $request->meterlocationID )												
											)],
        ], 
        [
			'location_code.required' => 'Meter Location Code is Required',
			'location_description.required' => 'Meter Location Description is Required'
        ]
		);
		
			$eeroomlocation = new MeterLocationModel();
			$eeroomlocation = MeterLocationModel::find($request->meterlocationID);
			$eeroomlocation->location_code = $request->location_code;
			$eeroomlocation->location_description = $request->location_description;
			$eeroomlocation->modified_by_user_idx 	= Session::get('loginID');
			$result = $eeroomlocation->update();
			
			if($result){
				return response()->json(['success'=>'Building Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Site Information']);
			}
			
	}

}
