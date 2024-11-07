<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SiteModel;
use App\Models\MeterModel;
use App\Models\MeterDataModel;
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

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ConsumptionReportController extends Controller
{
	
	/*Load RAW Report Interface*/
	public function consumption_report(){
		
		if(Session::has('loginID')){	

			$title = 'Consumption Report';
			$data = array();
		
			$data = User::where('user_id', '=', Session::get('loginID'))->first();			
		
			if($data->user_access=='ALL'){
				
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
		
			return view("amr.meter_consumption_report", compact('data','title','site_data'));
	
		}	
	
	}  	
	
	public function consumption_report_hourly(Request $request){

		$request->validate([
          'site_id'      			=> 'required',
		  'meter_id'      			=> 'required',
		  'start_date'      		=> 'required',
		  'start_time'      		=> 'required',
		  'end_date'      			=> 'required',
		  'end_time'      			=> 'required'
        ], 
        [
			'site_id.required' 		=> 'Please select a Building',
			'meter_id.required' 	=> 'Please select a Meter',
			'start_date.required' 	=> 'Please select a Start Date',
			'start_time.required' 	=> 'Please select a Start Time',
			'end_date.required' 	=> 'Please select a End Date',
			'end_time.required' 	=> 'Please select a End Time'
        ]
		);

	if ($request->ajax()) {	

		$site_id 	= $request->site_id;
		$meter_id 	= $request->meter_id;
		$meter_role = $request->meter_role;
		$start_date = $request->start_date;
		$start_time = $request->start_time;
		$end_date 	= $request->end_date;
		$end_time 	= $request->end_time;

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->site_id,)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description'
					]);
					
		$building_code = $site_data[0]->building_code;
		
		$raw_meter_info = "SELECT meter_multiplier
				FROM meter_details
				WHERE meter_name = ? and site_idx = ?";	
							   
		$meter_info_data = DB::select("$raw_meter_info", [$meter_id,$site_id]);
		
		$meter_multiplier = $meter_info_data[0]->meter_multiplier;

		$period = CarbonPeriod::create("$start_date $start_time", '1 hour', "$end_date $end_time");
		
		$result = array();
		foreach ($period as $key => $date) {
			
			$hourly 	= $date->format('Y-m-d H:i:s');
			
			$dt_from_w_time = $hourly;
			$date1=date_create($dt_from_w_time);
			date_sub($date1,date_interval_create_from_date_string("5 minutes"));
			$hourly_start = date_format($date1,"Y-m-d H:i:s");
			
			/*NEXT DATE MAX*/
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("55 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
				$raw_query_user_access = "SELECT 
					a.`meter_id` AS meter_id, 
					a.`datetime` AS min_datetime, 
					a.`wh_total` AS min_wh_total,
					b.`datetime` AS max_datetime,
					b.`wh_total` AS max_wh_total
					
					FROM meter_data a
					USE INDEX(meter_data_index)
					
					INNER JOIN meter_data b
					WHERE 
					a.meter_id = ? AND 
					a.location = ? AND 
					a.`datetime` >= ? AND 
					b.meter_id = ? AND 
					b.`datetime` >= ? LIMIT 0,1";	
							   
				$site_data = DB::select("$raw_query_user_access", [$meter_id,$building_code,$hourly_start,$meter_id,$hourly_end]);
				
				$COUNT_RESULTS = count($site_data);
				
				$KWh = (@$site_data[0]->max_wh_total - @$site_data[0]->min_wh_total) * $meter_multiplier;
				
				if( $KWh>=1 ){
				 // if($COUNT_RESULTS != 0){
					
					$result[] = array(
					'hour' => $hourly,
					'building_code' => $building_code,
					'min_datetime' => @$site_data[0]->min_datetime,
					'min_wh_total' => @$site_data[0]->min_wh_total,
					'max_datetime' => @$site_data[0]->max_datetime,
					'max_wh_total' => @$site_data[0]->max_wh_total,
					'multiplier' => $meter_multiplier,
					'kwh_total' => @$KWh
					);
				
				 }
				
		}
		
		return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
		}
		// return response()->json($result);
		
	}

	public function consumption_report_daily(Request $request){

		$request->validate([
          'site_id'      			=> 'required',
		  'meter_id'      			=> 'required',
		  'start_date'      		=> 'required',
		  'start_time'      		=> 'required',
		  'end_date'      			=> 'required',
		  'end_time'      			=> 'required'
        ], 
        [
			'site_id.required' 		=> 'Please select a Building',
			'meter_id.required' 	=> 'Please select a Meter',
			'start_date.required' 	=> 'Please select a Start Date',
			'start_time.required' 	=> 'Please select a Start Time',
			'end_date.required' 	=> 'Please select a End Date',
			'end_time.required' 	=> 'Please select a End Time'
        ]
		);
		
	if ($request->ajax()) {	
	
		$site_id 	= $request->site_id;
		$meter_id 	= $request->meter_id;
		$meter_role = $request->meter_role;
		$start_date = $request->start_date;
		$start_time = $request->start_time;
		$end_date 	= $request->end_date;
		$end_time 	= $request->end_time;

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->site_id,)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description'
					]);
					
		$building_code = $site_data[0]->building_code;
		
		$raw_meter_info = "SELECT meter_multiplier
				FROM meter_details
				WHERE meter_name = ? and site_idx = ?";	
							   
		$meter_info_data = DB::select("$raw_meter_info", [$meter_id,$site_id]);
		
		$meter_multiplier = $meter_info_data[0]->meter_multiplier;

		$period = CarbonPeriod::create("$start_date $start_time", '1 Day', "$end_date $end_time");
		
		$result = array();
		foreach ($period as $key => $date) {
			if ($key) {
				echo "\n";
			}
			
			$daily 	= $date->format('Y-m-d H:i:s');
			$hourly 	= $date->format('Y-m-d H:i:s');
			
			$dt_from_w_time = $hourly;
			$date1=date_create($dt_from_w_time);
			date_sub($date1,date_interval_create_from_date_string("5 minutes"));
			$hourly_start = date_format($date1,"Y-m-d H:i:s");
			
			/*NEXT DATE MAX*/
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("1435 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
				$raw_query_user_access = "SELECT 
					a.`meter_id` AS meter_id, 
					a.`datetime` AS min_datetime, 
					a.`wh_total` AS min_wh_total,
					b.`datetime` AS max_datetime,
					b.`wh_total` AS max_wh_total
					FROM meter_data a
					USE INDEX(meter_data_index)
					INNER JOIN meter_data b
					WHERE 
					a.meter_id = ? AND 
					a.location = ? AND 
					a.`datetime` >= ? AND 
					b.meter_id = ? AND 
					b.`datetime` >= ? LIMIT 0,1";	
							   
				$site_data = DB::select("$raw_query_user_access", [$meter_id,$building_code,$hourly_start,$meter_id,$hourly_end]);

				$COUNT_RESULTS = count($site_data);
				
				$KWh = (@$site_data[0]->max_wh_total - @$site_data[0]->min_wh_total) * $meter_multiplier;
				
				if( $KWh>=1 ){
					
					$result[] = array(
					'hour' => $hourly,
					'building_code' => $building_code,
					'min_datetime' => @$site_data[0]->min_datetime,
					'min_wh_total' => @$site_data[0]->min_wh_total,
					'max_datetime' => @$site_data[0]->max_datetime,
					'max_wh_total' => @$site_data[0]->max_wh_total,
					'multiplier' => $meter_multiplier,
					'kwh_total' => @$KWh
					);
				
				}
				
		}
		
		return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
		}
		// return response()->json($result);
		
	}


	public function download_consumption_report(Request $request){

		$request->validate([
			'site_id'      			=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Site'
        ]
		);
		
		$interval_type 	= $request->interval_type;
		$site_id 	= $request->site_id;
		$meter_id 	= $request->meter_id;
		
		$start_date = $request->start_date;
		$start_time = $request->start_time;
		$end_date 	= $request->end_date;
		$end_time 	= $request->end_time;
		
		$beginning_date	 = "$start_date $start_time";
		$ending_date	 = "$end_date $end_time";
		
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
		
		$raw_meter_info = "SELECT
						`meter_details`.`meter_id`,
						`meter_details`.`meter_name`,
						`meter_details`.`customer_name`,
						`meter_details`.`meter_role`,
						`meter_details`.`meter_status`,
						`meter_details`.`last_log_update`,
						`meter_details`.`meter_status`,
						`meter_details`.`meter_remarks`,
						`meter_details`.`meter_default_name`,
						`meter_details`.`meter_multiplier`,
						`meter_details`.`meter_type`,
						`meter_details`.`meter_brand`,
						`meter_configuration_file`.`config_file`,
						`meter_location_table`.`location_code`,
						`meter_location_table`.`location_description`,
						`meter_rtu`.`gateway_sn`
					from meter_details
						left join `meter_location_table` on `meter_location_table`.`location_id` = `meter_details`.`location_idx`
						left join `meter_rtu` on `meter_rtu`.`rtu_id` = `meter_details`.`rtu_idx`
						left join `meter_configuration_file` on `meter_configuration_file`.`config_id` = `meter_details`.`config_idx`
						where  `meter_details`.`meter_name` = ? and `meter_details`.`site_idx` = ?";	
							   
		$meter_info_data = DB::select("$raw_meter_info", [$meter_id,$site_id]);

	    ini_set('max_execution_time', 0);
       // ini_set('memory_limit', '500M');
       try {
		   ob_start();
           $spreadSheet = new Spreadsheet();
           
				
				$spreadSheet = IOFactory::load(public_path('/template/Meter Consumption.xlsx'));
		
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

				
				$spreadSheet->getActiveSheet()
					->setCellValue('B2', $meter_info_data[0]->customer_name)
					->setCellValue('B3', $meter_id)
					->setCellValue('B4', $building_description)
					->setCellValue('B5', $building_code)
					->setCellValue('B6', $beginning_date)
					->setCellValue('B7', $ending_date)
					->setCellValue('B8', $meter_info_data[0]->gateway_sn);		
			
			
			$no_excl = 12;
			$n = 1;
		
		if($interval_type=='hourly'){
			
			$period = CarbonPeriod::create("$start_date $start_time", '1 hour', "$end_date $end_time");
			
		}else{
			
			$period = CarbonPeriod::create("$start_date $start_time", '1 Day', "$end_date $end_time");
			
		}
		
		foreach ($period as $key => $date) {
			
			$hourly 	= $date->format('Y-m-d H:i:s');
			
			$dt_from_w_time = $hourly;
			$date1=date_create($dt_from_w_time);
			date_sub($date1,date_interval_create_from_date_string("5 minutes"));
			$hourly_start = date_format($date1,"Y-m-d H:i:s");
			
			if($interval_type=='hourly'){
				
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("55 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
			}
			else{
			
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("1435 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
			}
				$raw_query_user_access = "SELECT 
					a.`meter_id` AS meter_id, 
					a.`datetime` AS min_datetime, 
					a.`wh_total` AS min_wh_total,
					b.`datetime` AS max_datetime,
					b.`wh_total` AS max_wh_total
					FROM meter_data a
					USE INDEX(meter_data_index)
					INNER JOIN meter_data b
					WHERE 
					a.meter_id = ? AND 
					a.location = ? AND 
					a.`datetime` >= ? AND 
					b.meter_id = ? AND 
					b.`datetime` >= ? LIMIT 0,1";	
							   
					
					
				$site_data = DB::select("$raw_query_user_access", [$meter_id,$building_code,$hourly_start,$meter_id,$hourly_end]);
				
				$KWh = (@$site_data[0]->max_wh_total - @$site_data[0]->min_wh_total) * $meter_info_data[0]->meter_multiplier;
				
				$COUNT_RESULTS = count($site_data);
				
				 if( $KWh>=1 ){
	
					$spreadSheet->getActiveSheet()
						->setCellValue('A'.$no_excl, $n)
						->setCellValue('B'.$no_excl, $hourly)
						->setCellValue('C'.$no_excl, @$site_data[0]->min_datetime)
						->setCellValue('D'.$no_excl, @$site_data[0]->min_wh_total * $meter_info_data[0]->meter_multiplier)
						->setCellValue('E'.$no_excl, @$site_data[0]->max_datetime)
						->setCellValue('F'.$no_excl, @$site_data[0]->max_wh_total * $meter_info_data[0]->meter_multiplier)
						->setCellValue('G'.$no_excl, $meter_info_data[0]->meter_multiplier)
						->setCellValue('H'.$no_excl, $KWh);
					
					/*Increment*/
					$no_excl++;
					$n++;
				
				 }
				
		}
			
		  $Excel_writer = new Xlsx($spreadSheet);
		  
			$_server_time	=	date('Y_m_d_H_i_s');
			
			$report_name = "$building_description"."_$building_code"."_$meter_id"."_KWh Consumption"."_$_server_time";
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