<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SiteModel;
use App\Models\MeterModel;
use App\Models\BuildingModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
/*Excel*/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiteReportController extends Controller
{
	
	/*Load SAP Report Interface*/
	public function site_report(){
	
		if(Session::has('loginID')){	

			$title = 'Building Report';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();			
		
			if($data->user_access=='ALL'){
			// if($data->user_type=='Admin'){
				
						$site_data = SiteModel::leftjoin('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
						->get([
						'meter_site.site_id',
						'meter_building_table.building_code',
						'meter_building_table.building_description'
						]);
				
			}else{
						
					$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('user_access_group', 'user_access_group.site_idx', '=', 'meter_site.site_id')
					->where('user_idx', $data->user_id)
              		->get([
					'meter_site.site_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description']);
	
			}
		
			return view("amr.site_report", compact('data','title','site_data'));
	
		}		
	
	}  	

	public function generate_site_report(Request $request){

		$request->validate([
          'site_id'      		=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Building'
        ]
		);

 	if ($request->ajax()) {	

		$site_id 	= $request->site_id;
		$meter_role = $request->meter_role;
		
		$start_date = $request->start_date;
		$start_time = $request->start_time;
		$end_date 	= $request->end_date;
		$end_time 	= $request->end_time;
		
		$beginning_date	 = "$start_date $start_time";
		$ending_date	 = "$end_date $end_time";
		
		$valid_sap_meter 	= $request->valid_sap_meter;
		if($valid_sap_meter=='true'){ $valid_sap_meter_status = "yes"; }else{ $valid_sap_meter_status = ""; }
		
		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->site_id,)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description'
					]);
					
		$building_code = $site_data[0]->building_code;	
		
	   	/*Query Select using Raw*/
		$data = MeterModel::selectRaw("meter_details.meter_name,meter_details.customer_name, meter_building_table.building_code,meter_details.meter_type,meter_details.meter_multiplier,meter_rtu.gateway_sn,meter_location_table.location_code")
		->selectRaw(" IFNULL((select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` > '$beginning_date' /*order by `datetime` desc*/ limit 0, 1 ), '0')  as `start_reading`")	
		->selectRaw(" IFNULL((select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` > '$beginning_date' /*order by `datetime` desc*/ limit 0, 1 ), '0000-00-00 00:00:00')  as `start_reading_datetime`")	
		->selectRaw(" IFNULL((select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` > '$beginning_date' and `datetime` < '$ending_date' order by `datetime` desc limit 0, 1 ), '0')  as `ending_reading`")	
		->selectRaw(" IFNULL((select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` > '$beginning_date' and `datetime` < '$ending_date' order by `datetime` desc limit 0, 1 ), '0000-00-00 00:00:00')  as `ending_reading_datetime`")	
		->where('meter_details.site_idx', $site_id)
		->where("meter_details.meter_status", 'Active')
		->join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_details.site_idx')
		->join('meter_rtu', 'meter_rtu.rtu_id', '=', 'meter_details.rtu_idx')
		->join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_details.location_idx')
		->where(function ($r) use($meter_role) {
			if ($meter_role) {
			   $r->where('meter_role', $meter_role);
			}
			})
       ->get();
	   
	    $result = array();
		foreach ($data as $data_cols){
	   
				@$meter_name = $data_cols->meter_name;
				@$customer_name = $data_cols->customer_name;
				@$gateway_sn = $data_cols->gateway_sn;
				@$meter_multiplier = $data_cols->meter_multiplier;
				@$meter_type = $data_cols->meter_type;
				@$building_code = $data_cols->building_code;
				@$location_code = $data_cols->location_code;
				
				@$start_reading = $data_cols->start_reading * $data_cols['meter_multiplier'];
				@$start_reading_datetime = $data_cols->start_reading_datetime;
				@$ending_reading = $data_cols->ending_reading * $data_cols['meter_multiplier'];
				@$ending_reading_datetime = $data_cols->ending_reading_datetime;
	
				$KWh = ($data_cols['ending_reading'] - $data_cols['start_reading']) * $data_cols['meter_multiplier'];		
							
				/*Dont Include Meters with no Current Reading/Consumption July 2, 2024*/
				if( $KWh>=1 ){
				
					$result[] = array(
					'meter_name' => $meter_name,
					'customer_name' => $customer_name,
					'gateway_sn' => $gateway_sn,
					'meter_multiplier' => $meter_multiplier,
					'meter_type' => $meter_type,
					'building_code' => $building_code,
					'location_code' => $location_code,
					'meter_multiplier' => $meter_multiplier,
					'start_reading' => $start_reading,
					'start_reading_datetime' => @$start_reading_datetime,
					'ending_reading' => @$ending_reading,
					'ending_reading_datetime' => @$ending_reading_datetime,
					'current_consumption' => @$KWh
					);
					
				}	  
		}	
		return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
		}
		
		// return response()->json($result);
		
	}

	public function generate_site_report_excel(Request $request){

		$request->validate([
			'site_id'      			=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Site'
        ]
		);

		$site_id 	= $request->site_id;
		$meter_role = $request->meter_role;
		$building_id = $request->building_id;
		
		$start_date = $request->start_date;
		$start_time = $request->start_time;
		$end_date 	= $request->end_date;
		$end_time 	= $request->end_time;
		
		$beginning_date	 = "$start_date $start_time";
		$ending_date	 = "$end_date $end_time";
		
		$valid_sap_meter 	= $request->valid_sap_meter;
		if($valid_sap_meter=='true'){ $valid_sap_meter_status = "yes"; }else{ $valid_sap_meter_status = ""; }
				
		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->site_id,)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description'
					]);
					
		$building_code = $site_data[0]->building_code;	
		$building_description = $site_data[0]->building_description;
		
	   ini_set('max_execution_time', 0);
       // ini_set('memory_limit', '500M');
       try {
		   ob_start();
           $spreadSheet = new Spreadsheet();

			
				$spreadSheet = IOFactory::load(public_path('/template/Site Report ALL.xlsx'));

				$spreadSheet->getActiveSheet()
					->setCellValue('B2', $building_code)
					->setCellValue('B3', $building_description)
					->setCellValue('B4', $beginning_date)
					->setCellValue('B5', $ending_date);
				
			 
				$styleBorder_prepared = array(
					'borders' => array(
						'bottom' => array(
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							'color' => array('rgb' => '000000'),
						),
					),
				);
				
				$styleBorder = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ];
		
		$style_RIGHT = array(
		'alignment' => array(
			'horizontal' => Alignment::HORIZONTAL_RIGHT,));			
			
			$no_excl = 11;
			$n = 1;
		
	   	/*Query Select using Raw*/
		$data = MeterModel::selectRaw("meter_details.meter_name,meter_details.customer_name, meter_building_table.building_code,meter_details.meter_type,meter_details.meter_multiplier,meter_rtu.gateway_sn,meter_location_table.location_code")
		->selectRaw(" IFNULL((select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` >= '$beginning_date' /*order by `datetime` desc*/ limit 0, 1 ), '0')  as `start_reading`")	
		->selectRaw(" IFNULL((select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` >= '$beginning_date' /*order by `datetime` desc*/ limit 0, 1 ), '0000-00-00 00:00:00')  as `start_reading_datetime`")	
		->selectRaw(" IFNULL((select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` <= '$ending_date' order by `datetime` desc limit 0, 1 ), '0')  as `ending_reading`")	
		->selectRaw(" IFNULL((select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_name and `datetime` <= '$ending_date' order by `datetime` desc limit 0, 1 ), '0000-00-00 00:00:00')  as `ending_reading_datetime`")	
		->where('meter_details.site_idx', $site_id)
		->where("meter_details.meter_status", 'Active')
		->join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_details.site_idx')
		->join('meter_rtu', 'meter_rtu.rtu_id', '=', 'meter_details.rtu_idx')
		->join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_details.location_idx')
		->where(function ($r) use($meter_role) {
			if ($meter_role) {
			   $r->where('meter_role', $meter_role);
			}
			})
       ->get();
			
			foreach ($data as $sap_data_column){
				
				/*Date and Time*/
				$datetime = $sap_data_column['current_reading_datetime'];
				$date2=date_create($datetime);
				$date_generated = date_format($date2,"m/d/Y");
				$time_generated = date_format($date2,"H:i:s");
			
				$KWh = ($sap_data_column['ending_reading'] - $sap_data_column['start_reading']) * $sap_data_column['meter_multiplier'];
			
			if( $KWh>=1 ){
				
				$spreadSheet->getActiveSheet()
					->setCellValue('A'.$no_excl, $n)
					->setCellValue('B'.$no_excl, $sap_data_column['customer_name'])
					->setCellValue('C'.$no_excl, $sap_data_column['meter_name'])
					->setCellValue('D'.$no_excl, $sap_data_column['gateway_sn'])
					->setCellValue('E'.$no_excl, $sap_data_column['location_code'])
					->setCellValue('F'.$no_excl, $sap_data_column['start_reading_datetime'])
					->setCellValue('G'.$no_excl, $sap_data_column['start_reading'] * $sap_data_column['meter_multiplier'] +0)
					->setCellValue('H'.$no_excl, $sap_data_column['ending_reading_datetime'])
					->setCellValue('I'.$no_excl, $sap_data_column['ending_reading'] * $sap_data_column['meter_multiplier'] +0)
					->setCellValue('J'.$no_excl, $sap_data_column['meter_multiplier'])
					->setCellValue('K'.$no_excl, $KWh);
					
			/*Increment*/
			$no_excl++;
			$n++;
			
			}
			} 
			
		  $Excel_writer = new Xlsx($spreadSheet);
		  
			$_server_time	=	date('Y_m_d_H_i_s');
			
			// $report_name = "$building_description"."_$building_code". "_$_server_time";
			$report_name = "$building_description"."_$building_code"."_Building Report"."_$_server_time";
			$_report_name = str_replace(' ','%20',$report_name);
		  
		   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
           header("Content-Disposition: attachment;filename=$_report_name.xlsx");
           header('Cache-Control: max-age=0');
           ob_end_clean();
           $Excel_writer->save('php://output');
           exit();
       
	   } catch (Exception $e) {
           return;
       }
	   
	}	

}