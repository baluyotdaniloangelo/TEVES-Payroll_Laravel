<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Attendance\AttendanceImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttendanceImportController extends Controller
{
    public function legacyImport()
    {
        return response()->json(['message'=>'Use Import Attendance to preview, map employees, and review CSV or device attendance before saving.',
            'url'=>route('attendance-import.index')],422);
    }

    public function index(AttendanceImportService $service)
    {
        $data=User::where('user_id',session('loginID'))->firstOrFail();
        $title='DTR Plotting'; $active_link='attendance_dtr_plotting';
        $ready=Schema::hasTable('attendance_import_rows');
        $branches=$service->branches();
        $devices=$ready?DB::table('attendance_devices')->whereIn('branch_idx',$branches->pluck('branch_id'))->get():collect();
        $batches=$ready?DB::table('attendance_imports')->where('created_by',session('loginID'))->whereIn('device_id',$devices->pluck('id'))->orderByDesc('created_at')->limit(20)->get():collect();
        return view('payroll.attendance_dtr',compact('data','title','active_link','ready','branches','devices','batches'));
    }

    public function device(Request $request,AttendanceImportService $service)
    {
        $request->merge(['name'=>trim((string)$request->input('name'))]);
        $v=$request->validate(['branch_idx'=>'required|integer','name'=>'required|string|max:100']);
        abort_unless($service->branches()->contains('branch_id',(int)$v['branch_idx']),403);
        $id=DB::table('attendance_devices')->where('branch_idx',$v['branch_idx'])->where('name',trim($v['name']))->value('id');
        if (!$id) $id=DB::table('attendance_devices')->insertGetId(['branch_idx'=>$v['branch_idx'],'name'=>trim($v['name']),'created_at'=>now(),'updated_at'=>now()]);
        return response()->json(['id'=>$id,'name'=>trim($v['name']),'branch_idx'=>(int)$v['branch_idx']]);
    }

    public function upload(Request $request,AttendanceImportService $service)
    {
        $request->validate(['device_id'=>'required|integer','file'=>'required|file|max:10240','file.*'=>'prohibited',
            'cutoff_start'=>'required|date_format:Y-m-d','cutoff_end'=>'required|date_format:Y-m-d|after_or_equal:cutoff_start']);
        $file=$request->file('file');
        abort_unless(in_array(strtolower($file->getClientOriginalExtension()),['xls','xlsx','csv','txt'],true),422,'Use XLS, XLSX, CSV, or TXT attendance exports.');
        try { $id=$service->upload($file,(int)$request->device_id,$request->only('cutoff_start','cutoff_end')); }
        catch (\InvalidArgumentException|\PhpOffice\PhpSpreadsheet\Reader\Exception|\PhpOffice\PhpSpreadsheet\Exception $e) {
            throw ValidationException::withMessages(['file'=>$e->getMessage()]);
        }
        return response()->json(['id'=>$id]);
    }

    public function preview(string $batch,AttendanceImportService $service) { return response()->json($service->preview($batch)); }

    public function cutoff(Request $request,string $batch,AttendanceImportService $service)
    {
        $service->updateCutoff($batch,$request->only('cutoff_start','cutoff_end'));
        return response()->json(['saved'=>true]);
    }

    public function mapping(Request $request,string $batch,AttendanceImportService $service)
    {
        $v=$request->validate(['external_id'=>'required|string|max:100','employee_idx'=>'required|integer']);
        $service->mapEmployee($batch,$v['external_id'],(int)$v['employee_idx']);
        return response()->json(['saved'=>true]);
    }

    public function save(Request $request,string $batch,int $row,AttendanceImportService $service)
    {
        $v=$request->validate([
            'action'=>['required',Rule::in(['save','skip'])],'version'=>'required|integer|min:1',
            'employee_idx'=>'required_if:action,save|integer','confirmed'=>'required_if:action,save|boolean',
            'conflict_hash'=>'required_if:action,save|string|size:64','conflict_action'=>['nullable',Rule::in(['keep','replace'])],
            'proposed'=>'required_if:action,save|array',
            'proposed.log_in'=>'nullable|string','proposed.log_out'=>'nullable|string',
            'proposed.breaktime_start'=>'nullable|string','proposed.breaktime_end'=>'nullable|string',
            'proposed.break_policy'=>['required_if:action,save',Rule::in(['review','recorded','none'])],
            'proposed.overtime_status'=>['required_if:action,save',Rule::in(['No','Yes'])],
            'proposed.override_default_shift'=>['required_if:action,save',Rule::in(['No','Yes'])],
        ]);
        return response()->json($service->save($batch,$row,$v));
    }

    public function source(string $batch,AttendanceImportService $service)
    {
        $data=$service->batch($batch);
        return Storage::disk('local')->download($data->storage_path,$data->filename);
    }
}
