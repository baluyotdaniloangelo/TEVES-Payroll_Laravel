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
		
		$title = 'Site Report';
		$data = array();
		if(Session::has('loginID')){			
			$data = User::where('user_id', '=', Session::get('loginID'))->first();
			$site_data = SiteModel::all();			
		}
		return view("amr.site_report", compact('data','title','site_data'));
	
	}  	
	
	public function generate_site_report(Request $request){

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
		$_end_date 	= $request->end_date;

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::find($request->site_id, ['site_code']);		
		$site_code = $site_data->site_code;
				
		/*CURRENT DATE*/
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

		/*Query Select using Raw*/
		$data = MeterModel::selectRaw("meter_name,`customer_name`,`building_code`,`meter_site_name`,`meter_type`,`meter_multiplier`")
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading_datetime`")	
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$prev_month_date' order by `datetime` desc limit 0, 1 ) as `prev_reading`")	
		->selectRaw("(select `wh_total` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading`")	
		->selectRaw("(select `datetime` from `meter_data` USE INDEX (meter_data_index) where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading_datetime`")		
		->where('meter_site_id', $site_id)
		->where("meter_status", 'Active')
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

	public function generate_site_report_excel(Request $request){

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
		$_end_date 	= $request->end_date;

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::find($request->site_id, ['site_code','company_no','business_entity']);		
		$site_code = $site_data->site_code;
		$company_no = $site_data->company_no;
		$business_entity = $site_data->business_entity;
		
		/*CURRENT DATE*/
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
       ini_set('memory_limit', '500M');
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
			
		/*Query Select using Raw*/
		$data = MeterModel::selectRaw("meter_name,`customer_name`,`building_code`,`meter_site_name`,`meter_type`,`meter_multiplier`")
		->selectRaw("(select `wh_total` from `meter_data` where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading`")	
		->selectRaw("(select `datetime` from `meter_data` where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$start_date' order by `datetime` desc limit 0, 1 ) as `current_reading_datetime`")	
		->selectRaw("(select `wh_total` from `meter_data` where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$prev_month_date' order by `datetime` desc limit 0, 1 ) as `prev_reading`")	
		->selectRaw("(select `wh_total` from `meter_data` where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading`")	
		->selectRaw("(select `datetime` from `meter_data` where `location` = '$site_code' and `meter_id` = meter_name and `datetime` <= '$past_two_months_date' order by `datetime` desc limit 0, 1 ) as `past_two_months_reading_datetime`")		
		->where('meter_site_id', $site_id)
		->where("meter_status", 'Active')
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
			
			foreach ($data as $site_data_column){
				
				/*Date and Time*/
				$datetime = $site_data_column['current_reading_datetime'];
				$date2=date_create($datetime);
				$date_generated = date_format($date2,"m/d/Y");
				$time_generated = date_format($date2,"H:i:s");
			
				/*Present Consumption*/
				$current_consumption = number_format(($site_data_column['current_reading'] - $site_data_column['prev_reading']) , 3, '.', '');	
				/*Previous Consumption*/
				$previous_consumption = number_format(($site_data_column['prev_reading'] - $site_data_column['past_two_months_reading']) , 3, '.', '');
			
				/*Difference*/
				
					if($current_consumption==0 || $previous_consumption==0){
						$_difference_consumption = 0;
						$difference_consumption = number_format((0)+0, 2, '.', '');
					}else{
						$_difference_consumption =  ($current_consumption-$previous_consumption) / $previous_consumption ;
						$difference_consumption =   number_format( ($_difference_consumption*100) , 2, '.', '');
					}
				
				$spreadSheet->getActiveSheet()
					->setCellValue('A'.$no_excl, $site_data_column['meter_name'])
					->setCellValue('B'.$no_excl, $site_data_column['current_reading'])
					->setCellValue('C'.$no_excl, $date_generated)
					->setCellValue('D'.$no_excl, $time_generated)
					->setCellValue('E'.$no_excl, '')
					->setCellValue('F'.$no_excl, $site_data_column['building_code'])
					->setCellValue('G'.$no_excl, $site_data_column['customer_name'])
					->setCellValue('H'.$no_excl, $site_data_column['meter_type'])
					->setCellValue('I'.$no_excl, $site_data_column['current_reading'])
					->setCellValue('J'.$no_excl, $site_data_column['prev_reading'])
					->setCellValue('K'.$no_excl, $site_data_column['meter_multiplier'])
					->setCellValue('L'.$no_excl, $current_consumption)
					->setCellValue('M'.$no_excl, $previous_consumption)
					->setCellValue('N'.$no_excl, $difference_consumption);
					
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