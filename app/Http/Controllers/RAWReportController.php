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

class RAWReportController extends Controller
{
	
	/*Load RAW Report Interface*/
	public function raw_report(){
		
		if(Session::has('loginID')){	

			$title = 'RAW Report';
			//$data = array();
		
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
		
			return view("amr.raw_report", compact('data','title','site_data'));
	
		}	
	
	}  	
	
	public function generate_raw_report_OLD(Request $request){

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
		
		$cols_set1 	= $request->cols_set1;
		if($cols_set1=='true'){ $column_set1 = "`vrms_a`,`vrms_b`,`vrms_c`,"; }else{ $column_set1 = ""; }
		
		$cols_set2 	= $request->cols_set2;
		if($cols_set2=='true'){ $column_set2 = "`irms_a`,`irms_b`,`irms_c`,"; }else{ $column_set2 = ""; }
		
		$cols_set3 	= $request->cols_set3;
		if($cols_set3=='true'){ $column_set3 = "`freq`,`pf`,`watt`,`va`,`var`,"; }else{ $column_set3 = ""; }
		
		/*Default : $cols_set4 	= $request->cols_set4;*/
		
		$cols_set5 	= $request->cols_set5;
		if($cols_set5=='true'){ $column_set5 = "`varh_neg`,`varh_pos`,`varh_net`,`varh_total`,`vah_total`,"; }else{ $column_set5 = ""; }
		
		$cols_set6 	= $request->cols_set6;
		if($cols_set6=='true'){ $column_set6 = "`max_rec_kw_dmd`,`max_rec_kw_dmd_time`,"; }else{ $column_set6 = ""; }
		
		$cols_set7 	= $request->cols_set7;
		if($cols_set7=='true'){ $column_set7 = "`soft_rev`,`mac_addr`"; }else{ $column_set7 = ""; }
		
		$_custom_column = "`datetime`,`wh_del`,`wh_rec`,`wh_net`,`wh_total`,$column_set1$column_set2$column_set3$column_set5$column_set6$column_set7";
		$custom_column = rtrim($_custom_column, ',');

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->site_id,)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description'
					]);
					
		$building_code = $site_data[0]->building_code;
		
		$data = DB::select("SELECT $custom_column FROM meter_data USE INDEX (meter_data_index) WHERE meter_id = ? and location = ? and datetime >= ? and datetime <= ? ORDER BY datetime ASC", [$meter_id,$building_code, "$start_date $start_time","$end_date $end_time"]);
		
		return response()->json($data);
		
	}
	
	
		public function generate_raw_report(Request $request){

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
		
		$cols_set1 	= $request->cols_set1;
		if($cols_set1=='true'){ $column_set1 = "`vrms_a`,`vrms_b`,`vrms_c`,"; }else{ $column_set1 = ""; }
		
		$cols_set2 	= $request->cols_set2;
		if($cols_set2=='true'){ $column_set2 = "`irms_a`,`irms_b`,`irms_c`,"; }else{ $column_set2 = ""; }
		
		$cols_set3 	= $request->cols_set3;
		if($cols_set3=='true'){ $column_set3 = "`freq`,`pf`,`watt`,`va`,`var`,"; }else{ $column_set3 = ""; }
		
		/*Default : $cols_set4 	= $request->cols_set4;*/
		
		$cols_set5 	= $request->cols_set5;
		if($cols_set5=='true'){ $column_set5 = "`varh_neg`,`varh_pos`,`varh_net`,`varh_total`,`vah_total`,"; }else{ $column_set5 = ""; }
		
		$cols_set6 	= $request->cols_set6;
		if($cols_set6=='true'){ $column_set6 = "`max_rec_kw_dmd`,`max_rec_kw_dmd_time`,"; }else{ $column_set6 = ""; }
		
		$cols_set7 	= $request->cols_set7;
		if($cols_set7=='true'){ $column_set7 = "`soft_rev`,`mac_addr`"; }else{ $column_set7 = ""; }
		
		$_custom_column = "`datetime`,`wh_del`,`wh_rec`,`wh_net`,`wh_total`,$column_set1$column_set2$column_set3$column_set5$column_set6$column_set7";
		$custom_column = rtrim($_custom_column, ',');

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->site_id,)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description'
					]);
					
		$building_code = $site_data[0]->building_code;
		
		$data = DB::select("SELECT * FROM meter_data USE INDEX (meter_data_index) WHERE meter_id = ? and location = ? and datetime >= ? and datetime <= ? ORDER BY datetime ASC", [$meter_id,$building_code, "$start_date $start_time","$end_date $end_time"]);
		
		//return response()->json($data);
		
		return DataTables::of($data)
				->addIndexColumn()
                ->make(true);	
		}		
	}
	

	public function generate_raw_report_excel(Request $request){

		$request->validate([
          'site_id'      			=> 'required',
		  'meter_id'      			=> 'required',
		  'start_date'      		=> 'required',
		  'start_time'      		=> 'required',
		  'end_date'      			=> 'required',
		  'end_time'      			=> 'required'
        ], 
        [
			'site_id.required' 	=> 'Please select a Site',
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
		
		$beginning_date	 = "$start_date $start_time";
		$ending_date	 = "$end_date $end_time";
		
		$cols_set1 	= $request->cols_set1;
		if($cols_set1=='true'){ 
		
			$column_set1 = "`vrms_a`,`vrms_b`,`vrms_c`,"; 
			$column_set1_excel = "vrms_a,vrms_b,vrms_c,"; 
		
		}else{
			
			$column_set1 = "";
			$column_set1_excel = ""; 

			}
		
		$cols_set2 	= $request->cols_set2;
		if($cols_set2=='true'){ 
		
			$column_set2 = "`irms_a`,`irms_b`,`irms_c`,"; 
			$column_set2_excel = "irms_a,irms_b,irms_c,";
		
		}else{ 
		
			$column_set2 = "";
			$column_set2_excel = "";			
		
		}
		
		$cols_set3 	= $request->cols_set3;
		if($cols_set3=='true'){ 
		
			$column_set3 = "`freq`,`pf`,`watt`,`va`,`var`,";
			$column_set3_excel = "freq,pf,kw,kva,kvar,";		
		
		}else{
		
			$column_set3 = ""; 
			$column_set3_excel = "";
			
		}
		
		/*Default : $cols_set4 	= $request->cols_set4;*/
		
		$cols_set5 	= $request->cols_set5;
		if($cols_set5=='true'){ 
			
			$column_set5 = "`varh_neg`,`varh_pos`,`varh_net`,`varh_total`,`vah_total`,";
			$column_set5_excel = "kvarh_neg,kvarh_pos,kvarh_net,kvarh_total,kvah_total,";
			
		}else{ 
		
			$column_set5 = ""; 
			$column_set5_excel = "";
		
		}
		
		$cols_set6 	= $request->cols_set6;
		if($cols_set6=='true'){
			
			$column_set6 = "`max_rec_kw_dmd`,`max_rec_kw_dmd_time`,";
			$column_set6_excel = "max_rec_kw_dmd,max_rec_kw_dmd_time,";
			
		}else{
			
			$column_set6 = "";
			$column_set6_excel = "";
			
		}
		
		$cols_set7 	= $request->cols_set7;
		if($cols_set7=='true'){

			$column_set7 = "`mac_addr`,`soft_rev`"; 
			$column_set7_excel = "MAC Address,Firmware Revision"; 
			
		}
		else{
			
			$column_set7 = "";
			$column_set7_excel = ""; 
			
		}
		
		$_custom_column = "`datetime`,$column_set1$column_set2$column_set3`wh_del`,`wh_rec`,`wh_net`,`wh_total`,$column_set5$column_set6$column_set7";
		$custom_column = rtrim($_custom_column, ',');

		/*Column for Excel*/
		$custom_column_header_excel = "#,Datetime,$column_set1_excel$column_set2_excel$column_set3_excel"."kwh_del,kwh_rec,kwh_net,kwh_total,$column_set5_excel$column_set6_excel$column_set7_excel";
		$_custom_column_data_excel = str_replace('`', '', $custom_column);
		$custom_column_data_excel = explode(",", $_custom_column_data_excel);
		
		/*Query Site Code needed for Meter Data*/
		//$site_data = SiteModel::find($request->site_id, ['site_code']);		
		//$site_code = $site_data->site_code;
		
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
						`meter_details`.`meter_multiplier`,
						`meter_location_table`.`location_code`,
						`meter_location_table`.`location_description`,
						`meter_rtu`.`gateway_sn`
					from meter_details
						left join `meter_location_table` on `meter_location_table`.`location_id` = `meter_details`.`location_idx`
						left join `meter_rtu` on `meter_rtu`.`rtu_id` = `meter_details`.`rtu_idx`
						where  `meter_details`.`meter_name` = ? and `meter_details`.`site_idx` = ?";	
							   
		$meter_info_data = DB::select("$raw_meter_info", [$meter_id,$site_id]);
		
		$meter_multiplier = $meter_info_data[0]->meter_multiplier;
		
	   ini_set('max_execution_time', 0);
       // ini_set('memory_limit', '500M');
       try {
		   ob_start();
           ob_start();
           $spreadSheet = new Spreadsheet();
           
           $spreadSheet = IOFactory::load(public_path('/template/Raw Data.xlsx'));
				
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
		
		
			$no_excl = 12;
			$n = 1;
		
		$letter_column = 'A';
		
		$excel_column_header = explode(",", $custom_column_header_excel);	
		foreach($excel_column_header as $excel_column_header_cols) {
		   
			$excel_header = trim($excel_column_header_cols);
			
			$spreadSheet->getActiveSheet()
				->setCellValue("$letter_column"."11", "$excel_header");
			
		$letter_column++;

		}	
		
		/*Query Select using Raw
			$data = MeterDataModel::selectRaw("$custom_column")
					->where('location', $site_code)
					->where('meter_id', '=', $meter_id)
					->where('datetime', '>=', "$start_date $start_time")
                    ->where('datetime', '<=', "$end_date $end_time")
					->orderBy('datetime', 'asc')
              		->get();	 */
					
			$data = DB::select("SELECT $custom_column FROM meter_data USE INDEX (meter_data_index) WHERE meter_id = ? and location = ? and datetime >= ? and datetime <= ? ORDER BY datetime ASC", [$meter_id,$building_code, "$start_date $start_time","$end_date $end_time"]);
		
			foreach ($data as $raw_data_column){
				
				$letter_column_data = 'B';
				
					$spreadSheet->getActiveSheet()
						->setCellValue("A$no_excl", $n);
						
				foreach($custom_column_data_excel as $custom_column_data_excel_cols) {
					//echo $raw_data_column->$custom_column_data_excel_cols. " - ";
		   
					$excel_data = trim($custom_column_data_excel_cols);
					$spreadSheet->getActiveSheet()
						->setCellValue("$letter_column_data"."$no_excl", $raw_data_column->$custom_column_data_excel_cols);
					
					$letter_column_data++;

				}

			/*Increment*/
			$no_excl++;
			$n++;
			} 
			
		  $Excel_writer = new Xlsx($spreadSheet);
		  
			$_server_time	=	date('Y_m_d_H_i_s');
			
			$report_name = "$building_description"."_$building_code"."_$meter_id"."_Raw Data"."_$_server_time";
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