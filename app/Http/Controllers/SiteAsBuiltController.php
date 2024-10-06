<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SiteModel;
use App\Models\MeterModel;
use App\Models\GatewayModel;
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

class SiteAsBuiltController extends Controller
{
	
	public function generate_site_as_built_excel(Request $request){

		$request->validate([
			'siteID'      			=> 'required'
        ], 
        [
			'siteID.required' 		=> 'Please select a Site'
        ]
		);

		$site_id 	= $request->siteID;
	
		/*Query Site Code needed for Meter Data*/
		$site_data = SiteModel::join('meter_building_table', 'meter_building_table.site_idx', '=', 'meter_site.site_id')
					->where('meter_site.site_id', $request->siteID,)
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

				
				$spreadSheet = IOFactory::load(public_path('/template/Meter List.xlsx'));
				
				$spreadSheet->setActiveSheetIndex(1);
				
				$spreadSheet->getActiveSheet()
					->setCellValue('B2', $building_code)
					->setCellValue('B3', $building_description);
	 
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
			
			$no_excl = 5;
			$n = 1;
		
	   	/*Query Meter List*/
		$data = MeterModel::selectRaw("meter_details.meter_name,
		meter_details.meter_default_name,
		meter_details.meter_type,
		meter_details.meter_brand,
		meter_details.meter_role,
		meter_details.meter_remarks,
		meter_details.meter_status,
		meter_details.meter_multiplier,
		meter_details.customer_name, 
		meter_configuration_file.config_file,
		meter_rtu.gateway_sn,
		meter_location_table.location_code,
		meter_location_table.location_description")
		->where('meter_details.site_idx', $site_id)
		//->where("meter_details.meter_status", 'Active')
		->join('meter_configuration_file', 'meter_configuration_file.config_id', '=', 'meter_details.config_idx')
		->join('meter_rtu', 'meter_rtu.rtu_id', '=', 'meter_details.rtu_idx')
		->join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_details.location_idx')
       ->get();
			
			foreach ($data as $data_meter_column){
				
				$spreadSheet->getActiveSheet()
					->setCellValue('A'.$no_excl, $n)
					->setCellValue('B'.$no_excl, $data_meter_column['customer_name'])
					->setCellValue('C'.$no_excl, $data_meter_column['meter_name'])
					->setCellValue('D'.$no_excl, $data_meter_column['meter_role'])
					->setCellValue('E'.$no_excl, $data_meter_column['meter_status'])
					->setCellValue('F'.$no_excl, $data_meter_column['gateway_sn'])
					->setCellValue('G'.$no_excl, $data_meter_column['config_file'])
					->setCellValue('H'.$no_excl, $data_meter_column['meter_default_name'])
					->setCellValue('I'.$no_excl, $data_meter_column['meter_multiplier'])
					->setCellValue('J'.$no_excl, $data_meter_column['meter_type'])
					->setCellValue('K'.$no_excl, $data_meter_column['meter_brand'])
					->setCellValue('L'.$no_excl, $data_meter_column['meter_remarks'])
					->setCellValue('M'.$no_excl, $data_meter_column['location_code'])
					->setCellValue('N'.$no_excl, $data_meter_column['location_description']);
					
			/*Increment*/
			$no_excl++;
			$n++;
			
			}
		
		$spreadSheet->setActiveSheetIndex(0);	
		$spreadSheet->getActiveSheet()
					->setCellValue('B2', $building_code)
					->setCellValue('B3', $building_description);
		/*Query Gateway List*/	
		$data_gateway = GatewayModel::join('meter_location_table', 'meter_location_table.location_id', '=', 'meter_rtu.location_idx')
						->where('meter_rtu.site_idx', $site_id)
						->get([
						'meter_rtu.gateway_sn',
						'meter_rtu.gateway_mac',
						'meter_rtu.gateway_ip',
						'meter_rtu.idf_number',
						'meter_rtu.switch_name',
						'meter_rtu.idf_port',
						'meter_rtu.connection_type',
						'meter_rtu.gateway_description',
						'meter_rtu.last_log_update',
						'meter_rtu.soft_rev',
						'meter_rtu.location_idx',
						'meter_location_table.location_code',
						'meter_location_table.location_description']);
			
			$no_excl_gw = 5;
			$n_gw = 1;
			foreach ($data_gateway as $data_gateway_column){
				
				$spreadSheet->getActiveSheet()
					->setCellValue('A'.$no_excl_gw, $n_gw)
					->setCellValue('B'.$no_excl_gw, $data_gateway_column['gateway_sn'])
					->setCellValue('C'.$no_excl_gw, $data_gateway_column['gateway_ip'])
					->setCellValue('D'.$no_excl_gw, $data_gateway_column['gateway_mac'])
					->setCellValue('E'.$no_excl_gw, $data_gateway_column['connection_type'])
					->setCellValue('F'.$no_excl_gw, $data_gateway_column['gateway_description'])
					->setCellValue('G'.$no_excl_gw, $data_gateway_column['idf_number'])
					->setCellValue('H'.$no_excl_gw, $data_gateway_column['switch_name'])
					->setCellValue('I'.$no_excl_gw, $data_gateway_column['idf_port'])
					->setCellValue('J'.$no_excl_gw, $data_gateway_column['location_code'])
					->setCellValue('K'.$no_excl_gw, $data_gateway_column['location_description']);
					
			/*Increment*/
			$no_excl_gw++;
			$n_gw++;
			
			}			
			
		   $Excel_writer = new Xlsx($spreadSheet);
		  
			 $_server_time	=	date('Y_m_d_H_i_s');
			
			 $report_name = "$building_description"."_$building_code"."_Building Meters and Gateway List"."_$_server_time";
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