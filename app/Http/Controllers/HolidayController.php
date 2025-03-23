<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\HolidayModel;
use Session;
use Validator;
use DataTables;

class holidayController extends Controller
{
	
	/*Load holiday Interface*/
	public function holiday(){

		if(Session::has('loginID')){	
			$title = 'Holiday';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			$client_data = HolidayModel::all();		
			
			$active_link = 'holiday';
		
			return view("payroll.holiday", compact('data','title', 'active_link'));
		}
		
	}   

	/*Fetch holiday List using Datatable*/
	public function getholidayList(Request $request)
    {
		//$list = HolidayModel::get();
		if ($request->ajax()) {
			$data = HolidayModel::select(
			'holiday_id',
			'holiday_description',
			'holiday_date',
			'holiday_type');
			return DataTables::of($data)
					->addIndexColumn()
					->addColumn('action', function($row){
						
						$actionBtn = '
							<div class="dropdown dropdown-action">
								<a href="#" class="action-icon dropdown-toggle " data-bs-toggle="dropdown" aria-expanded="true"><i class="si si-options-vertical" data-bs-toggle="tooltip" aria-label="si-options-vertical" data-bs-original-title="si-options-vertical"></i></a>
									<div class="dropdown-menu dropdown-menu-right " data-popper-placement="bottom-end" style="position: absolute; inset: 0px 0px auto auto; margin: 0px; transform: translate(0px, 34px);">
										<a class="dropdown-item" href="#" data-id="'.$row->holiday_id.'" id="edit_holiday"><i class="fa-solid fa-pencil m-r-5"></i> Edit</a>
										<a class="dropdown-item" href="#" data-id="'.$row->holiday_id.'" id="delete_holiday"><i class="fa-regular fa-trash-can m-r-5"></i> Delete</a>
									</div>
							</div>';
					
						return $actionBtn;
					})
					->rawColumns(['action'])
					->make(true);
		}
    }

	/*Fetch holiday Information*/
	public function holiday_info(Request $request){
		
		$holidayID = $request->holidayID;
		$data = HolidayModel::find($holidayID, ['holiday_id', 'holiday_description', 'holiday_type', 'holiday_date']);
		return response()->json($data);

	}

	/*Delete holiday Information*/
	public function delete_holiday_confirmed(Request $request){

		$holidayID = $request->holidayID;
		HolidayModel::find($holidayID)->delete();
		
		return 'Deleted';

	} 
	
	/*Create/Update holiday Information*/
	public function submit_holiday_post(Request $request){
		
		$holiday_id = $request->holiday_id;
		
		$request->validate([
		  'holiday_description'   			=> ['required',Rule::unique('teves_payroll_holiday_table')->where( 
											fn ($query) =>$query
												->where('holiday_description', $request->holiday_description)
												->where('holiday_date', $request->holiday_date)
												->where('holiday_type', $request->holiday_type)
												->where('holiday_id', '<>',  $holiday_id )											
										)]
        ]);
		
			if($holiday_id==0){
				
				$holiday = new HolidayModel();
				$holiday->holiday_description 		= $request->holiday_description;
				$holiday->holiday_date 				= $request->holiday_date;
				$holiday->holiday_type 				= $request->holiday_type;
				$holiday->created_by_user_idx 		= Session::get('loginID');
				
				$result = $holiday->save();
				
				if($result){
					return response()->json(['success'=>'Created']);
				}
				else{
					return response()->json(['success'=>'Error on Insert holiday Information']);
				}
				
			}else{
				
				$holiday = new HolidayModel();
				$holiday = HolidayModel::find($holiday_id);
				$holiday->holiday_description 		= $request->holiday_description;
				$holiday->holiday_date 				= $request->holiday_date;
				$holiday->holiday_type 				= $request->holiday_type;
				$holiday->updated_by_user_idx 		= Session::get('loginID');
				
				$result = $holiday->update();
				
				if($result){
					return response()->json(['success'=>'Updated']);
				}
				else{
					return response()->json(['success'=>'Error on Insert Holiday Information']);
				}
								
			}
			
	}

}
