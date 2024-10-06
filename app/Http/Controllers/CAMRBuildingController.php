<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\SiteModel;
use App\Models\BuildingModel;
use App\Models\MeterModel;
use Session;
use Validator;

class CAMRBuildingController extends Controller
{

	/*Fetch Site Building*/
	public function get_building_accordion(Request $request)
    {
		
		$data =  BuildingModel::where('meter_site_id', $request->siteID)
				->orderBy('building_code', 'asc')
              	->get([
					'id',
					'building_code',
					'building_description'
					]);

		return response()->json($data);
    }

	/*Fetch Site Information*/
	public function building_info(Request $request){

		$buildingID = $request->buildingID;
		$data = BuildingModel::find($buildingID);
		return response()->json($data);
		
	}

	/*Delete Site Information*/
	public function delete_building_confirmed(Request $request){

		$buildingID = $request->buildingID;
		BuildingModel::find($buildingID)->delete();
		return 'Deleted';
		
	} 

	public function create_building_post(Request $request){

		$request->validate([
		  'building_code'      		=> ['required',Rule::unique('meter_building_table')->where( 
											fn ($query) =>$query
												->where('building_code', $request->building_code)
												->where('meter_site_id', $request->siteID) 
											)],
		  'building_description'    => ['required',Rule::unique('meter_building_table')->where( 
											fn ($query) =>$query
												->where('building_description', $request->building_description)
												->where('meter_site_id', $request->siteID) 
											)],
        ], 
        [
			'building_code.required' => 'Building Code is Required',
			'building_description.required' => 'Building Description is Required'
        ]
		);
					
			$bldg = new BuildingModel();
			$bldg->building_code = $request->building_code;
			$bldg->building_description = $request->building_description;
			$bldg->meter_site_id = $request->siteID;
			
			$result = $bldg->save();
			
			if($result){
				return response()->json(['success'=>'Building Information Successfully Created!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Site Information']);
			}
	}

	public function update_building_post(Request $request){

		$request->validate([
		  'building_code'      		=> ['required',Rule::unique('meter_building_table')->where( 
											fn ($query) =>$query
												->where('building_code', $request->building_code)
												->where('meter_site_id', $request->siteID) 
												->where('id', '<>',  $request->buildingID )
											)],
		  'building_description'    => ['required',Rule::unique('meter_building_table')->where( 
											fn ($query) =>$query
												->where('building_description', $request->building_description)
												->where('meter_site_id', $request->siteID)
												->where('id', '<>',  $request->buildingID )												
											)],
        ], 
        [
			'building_code.required' => 'Building Code is Required',
			'building_description.required' => 'Building Description is Required'
        ]
		);
		
			$bldg = new BuildingModel();
			$bldg = BuildingModel::find($request->buildingID);
			$bldg->building_code = $request->building_code;
			$bldg->building_description = $request->building_description;
			
			/*Update Meter Info*/			
			$MeterUpdateBuildingInfo = MeterModel::where('building_id', '=', $request->buildingID)
				->update(
					[
					'building_code' => $request->building_code,
					'building_description' => $request->building_description,
					],
				);
			
			$result = $bldg->update();
			
			if($result){
				return response()->json(['success'=>'Building Information Successfully Updated!']);
			}
			else{
				return response()->json(['success'=>'Error on Insert Site Information']);
			}
	}


}
