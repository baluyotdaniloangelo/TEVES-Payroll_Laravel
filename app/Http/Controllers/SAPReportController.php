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

class SAPReportController extends Controller
{
	
	/*Load SAP Report Interface*/
	public function sap_report(){
		
		if(Session::has('loginID')){	

			$title = 'SAP Report';
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
		
			return view("amr.sap_report", compact('data','title','site_data'));
	
		}
	}  	

	public function generate_sap_report(Request $request){

		$request->validate([
          'site_id'      			=> 'required',
		  'start_date'      		=> 'required',
		  'end_date'      			=> 'required'
        ], 
        [
			'site_id.required' 		=> 'Please select a Building',
			'start_date.required' 	=> 'Please select a Start Date',
			'end_date.required' 	=> 'Please select a End Date'
        ]
		);


 	if ($request->ajax()) {	
	
		$site_id 	= $request->site_id;
		$meter_role = $request->meter_role;
		//$building_id = $request->building_id;
		$_start_date = $request->start_date;
		$_end_date 	= $request->start_date;
		
		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->site_id,)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description'
					]);
					
		$building_code = $site_data[0]->building_code;		
		
		/*CURRENT DATE*/
		$dt_from_10_00AM = ("$_end_date 00:00:00");
		$dt_from_10_14AM = ("$_end_date 00:14:00");
		
		$dt_from = ("$_end_date 10:00:00");
		
		$dt_from_w_time = "$dt_from";
		$date1=date_create($dt_from_w_time);
		date_add($date1,date_interval_create_from_date_string("14 minutes"));
		$start_date = date_format($date1,"Y-m-d H:i:59");
		
		/*Prev Month Base of date*/
		$prev_month = date('Y-m-d', strtotime($_end_date. ' - 1 months'));
		$prev_month_date = "$prev_month 10:14:59";

		/*Prev Month Base of date*/
		$past_two_months = date('Y-m-d', strtotime($_end_date. ' - 2 months'));
		$past_two_months_date = "$past_two_months 10:14:59";

	   	$data = MeterModel::selectRaw("meter_details.meter_name,meter_details.customer_name, meter_building_table.building_code,meter_details.meter_type,meter_details.meter_multiplier")
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading_datetime`")	
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$prev_month_date' order by `datetime` desc limit 0, 1 ) as `prev_reading`")	
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading_datetime`")		
		->where('meter_details.site_idx', $site_id)
		->where("meter_details.meter_status", 'Active')
		->join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_details.site_idx')
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
				@$meter_multiplier = $data_cols->meter_multiplier;
				@$meter_type = $data_cols->meter_type;
				@$building_code = $data_cols->building_code;
				@$current_reading = $data_cols->current_reading*$data_cols['meter_multiplier'];
				@$current_reading_datetime = $data_cols->current_reading_datetime;
				@$prev_reading = $data_cols->prev_reading*$data_cols['meter_multiplier'];
				@$past_two_months_reading = $data_cols->past_two_months_reading;
				
					$datetime = $data_cols['current_reading_datetime'];
					$date2=date_create($datetime);
					$date_generated = date_format($date2,"m/d/Y");
					$time_generated = date_format($date2,"H:i:s");
				
					/*Present Consumption*/
					$current_consumption = number_format(($data_cols['current_reading']*$data_cols['meter_multiplier'] - $data_cols['prev_reading']*$data_cols['meter_multiplier']) , 3, '.', '');	
					/*Previous Consumption*/
					$previous_consumption = number_format(($data_cols['prev_reading']*$data_cols['meter_multiplier'] - $data_cols['past_two_months_reading']*$data_cols['meter_multiplier']) , 3, '.', '');
				
						/*Difference*/
					
						if($current_consumption==0 || $previous_consumption==0){
							$_difference_consumption = 0;
							$difference_consumption = number_format((0)+0, 2, '.', '');
						}else{
							$_difference_consumption =  ($current_consumption-$previous_consumption) / $previous_consumption ;
							$difference_consumption =   number_format( ($_difference_consumption*100) , 2, '.', '');
						}
				
							$difference_consumption_percentage = "$difference_consumption %";
							
				/*Dont Include Meters with no Current Reading July 2, 2024*/
				if( $current_reading!=$prev_reading ){
				
					$result[] = array(
					'meter_name' => $meter_name,
					'date_generated' => $date_generated,
					'time_generated' => $time_generated,
					'customer_name' => $customer_name,
					'initial' => '',
					'meter_multiplier' => $meter_multiplier,
					'meter_type' => $meter_type,
					'building_code' => @$building_code,
					'current_reading' => @$current_reading,
					'current_reading_datetime' => @$current_reading_datetime,
					'prev_reading' => @$prev_reading,
					'past_two_months_reading' => @$past_two_months_reading,
					'current_consumption' => @$current_consumption,
					'previous_consumption' => @$previous_consumption,
					'difference_consumption' => @$difference_consumption_percentage,
					);
					
				}
				
	   }

		return DataTables::of($result)
				->addIndexColumn()
                ->make(true);	
		}
		
		// return response()->json($result);
		
	}

	public function generate_sap_report_excel(Request $request){

		$request->validate([
          'site_id'      			=> 'required',
		  'start_date'      		=> 'required',
		  'end_date'      			=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Site',
			'start_date.required' 	=> 'Please select a Start Date',
			'end_date.required' 	=> 'Please select a End Date'
        ]
		);

		$site_id 	= $request->site_id;
		$meter_role = $request->meter_role;
		$building_id = $request->building_id;
		$_start_date = $request->start_date;
		$_end_date 	= $request->start_date;

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
		
		/*CURRENT DATE*/
		$dt_from_12AM = ("$_end_date 00:00:00");
		
		$dt_from = ("$_end_date 10:00:00");
		
		$dt_from_w_time = "$dt_from";
		$date1=date_create($dt_from_w_time);
		date_add($date1,date_interval_create_from_date_string("14 minutes"));
		$start_date = date_format($date1,"Y-m-d H:i:59");
		
		/*Prev Month Base of date*/
		$prev_month = date('Y-m-d', strtotime($_end_date. ' - 1 months'));
		$prev_month_date = "$prev_month 10:14:59";

		/*Prev Month Base of date*/
		$past_two_months = date('Y-m-d', strtotime($_end_date. ' - 2 months'));
		$past_two_months_date = "$past_two_months 10:14:59";
			
	   ini_set('max_execution_time', 0);
       // ini_set('memory_limit', '500M');
       try {
		   ob_start();
           $spreadSheet = new Spreadsheet();
           
           $spreadSheet = IOFactory::load(public_path('/template/SAP.xlsx'));
				
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
			
			$no_excl = 2;
			$n = 1;

		$data = MeterModel::selectRaw("meter_details.meter_name,meter_details.customer_name, meter_building_table.building_code,meter_details.meter_type,meter_details.meter_multiplier")
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading_datetime`")	
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$prev_month_date' order by `datetime` desc limit 0, 1 ) as `prev_reading`")	
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$building_code' and `meter_id` = meter_details.meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading_datetime`")		
		->where('meter_details.site_idx', $site_id)
		->where("meter_details.meter_status", 'Active')
		->join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_details.site_idx')
		->where(function ($r) use($meter_role) {
			if ($meter_role) {
			   $r->where('meter_role', $meter_role);
			}
			})
       ->get();
	   
			foreach ($data as $sap_data_column){
				
				@$current_reading = $sap_data_column->current_reading;
				@$prev_reading = $sap_data_column->prev_reading;
				
				/*Dont Include Meters with no Current Reading July 2, 2024*/
				if( $current_reading!=$prev_reading ){
				
					/*Date and Time*/
					$datetime = $sap_data_column['current_reading_datetime'];
					$date2=date_create($datetime);
					$date_generated = date_format($date2,"m/d/Y");
					$time_generated = date_format($date2,"H:i:s");
				
					/*Present Consumption*/
					$current_consumption = number_format(($sap_data_column['current_reading']*$sap_data_column['meter_multiplier'] - $sap_data_column['prev_reading']*$sap_data_column['meter_multiplier']) , 3, '.', '');	
					/*Previous Consumption*/
					$previous_consumption = number_format(($sap_data_column['prev_reading']*$sap_data_column['meter_multiplier'] - $sap_data_column['past_two_months_reading']*$sap_data_column['meter_multiplier']) , 3, '.', '');
				
					/*Difference*/
					
						if($current_consumption==0 || $previous_consumption==0){
							$_difference_consumption = 0;
							$difference_consumption = number_format((0)+0, 2, '.', '');
						}else{
							$_difference_consumption =  ($current_consumption-$previous_consumption) / $previous_consumption ;
							$difference_consumption =   number_format( ($_difference_consumption*100) , 2, '.', '');
						}
					
					$spreadSheet->getActiveSheet()
						->setCellValue('A'.$no_excl, $sap_data_column['meter_name'])
						->setCellValue('B'.$no_excl, number_format(($sap_data_column['current_reading']*$sap_data_column['meter_multiplier']) , 3, '.', ''))
						->setCellValue('C'.$no_excl, $date_generated)
						->setCellValue('D'.$no_excl, $time_generated)
						->setCellValue('E'.$no_excl, '')
						->setCellValue('F'.$no_excl, $sap_data_column['building_code'])
						->setCellValue('G'.$no_excl, $sap_data_column['customer_name'])
						->setCellValue('H'.$no_excl, $sap_data_column['meter_type'])
						->setCellValue('I'.$no_excl, number_format(($sap_data_column['current_reading']*$sap_data_column['meter_multiplier']) , 3, '.', ''))
						->setCellValue('J'.$no_excl, number_format(($sap_data_column['prev_reading']*$sap_data_column['meter_multiplier']) , 3, '.', ''))
						->setCellValue('K'.$no_excl, $sap_data_column['meter_multiplier'])
						->setCellValue('L'.$no_excl, $current_consumption)
						->setCellValue('M'.$no_excl, $previous_consumption)
						->setCellValue('N'.$no_excl, $difference_consumption);
						
						$spreadSheet->getActiveSheet()->getStyle("A$no_excl:O$no_excl")->applyFromArray($style_RIGHT);
				/*Increment*/
				$no_excl++;
				$n++;
			
				}
			
			} 
			
		  $Excel_writer = new Xlsx($spreadSheet);
		  
			$_server_time	=	date('Y_m_d_H_i_s');
			
			$report_name = "$building_description"."_$building_code"."_SAP Report"."_$_server_time";
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

	public function generate_sap_report_sm(Request $request){

		$request->validate([
          'site_id'      			=> 'required',
		  'cut_off'      		=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Site',
			'cut_off.required' 	=> 'Please select a Cut-off'
        ]
		);

		$site_id 	= $request->site_id;
		$meter_role = $request->meter_role;
		$building_id = $request->building_id;
		$_cut_off = $request->cut_off;

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::find($request->site_id, ['site_code']);		
		$site_code = $site_data->site_code;
				
		/*CURRENT DATE*/
		$dt_from = ("$_cut_off 00:00:00");
		$dt_from_w_time = "$dt_from";
		$date1=date_create($dt_from_w_time);
		date_add($date1,date_interval_create_from_date_string("14 minutes"));
		$cut_off = date_format($date1,"Y-m-d H:i:59");

		/*Query Select using Raw*/
		$data = MeterModel::selectRaw("`meter_name`,`customer_name`,`measuring_point`,`meter_site_name`")
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$cut_off' order by `datetime` desc limit 0, 1 ) as `current_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$cut_off' order by `datetime` desc limit 0, 1 ) as `current_reading_datetime`")	
		->where('meter_site_id', $site_id)
		->where("meter_status", "Active")
		->where("measuring_point", '<>', '0')
		->where(function ($q) use($building_id) {
			if ($building_id) {
			   $q->where('building_id', $building_id);
			}
			})
		->where(function ($r) use($meter_role) {
			if ($meter_role) {
			   $r->where('meter_role', $meter_role);
			}
			})
       ->get();

		return response()->json($data);
		
	}

	public function generate_sap_report_excel_sm(Request $request){

		$request->validate([
          'site_id'      			=> 'required',
		  'cut_off'      		=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Site',
			'cut_off.required' 	=> 'Please select a Cut-off'
        ]
		);

		$site_id 	= $request->site_id;
		$meter_role = $request->meter_role;
		$building_id = $request->building_id;
		$_cut_off = $request->cut_off;

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::find($request->site_id, ['site_code']);		
		$site_code = $site_data->site_code;
				
		/*CURRENT DATE*/
		$dt_from = ("$_cut_off 00:00:00");
		$dt_from_w_time = "$dt_from";
		$date1=date_create($dt_from_w_time);
		date_add($date1,date_interval_create_from_date_string("14 minutes"));
		$cut_off = date_format($date1,"Y-m-d H:i:59");
			
	   ini_set('max_execution_time', 0);
       ini_set('memory_limit', '500M');
       try {
		   ob_start();
           $spreadSheet = new Spreadsheet();
           
           $spreadSheet = IOFactory::load(public_path('/template/SAP_SM.xlsx'));
				
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
			
			$no_excl = 2;
			$n = 1;
			
		/*Query Select using Raw*/
		$data = MeterModel::selectRaw("`meter_name`,`customer_name`,`measuring_point`,`meter_site_name`")
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$cut_off' order by `datetime` desc limit 0, 1 ) as `current_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$cut_off' order by `datetime` desc limit 0, 1 ) as `current_reading_datetime`")	
		->where('meter_site_id', $site_id)
		->where("meter_status", 'Active')
		->where("measuring_point", '<>', '0')
		->where(function ($q) use($building_id) {
			if ($building_id) {
			   $q->where('building_id', $building_id);
			}
			})
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
			
				/*Present Consumption*/
				//$current_consumption = number_format(($sap_data_column['current_reading'] - $sap_data_column['prev_reading']) , 3, '.', '');	
				/*Previous Consumption*/
				//$previous_consumption = number_format(($sap_data_column['prev_reading'] - $sap_data_column['past_two_months_reading']) , 3, '.', '');
			
				/*Difference*/
				
					if($current_consumption==0 || $previous_consumption==0){
						$_difference_consumption = 0;
						$difference_consumption = number_format((0)+0, 2, '.', '');
					}else{
						$_difference_consumption =  ($current_consumption-$previous_consumption) / $previous_consumption ;
						$difference_consumption =   number_format( ($_difference_consumption*100) , 2, '.', '');
					}
				
				$spreadSheet->getActiveSheet()
					->setCellValue('A'.$no_excl, $sap_data_column['meter_name'])
					->setCellValue('B'.$no_excl, $sap_data_column['customer_name'])
					->setCellValue('C'.$no_excl, $sap_data_column['current_reading'])
					->setCellValue('D'.$no_excl, $date_generated)
					->setCellValue('E'.$no_excl, $time_generated)
					->setCellValue('F'.$no_excl, $sap_data_column['measuring_point']);
					
					$spreadSheet->getActiveSheet()->getStyle("A$no_excl:O$no_excl")->applyFromArray($style_RIGHT);
			/*Increment*/
			$no_excl++;
			$n++;
			} 
			
		  $Excel_writer = new Xlsx($spreadSheet);
		  
			$report_name = "$business_entity"."_$company_no". "_$date_generated";
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