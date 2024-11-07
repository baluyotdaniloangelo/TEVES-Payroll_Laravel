<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\GatewayModel;
use Session;
use Validator;
use DataTables;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CAMRSampleExcel extends Controller
{
	
	public function sample1(Request $request){

	
	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setCellValue('A1', 'Hello World !');

	$writer = new Xlsx($spreadsheet);
	$writer->save('hello world.xlsx');
	}	
	
}