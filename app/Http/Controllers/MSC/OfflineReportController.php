<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteModel;
use Session;
use Validator;
use DataTables;
use Illuminate\Support\Facades\DB;
/*Excel*/
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OfflineReportController extends Controller
{
	
	public function download_offline_gateway(Request $request){

		$siteID 	= $request->siteID;

		/*Query Site Code needed for Meter Data*/
		
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
					->where('meter_site.site_id', $siteID)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_division_table.division_name',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
					
		$building_code = $site_data[0]->building_code;
		
	   ini_set('max_execution_time', 0);
       ini_set('memory_limit', '500M');
       try {
		   ob_start();
           $spreadSheet = new Spreadsheet();
          
				$spreadSheet = IOFactory::load(public_path('/template/Offline Gateway.xlsx'));
			 
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

			$raw_query_offline = "SELECT
				 `meter_rtu`.`rtu_id`,
				  `meter_rtu`.`gateway_sn`,
				   `meter_rtu`.`gateway_mac`,
					 `meter_rtu`.`gateway_ip`,
					  `meter_rtu`.`idf_number`,
					   `meter_rtu`.`switch_name`,
					   `meter_rtu`.`idf_port`,
						 `meter_rtu`.`last_log_update`,
						  `meter_rtu`.`soft_rev`,
							  `meter_rtu`.`location_idx`,
							   `meter_location_table`.`location_code`, 
							   `meter_location_table`.`location_description` 
					from `meter_rtu`
					 inner join `meter_location_table` on `meter_location_table`.`location_id` = `meter_rtu`.`location_idx`
					  where `meter_rtu`.`site_idx` = ?
					   and DATEDIFF(NOW(), meter_rtu.last_log_update) >= 1 OR (meter_rtu.last_log_update = '0000-00-00 00:00:00' AND meter_rtu.site_idx = ?)";			   
					   
			$offline_gateway_data = DB::select("$raw_query_offline", [$siteID,$siteID]);
			
			foreach ($offline_gateway_data as $data_column){

				$spreadSheet->getActiveSheet()
					->setCellValue('A'.$no_excl, $n)
					->setCellValue('B'.$no_excl, $data_column->gateway_sn)
					->setCellValue('C'.$no_excl, $data_column->gateway_ip)
					->setCellValue('D'.$no_excl, $data_column->gateway_mac)
					
					->setCellValue('E'.$no_excl, $data_column->idf_number)
					->setCellValue('F'.$no_excl, $data_column->switch_name)
					->setCellValue('G'.$no_excl, $data_column->idf_port)
					
					->setCellValue('H'.$no_excl, $data_column->location_code)
					->setCellValue('I'.$no_excl, $data_column->location_description)
					->setCellValue('J'.$no_excl, $data_column->last_log_update);
					
			/*Increment*/
			$no_excl++;
			$n++;
			} 
			
		  $Excel_writer = new Xlsx($spreadSheet);
		  
			$_server_time	=	date('Y_m_d_H_i_s');
			
			$report_name = "Offline_Gateway_$building_code"."_$_server_time";
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

	public function download_offline_meter(Request $request){

		$siteID 	= $request->siteID;

		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->join('meter_division_table', 'meter_division_table.division_id', '=', 'meter_site.division_idx')
					->join('meter_company_table', 'meter_company_table.company_id', '=', 'meter_site.company_idx')
					->where('meter_site.site_id', $siteID)
              		->get([
					'meter_building_table.building_id',
					'meter_building_table.building_code',
					'meter_building_table.building_description',
					'meter_company_table.company_name',
					'meter_division_table.division_code',
					'meter_division_table.division_name',
					'meter_building_table.device_ip_range',
					'meter_building_table.ip_network',
					'meter_building_table.ip_netmask',
					'meter_building_table.ip_gateway',
					'meter_building_table.cut_off']);
					
		$building_code = $site_data[0]->building_code;		
		
		
	   ini_set('max_execution_time', 0);
       ini_set('memory_limit', '500M');
       try {
		   ob_start();
           $spreadSheet = new Spreadsheet();
          
				$spreadSheet = IOFactory::load(public_path('/template/Offline Meter.xlsx'));
			 
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
					$raw_query_offline = "SELECT
						`meter_details`.`meter_id`,
						`meter_details`.`meter_name`,
						`meter_details`.`customer_name`,
						`meter_details`.`meter_role`,
						`meter_details`.`meter_status`,
						`meter_details`.`last_log_update`,
						`meter_details`.`meter_status`,
						`meter_details`.`meter_remarks`,
						`meter_details`.`meter_default_name`,
						`meter_configuration_file`.`config_file`,
						`meter_location_table`.`location_code`,
						`meter_location_table`.`location_description`,
						`meter_rtu`.`gateway_sn`
					from meter_details
						left join `meter_location_table` on `meter_location_table`.`location_id` = `meter_details`.`location_idx`
						left join `meter_rtu` on `meter_rtu`.`rtu_id` = `meter_details`.`rtu_idx`
						left join `meter_configuration_file` on `meter_configuration_file`.`config_id` = `meter_details`.`config_idx`
						where `meter_details`.`site_idx` = ?
						and DATEDIFF(NOW(), meter_details.last_log_update) >= 1 OR (meter_details.last_log_update = '0000-00-00 00:00:00' AND meter_details.site_idx = ?)";	
						
			$offline_meter_data = DB::select("$raw_query_offline", [$siteID,$siteID]);
			
			foreach ($offline_meter_data as $data_column){
	
				$spreadSheet->getActiveSheet()
					->setCellValue('A'.$no_excl, $n)
					->setCellValue('B'.$no_excl, $data_column->meter_name)
					->setCellValue('C'.$no_excl, $data_column->customer_name)
					->setCellValue('D'.$no_excl, $data_column->gateway_sn)
					->setCellValue('E'.$no_excl, $data_column->meter_role)
					->setCellValue('F'.$no_excl, $data_column->meter_status)
					->setCellValue('G'.$no_excl, $data_column->meter_remarks)
					->setCellValue('H'.$no_excl, $data_column->meter_default_name)
					->setCellValue('I'.$no_excl, $data_column->config_file)
					->setCellValue('J'.$no_excl, $data_column->location_code)
					->setCellValue('K'.$no_excl, $data_column->location_description)
					->setCellValue('L'.$no_excl, $data_column->last_log_update);
					
			/*Increment*/
			$no_excl++;
			$n++;
			} 
			
		  $Excel_writer = new Xlsx($spreadSheet);
			
			$_server_time	=	date('Y_m_d_H_i_s');
		  
			$report_name = "Offline_Meter_$building_code"."_$_server_time";
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