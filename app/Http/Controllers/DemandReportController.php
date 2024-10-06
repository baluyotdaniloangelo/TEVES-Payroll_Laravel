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

class DemandReportController extends Controller
{
	
	/*Load RAW Report Interface*/
	public function demand_report(){
		
		if(Session::has('loginID')){	

			$title = 'Demand Report';
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
		
			return view("amr.meter_demand_report", compact('data','title','site_data'));
	
		}	
	
	}  	
	
	public function demand_report_hourly(Request $request){

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
			//$dt_to = $hourly;
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("55 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
				$raw_query_user_access = "SELECT 
					a.`meter_id` AS meter_id, 
					IFNULL(a.`datetime`, '0000-00-00 00:00:00') AS min_datetime, 
					IFNULL(a.`wh_total`, 0) AS min_wh_total,
					IFNULL(b.`datetime`, '0000-00-00 00:00:00') AS max_datetime,
					IFNULL(b.`wh_total`, 0) AS max_wh_total
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
				
				if(@$site_data[0]->min_wh_total == 0){

					@$min_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');
					@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

					@$min_datetime = $site_data[0]->max_datetime;
					@$max_datetime = $site_data[0]->max_datetime;
			 
				}else{

					@$min_wh_total = number_format($site_data[0]->min_wh_total+0, 2, '.', '');
					@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

					@$min_datetime = $site_data[0]->min_datetime;
					@$max_datetime = $site_data[0]->max_datetime;
			 
				}
				
				$to_time = strtotime("$min_datetime");
				$from_time = strtotime("$max_datetime");
				$total_intervaltime = round( (abs($to_time - $from_time)) / 60 ,0);
				
				if($max_wh_total == '0' || $max_wh_total==NULL || ($max_wh_total-$min_wh_total)==0){
				@$_wh_total=0.00;
				$kw_demand = 0;	
				}else{
				@$_wh_total = number_format(($max_wh_total-$min_wh_total)+0, 2, '.', '');
				$kw_demand = number_format((60/$total_intervaltime)*($_wh_total)*$meter_multiplier +0, 2, '.', ''); 	
				} 
				
				$COUNT_RESULTS = count($site_data);
				
				 if($kw_demand!=0){
					
					$result[] = array(
					'hour' => $hourly,
					'building_code' => $building_code,
					'min_datetime' => @$min_datetime,
					'min_wh_total' => @$min_wh_total,
					'max_datetime' => @$max_datetime,
					'max_wh_total' => @$max_wh_total,
					'kw_demand' => @$kw_demand,
					'multiplier' => $meter_multiplier
					);
					
				 }
				
		}
		
		return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
		}
		
		// return response()->json($result);
		
	}
	public function demand_report_15_old(Request $request){

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

		$period = CarbonPeriod::create("$start_date $start_time", '15 minute', "$end_date $end_time");
		
		$result = array();
		foreach ($period as $key => $date) {
			if ($key) {
				echo "\n";
			}
			
			$hourly 	= $date->format('Y-m-d H:i:s');
			
			$dt_from_w_time = $hourly;
			$date1=date_create($dt_from_w_time);
			date_sub($date1,date_interval_create_from_date_string("5 minutes"));
			$hourly_start = date_format($date1,"Y-m-d H:i:s");
			
			/*NEXT DATE MAX*/
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("14 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
			/**/
				$raw_query_user_access = "SELECT 
				(	
				SELECT 	
				(`wh_total`)
				from `meter_data`
				USE INDEX(meter_data_index)
				 where 
				`location`= ? and
				`datetime`<= ? and
				 `meter_id`= ? ORDER BY `datetime` DESC LIMIT 0,1
				) as `min_wh_total`,
				
				(	
				SELECT 	
				(`datetime`)
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`=? and
				`datetime`<=? and
				 `meter_id`=? ORDER BY `datetime` DESC LIMIT 0,1
				) as `min_datetime`,
				
				( SELECT  
				`wh_total`
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`=? and
				`datetime`>=? and
				`meter_id`=? LIMIT 0,1
				) AS `max_wh_total`,
				
				( SELECT  
				`datetime`
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`= ? and
				`datetime`>= ? and
				`meter_id`= ? LIMIT 0,1
				) AS `max_datetime`";	
				
				$site_data = DB::select("$raw_query_user_access", [$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id]);
				
				
				if(@$site_data[0]->min_wh_total == 0){

				@$min_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');
				@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

				@$min_datetime = $site_data[0]->max_datetime;
				@$max_datetime = $site_data[0]->max_datetime;
			 
				}else{

				@$min_wh_total = number_format($site_data[0]->min_wh_total+0, 2, '.', '');
				@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

				@$min_datetime = $site_data[0]->min_datetime;
				@$max_datetime = $site_data[0]->max_datetime;
			 
				}
				
				$to_time = strtotime("$min_datetime");
				$from_time = strtotime("$max_datetime");
				$total_intervaltime = round( (abs($to_time - $from_time)) / 60 ,0);
				
				//$total_intervaltime = round($_total_intervaltime/15,2);
				
				if($max_wh_total == '0' || $max_wh_total==NULL || ($max_wh_total-$min_wh_total)==0){
				@$_wh_total=0.00;
				$kw_demand = 0;	
				}else{
				@$_wh_total = number_format(($max_wh_total-$min_wh_total)+0, 2, '.', '');
				$kw_demand = number_format((60/$total_intervaltime)*($_wh_total)*$meter_multiplier +0, 2, '.', ''); 	
				}
				
				$COUNT_RESULTS = count($site_data);
				
				 if($max_wh_total!=0){
					 
					$result[] = array(
					'hour' => $hourly,
					'building_code' => $building_code,
					'min_datetime' => @$min_datetime,
					'min_wh_total' => @$min_wh_total,
					'max_datetime' => @$max_datetime,
					'max_wh_total' => @$max_wh_total,
					'kw_demand' => @$kw_demand,
					'multiplier' => $meter_multiplier
					);
					
				 }
				
		}
		
		return response()->json($result);
		
	}
	
	public function demand_report_15(Request $request){

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

		$period = CarbonPeriod::create("$start_date $start_time", '15 minute', "$end_date $end_time");
		
		$result = array();
		foreach ($period as $key => $date) {
			if ($key) {
				echo "\n";
			}
			
			$hourly 	= $date->format('Y-m-d H:i:s');
			
			$dt_from_w_time = $hourly;
			$date1=date_create($dt_from_w_time);
			date_sub($date1,date_interval_create_from_date_string("5 minutes"));
			$hourly_start = date_format($date1,"Y-m-d H:i:s");
			
			/*NEXT DATE MAX*/
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("14 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
			/**/
				$raw_query_user_access = "SELECT 
				(	
				SELECT 	
				(`wh_total`)
				from `meter_data`
				USE INDEX(meter_data_index)
				 where 
				`location`= ? and
				`datetime`<= ? and
				 `meter_id`= ? ORDER BY `datetime` DESC LIMIT 0,1
				) as `min_wh_total`,
				
				(	
				SELECT 	
				(`datetime`)
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`=? and
				`datetime`<=? and
				 `meter_id`=? ORDER BY `datetime` DESC LIMIT 0,1
				) as `min_datetime`,
				
				( SELECT  
				`wh_total`
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`=? and
				`datetime`>=? and
				`meter_id`=? LIMIT 0,1
				) AS `max_wh_total`,
				
				( SELECT  
				`datetime`
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`= ? and
				`datetime`>= ? and
				`meter_id`= ? LIMIT 0,1
				) AS `max_datetime`";	
				
				$site_data = DB::select("$raw_query_user_access", [$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id]);
				
				
				if(@$site_data[0]->min_wh_total == 0){

				@$min_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');
				@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

				@$min_datetime = $site_data[0]->max_datetime;
				@$max_datetime = $site_data[0]->max_datetime;
			 
				}else{

				@$min_wh_total = number_format($site_data[0]->min_wh_total+0, 2, '.', '');
				@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

				@$min_datetime = $site_data[0]->min_datetime;
				@$max_datetime = $site_data[0]->max_datetime;
			 
				}
				
				$to_time = strtotime("$min_datetime");
				$from_time = strtotime("$max_datetime");
				$total_intervaltime = round( (abs($to_time - $from_time)) / 60 ,0);
				
				//$total_intervaltime = round($_total_intervaltime/15,2);
				
				if($max_wh_total == '0' || $max_wh_total==NULL || ($max_wh_total-$min_wh_total)==0){
				@$_wh_total=0.00;
				$kw_demand = 0;	
				}else{
				@$_wh_total = number_format(($max_wh_total-$min_wh_total)+0, 2, '.', '');
				$kw_demand = number_format((60/$total_intervaltime)*($_wh_total)*$meter_multiplier +0, 2, '.', ''); 	
				}
				
				$COUNT_RESULTS = count($site_data);
				
				 if($kw_demand!=0){
					 
					$result[] = array(
					'hour' => $hourly,
					'building_code' => $building_code,
					'min_datetime' => @$min_datetime,
					'min_wh_total' => @$min_wh_total,
					'max_datetime' => @$max_datetime,
					'max_wh_total' => @$max_wh_total,
					'kw_demand' => @$kw_demand,
					'multiplier' => $meter_multiplier
					);
					
				 }
				
		}
				return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
		}
		// return response()->json($result);
		
	}


	public function download_demand_report(Request $request){

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
					->where('meter_site.site_id', $site_id)
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
		
		$meter_multiplier = $meter_info_data[0]->meter_multiplier;
		
	   ini_set('max_execution_time', 0);
       // ini_set('memory_limit', '500M');
       try {
		   ob_start();
           $spreadSheet = new Spreadsheet();
           	
				$spreadSheet = IOFactory::load(public_path('/template/Meter Demand.xlsx'));
		
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
					->setCellValue('B4', $building_code)
					->setCellValue('B5', $building_description)
					->setCellValue('B6', $beginning_date)
					->setCellValue('B7', $ending_date)
					->setCellValue('B8', $meter_info_data[0]->gateway_sn);		
						
			$no_excl = 11;
			$n = 1;
		
		if($interval_type=='hourly'){
			
			$period = CarbonPeriod::create("$start_date $start_time", '1 hour', "$end_date $end_time");
			
			/*$raw_query_user_access = "SELECT 
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
					b.`datetime` >= ? LIMIT 0,1";*/
			
				$raw_query_user_access = "SELECT 
					a.`meter_id` AS meter_id, 
					IFNULL(a.`datetime`, '0000-00-00 00:00:00') AS min_datetime, 
					IFNULL(a.`wh_total`, 0) AS min_wh_total,
					IFNULL(b.`datetime`, '0000-00-00 00:00:00') AS max_datetime,
					IFNULL(b.`wh_total`, 0) AS max_wh_total
					FROM meter_data a
					USE INDEX(meter_data_index)
					INNER JOIN meter_data b
					WHERE 
					a.meter_id = ? AND 
					a.location = ? AND 
					a.`datetime` >= ? AND 
					b.meter_id = ? AND 
					b.`datetime` >= ? LIMIT 0,1";	
			
		}else{
			
			$period = CarbonPeriod::create("$start_date $start_time", '15 minute', "$end_date $end_time");
			
			$raw_query_user_access = "SELECT 
				(	
				SELECT 	
				(`wh_total`) as min_wh_total
				from `meter_data`
				USE INDEX(meter_data_index)
				 where 
				`location`= ? and
				`datetime`<= ? and
				 `meter_id`= ? ORDER BY `datetime` DESC LIMIT 0,1
				) as `min_wh_total`,
				(	
				SELECT 	
				(`datetime`)
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`=? and
				`datetime`<=? and
				 `meter_id`=? ORDER BY `datetime` DESC LIMIT 0,1
				) as `min_datetime`,
				( SELECT  
				`wh_total` as max_wh_total
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`=? and
				`datetime`>=? and
				`meter_id`=? LIMIT 0,1
				) AS `max_wh_total`,
				( SELECT  
				`datetime`
				from `meter_data` 
				USE INDEX(meter_data_index)
				where 
				`location`= ? and
				`datetime`>= ? and
				`meter_id`= ? LIMIT 0,1
				) AS `max_datetime`";	
				
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
			
			$site_data = DB::select("$raw_query_user_access", [$meter_id,$building_code,$hourly_start,$meter_id,$hourly_end]);
			
			}
			else{
			
			$dt_to_w_time = $hourly;
			$date2=date_create($dt_to_w_time);
			date_add($date2,date_interval_create_from_date_string("14 minutes"));
			$hourly_end = date_format($date2,"Y-m-d H:i:s");
			
			$site_data = DB::select("$raw_query_user_access", [$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id,$building_code,$hourly_end,$meter_id]);
			
			}
				
				if(@$site_data[0]->min_wh_total == 0){

				@$min_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');
				@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

				@$min_datetime = $site_data[0]->max_datetime;
				@$max_datetime = $site_data[0]->max_datetime;
			 
				}else{

				@$min_wh_total = number_format($site_data[0]->min_wh_total+0, 2, '.', '');
				@$max_wh_total = number_format($site_data[0]->max_wh_total+0, 2, '.', '');

				@$min_datetime = $site_data[0]->min_datetime;
				@$max_datetime = $site_data[0]->max_datetime;
			 
				}
				
				$to_time = strtotime("$min_datetime");
				$from_time = strtotime("$max_datetime");
				$total_intervaltime = round( (abs($to_time - $from_time)) / 60 ,0);
				
				if($max_wh_total == '0' || $max_wh_total==NULL || ($max_wh_total-$min_wh_total)==0){
				@$_wh_total=0.00;
				$kw_demand = 0;	
				}else{
				@$_wh_total = number_format(($max_wh_total-$min_wh_total)+0, 2, '.', '');
				$kw_demand = number_format((60/$total_intervaltime)*($_wh_total)*$meter_multiplier +0, 2, '.', ''); 	
				}

				$COUNT_RESULTS = count($site_data);
				
				 if($kw_demand!=0){
				
					$spreadSheet->getActiveSheet()
						->setCellValue('A'.$no_excl, $n)
						->setCellValue('B'.$no_excl, $hourly)
						->setCellValue('C'.$no_excl, @$site_data[0]->min_datetime)
						->setCellValue('D'.$no_excl, @$site_data[0]->min_wh_total*$meter_multiplier)
						->setCellValue('E'.$no_excl, @$site_data[0]->max_datetime)
						->setCellValue('F'.$no_excl, @$site_data[0]->max_wh_total*$meter_multiplier)
						->setCellValue('G'.$no_excl, $meter_info_data[0]->meter_multiplier)
						->setCellValue('H'.$no_excl, $kw_demand);
					
					/*Increment*/
					$no_excl++;
					$n++;
					
				 }
				
		}
			
		  $Excel_writer = new Xlsx($spreadSheet);
		  
			$_server_time	=	date('Y_m_d_H_i_s');
			
			$report_name = "$building_description"."_$building_code"."_$meter_id"."_KW Demand"."_$_server_time";
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