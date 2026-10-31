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

    public function savedEntries(Request $request,AttendanceImportService $service)
    {
        $filters=$request->validate([
            'branch_id'=>['nullable','integer'],
            'period'=>['nullable','string','max:21'],
            'employee'=>['nullable','string','max:100'],
        ]);
        $data=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(strcasecmp(trim((string)$data->user_type),'Employee')!==0,403);
        $branches=$service->branches();
        $branchIds=$branches->pluck('branch_id');
        $branchId=!empty($filters['branch_id'])?(int)$filters['branch_id']:null;
        abort_if($branchId && !$branchIds->contains($branchId),403,'You do not have access to this branch.');

        $periods=DB::table('attendance_imports as i')->join('attendance_devices as d','d.id','=','i.device_id')
            ->whereIn('d.branch_idx',$branchIds)->when($branchId,fn($q)=>$q->where('d.branch_idx',$branchId))
            ->whereNotNull('i.cutoff_start')->whereNotNull('i.cutoff_end')
            ->select('i.cutoff_start','i.cutoff_end')->distinct()->orderByDesc('i.cutoff_start')->get();
        $period=(string)($filters['period']??'');
        if (!$period || !$periods->contains(fn($item)=>$period===$item->cutoff_start.'|'.$item->cutoff_end)) {
            $period=$periods->isNotEmpty()?$periods->first()->cutoff_start.'|'.$periods->first()->cutoff_end:'';
        }
        [$cutoffStart,$cutoffEnd]=array_pad(explode('|',$period,2),2,null);
        $employeeSearch=trim((string)($filters['employee']??''));

        $rows=DB::table('attendance_import_rows as r')->join('attendance_imports as i','i.id','=','r.import_id')
            ->join('attendance_devices as d','d.id','=','i.device_id')->join('teves_branch_table as b','b.branch_id','=','d.branch_idx')
            ->leftJoin('teves_payroll_employee_table as e','e.employee_id','=','r.employee_idx')
            ->leftJoin('teves_payroll_employee_logs as l','l.employee_logs_id','=','r.saved_log_id')
            ->whereIn('d.branch_idx',$branchIds)->whereNotNull('i.cutoff_start')->whereNotNull('i.cutoff_end')
            ->whereIn('r.status',['saved','employee_pending','employee_submitted'])
            ->whereNotNull('r.employee_idx')
            ->when($cutoffStart,fn($q)=>$q->where('i.cutoff_start',$cutoffStart)->where('i.cutoff_end',$cutoffEnd))
            ->when($branchId,fn($q)=>$q->where('d.branch_idx',$branchId))
            ->when($employeeSearch,function($q)use($employeeSearch){
                $like='%'.$employeeSearch.'%';
                $q->where(function($nested)use($like){
                    $nested->where('e.employee_number','like',$like)
                        ->orWhereRaw("CONCAT(e.employee_last_name, ', ', e.employee_first_name, ' ', IFNULL(e.employee_middle_name,''), ' ', IFNULL(e.employee_extension_name,'')) LIKE ?",[$like]);
                });
            })
            ->orderBy('b.branch_code')->orderBy('i.cutoff_start','desc')->orderBy('e.employee_last_name')->orderBy('r.attendance_date')
            ->get([
                'r.id as import_row_id','r.import_id','r.employee_idx','r.status','r.version','r.proposed','r.review','r.external_name','r.saved_log_id',
                'i.filename','i.cutoff_start','i.cutoff_end','d.name as device_name','d.branch_idx',
                'b.branch_code','b.branch_name','e.employee_number',
                DB::raw("CONCAT(e.employee_last_name, ', ', e.employee_first_name, ' ', IFNULL(e.employee_middle_name,''), ' ', IFNULL(e.employee_extension_name,'')) as employee_name"),
                'l.employee_logs_id','r.attendance_date','l.log_in','l.breaktime_start','l.breaktime_end','l.log_out',
                'l.total_regular_hours','l.total_regular_overtime_hours','l.total_restday_hours','l.total_restday_overtime_hours',
                'l.total_hours','l.cutoff_idx','l.log_type','l.override_shift',
            ]);
        $employeeGroups=$rows->groupBy(fn($row)=>$row->branch_idx.'|'.$row->employee_idx);
        $failedNotices=Schema::hasTable('attendance_employee_review_notifications')
            ?DB::table('attendance_employee_review_notifications')
                ->whereIn('import_id',$rows->pluck('import_id')->unique())
                ->where('status','failed')->get()
                ->keyBy(fn($notice)=>$notice->import_id.'|'.$notice->employee_idx)
            :collect();
        $title='Saved DTR Entries'; $active_link='saved_dtr_entries';
        return view('payroll.saved_dtr_entries',compact('data','branches','branchId','periods','period','employeeSearch','employeeGroups','failedNotices','title','active_link'));
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

    public function preview(Request $request,string $batch,AttendanceImportService $service)
    {
        if (!$request->expectsJson()) return redirect()->route('attendance-import.index');
        return response()->json($service->preview($batch))
            ->header('Cache-Control','private, no-store, no-cache, must-revalidate')
            ->header('X-Content-Type-Options','nosniff');
    }

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

    public function queueMapped(string $batch,AttendanceImportService $service)
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(strcasecmp(trim((string)$user->user_type),'Employee')!==0,403);
        return response()->json($service->queueMappedRows($batch));
    }

    public function save(Request $request,string $batch,int $row,AttendanceImportService $service)
    {
        $v=$request->validate([
            'action'=>['required',Rule::in(['save','skip','restore'])],'version'=>'required|integer|min:1',
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

    public function approve(Request $request,string $batch,int $row,AttendanceImportService $service)
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(in_array($user->user_type,['Admin','Supervisor','Accounting_Staff'],true),403,'Only payroll reviewers can approve employee DTR.');
        return response()->json($service->approveEmployeeDtr($batch,$row));
    }

    public function returnLogToEmployee(Request $request,int $log,AttendanceImportService $service)
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(in_array($user->user_type,['Admin','Supervisor','Accounting_Staff'],true),403,'Only payroll reviewers can return posted DTR.');
        $result=$service->returnPostedImportToEmployee($log);
        $message=$result['email_sent']
            ?'DTR returned to My DTR for correction. The employee was emailed.'
            :'DTR returned to My DTR for correction. No notification email was sent.';
        if ($request->expectsJson()) return response()->json($result+['message'=>$message]);
        return redirect()->route('attendance-import.saved',$request->only('period','branch_id','employee'))->with('success',$message);
    }

    public function updateSavedDtr(Request $request,int $log,AttendanceImportService $service)
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(in_array($user->user_type,['Admin','Supervisor','Accounting_Staff'],true),403,'Only payroll reviewers can edit saved DTR.');
        $values=$request->validate([
            'log_in'=>['required','date_format:Y-m-d\\TH:i'],
            'log_out'=>['required','date_format:Y-m-d\\TH:i'],
            'break_policy'=>['required',Rule::in(['none','recorded'])],
            'breaktime_start'=>['nullable','required_if:break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'breaktime_end'=>['nullable','required_if:break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'override_default_shift'=>['required',Rule::in(['No','Yes'])],
        ]);
        $service->updateSavedImportedDtr($log,$values);
        if ($request->expectsJson()) {
            return response()->json(['message'=>'DTR changes saved and posted. Payroll hours were recalculated.']);
        }
        return redirect()->route('attendance-import.saved',$request->only('period','branch_id','employee'))
            ->with('success','Saved DTR updated and payroll hours were recalculated.');
    }

    public function completePendingDtr(Request $request,int $row,AttendanceImportService $service)
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(in_array($user->user_type,['Admin','Supervisor','Accounting_Staff'],true),403,'Only payroll reviewers can complete employee DTR.');
        $values=$request->validate([
            'version'=>['required','integer','min:1'],
            'log_in'=>['required','date_format:Y-m-d\\TH:i'],
            'log_out'=>['required','date_format:Y-m-d\\TH:i'],
            'break_policy'=>['required',Rule::in(['none','recorded'])],
            'breaktime_start'=>['nullable','required_if:break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'breaktime_end'=>['nullable','required_if:break_policy,recorded','date_format:Y-m-d\\TH:i'],
            'override_default_shift'=>['required',Rule::in(['No','Yes'])],
        ]);
        $service->completePendingImportedDtr($row,$values);
        if ($request->expectsJson()) {
            return response()->json(['message'=>'DTR completed and posted. Work hours and pay were recalculated.']);
        }
        return redirect()->route('attendance-import.saved',$request->only('period','branch_id','employee'))
            ->with('success','DTR completed and posted by payroll. Work hours and pay were recalculated.');
    }

    public function returnSubmittedDtr(Request $request,int $row,AttendanceImportService $service)
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(in_array($user->user_type,['Admin','Supervisor','Accounting_Staff'],true),403);
        $values=$request->validate(['reason'=>['required','string','max:500']]);
        $result=$service->returnSubmittedDtrToEmployee($row,trim($values['reason']));
        $result['message']=$result['email_sent']
            ?'DTR returned for correction. The employee was emailed.'
            :'DTR returned for correction. The employee can see it in My DTR, but no email was sent.';
        if($request->expectsJson())return response()->json($result);
        return redirect()->route('attendance-import.saved',$request->only('period','branch_id','employee'))
            ->with('success',$result['message']);
    }

    public function resendEmployeeDtr(Request $request,string $batch,int $employee,AttendanceImportService $service)
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        abort_unless(in_array($user->user_type,['Admin','Supervisor','Accounting_Staff'],true),403);
        $result=$service->resendEmployeeDtrNotice($batch,$employee);
        if($request->expectsJson())return response()->json($result);
        return redirect()->route('attendance-import.saved',$request->only('period','branch_id','employee'))
            ->with('success',$result['message']);
    }

    public function source(string $batch,AttendanceImportService $service)
    {
        $data=$service->batch($batch);
        return Storage::disk('local')->download($data->storage_path,$data->filename);
    }
}
