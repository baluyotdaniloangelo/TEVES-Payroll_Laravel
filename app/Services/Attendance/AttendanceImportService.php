<?php

namespace App\Services\Attendance;

use App\Models\EmployeeModel;
use App\Models\EmployeeLogsModel;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AttendanceImportService
{
    public function branches()
    {
        $user=User::where('user_id',session('loginID'))->firstOrFail();
        $query=DB::table('teves_branch_table');
        if (strtoupper(trim((string)$user->user_branch_access_type)) !== 'ALL') {
            $query->whereIn('branch_id',DB::table('teves_payroll_user_branch_access')->where('user_idx',session('loginID'))->select('branch_idx'));
        }
        return $query->orderBy('branch_code')->get(['branch_id','branch_code','branch_name']);
    }

    public function device(int $id)
    {
        $device=DB::table('attendance_devices')->where('id',$id)->first();
        abort_unless($device && $this->branches()->contains('branch_id',$device->branch_idx),403);
        return $device;
    }

    public function batch(string $id)
    {
        $batch=DB::table('attendance_imports')->where('id',$id)->where('created_by',session('loginID'))->first();
        abort_unless($batch,404); $this->device($batch->device_id);
        return $batch;
    }

    public function employees(int $branch)
    {
        return EmployeeModel::where('branch_idx',$branch)->orderBy('employee_last_name')->get([
            'employee_id','employee_number','employee_full_name','employee_first_name','employee_last_name',
        ])->map(fn($e)=>['id'=>$e->employee_id,'number'=>$e->employee_number,
            'name'=>$e->employee_full_name ?: trim($e->employee_first_name.' '.$e->employee_last_name)]);
    }

    public function upload($file, int $deviceId, array $cutoff = []): string
    {
        $this->device($deviceId);
        if ($cutoff) $cutoff=$this->validateCutoff($cutoff);
        $data=app(AttendanceFileParser::class)->parse($file->getRealPath(),$file->getClientOriginalExtension());
        if (count($data['rows'])>5000) throw ValidationException::withMessages(['file'=>'Upload at most 5,000 daily records per file.']);
        $id=(string)Str::uuid(); $path=$file->storeAs('attendance-imports/'.$id,'source.'.strtolower($file->getClientOriginalExtension()),'local');
        try {
            DB::transaction(function() use($id,$path,$file,$deviceId,$data,$cutoff) {
                DB::table('attendance_imports')->insert(['id'=>$id,'device_id'=>$deviceId,'created_by'=>session('loginID'),
                    'filename'=>basename($file->getClientOriginalName()),'storage_path'=>$path,
                    'file_hash'=>hash_file('sha256',$file->getRealPath()),'format'=>$data['format'],
                    'cutoff_start'=>$cutoff['cutoff_start']??null,'cutoff_end'=>$cutoff['cutoff_end']??null,'created_at'=>now(),'updated_at'=>now()]);
                foreach ($data['rows'] as $row) {
                    if ($row['external_id']==='' || strlen($row['external_id'])>100) throw ValidationException::withMessages(['file'=>'Missing or oversized device employee ID.']);
                    DB::table('attendance_import_rows')->insert([
                        'import_id'=>$id,'external_id'=>$row['external_id'],'external_name'=>$row['external_name'],
                        'attendance_date'=>$row['attendance_date'],'raw'=>json_encode($row['raw'],JSON_THROW_ON_ERROR),
                        'proposed'=>json_encode($row['proposed'],JSON_THROW_ON_ERROR),'warnings'=>json_encode($row['warnings'],JSON_THROW_ON_ERROR),
                        'created_at'=>now(),'updated_at'=>now(),
                    ]);
                }
            });
        } catch (\Throwable $e) { Storage::disk('local')->delete($path); throw $e; }
        return $id;
    }

    public function preview(string $id): array
    {
        $batch=$this->batch($id); $device=$this->device($batch->device_id);
        $rows=DB::table('attendance_import_rows')->where('import_id',$id)->orderBy('attendance_date')->orderBy('id')->get();
        $mapping=DB::table('attendance_employee_mappings')->where('device_id',$device->id)->pluck('employee_idx','external_id');
        $employees=$this->employees($device->branch_idx);
        $existing=DB::table('teves_payroll_employee_logs')->where('branch_idx',$device->branch_idx)
            ->whereBetween('attendance_date',[$rows->min('attendance_date'),$rows->max('attendance_date')])->get();
        $raw=$rows->first()?json_decode($rows->first()->raw,true):[];
        $period=$raw['period']??[$rows->min('attendance_date'),$rows->max('attendance_date')];
        return ['batch'=>['id'=>$batch->id,'filename'=>$batch->filename,'format'=>$batch->format,'device'=>$device->name,
                'branch'=>DB::table('teves_branch_table')->where('branch_id',$device->branch_idx)->value('branch_name'),
                'period_start'=>$period[0],'period_end'=>$period[1],
                'cutoff_start'=>$batch->cutoff_start??$period[0],'cutoff_end'=>$batch->cutoff_end??$period[1]],
            'employees'=>$employees,'rows'=>$rows->map(function($row)use($mapping,$employees,$existing){
                $proposed=json_decode($row->proposed,true);
                if ($row->review) $proposed=array_merge($proposed,json_decode($row->review,true)['proposal']??[]);
                $mapped=$mapping[$row->external_id]??null;
                if (!$employees->contains('id',$mapped)) $mapped=null;
                $employee=$row->employee_idx ?: $mapped;
                if (!$employees->contains('id',$employee)) $employee=null;
                $conflicts=$existing->filter(fn($log)=>(int)$log->employee_idx===(int)$employee && $log->attendance_date===$row->attendance_date)->values();
                return ['id'=>$row->id,'external_id'=>$row->external_id,'external_name'=>$row->external_name,
                    'date'=>$row->attendance_date,'raw'=>json_decode($row->raw,true),'proposed'=>$proposed,
                    'warnings'=>json_decode($row->warnings,true),'employee_idx'=>$employee,'mapped_employee_idx'=>$mapped,'status'=>$row->status,
                    'version'=>$row->version,'saved_log_id'=>$row->saved_log_id,
                    'conflicts'=>$conflicts->map(fn($l)=>$this->conflictView($l))->all(),'conflict_hash'=>$this->fingerprint($conflicts)];
            })->all()];
    }

    public function mapEmployee(string $batchId,string $external,int $employee): void
    {
        $batch=$this->batch($batchId);$device=$this->device($batch->device_id);
        abort_unless(DB::table('attendance_import_rows')->where('import_id',$batchId)->where('external_id',$external)->exists(),404);
        abort_unless(EmployeeModel::where('employee_id',$employee)->where('branch_idx',$device->branch_idx)->exists(),422,'Employee must belong to this branch.');
        DB::table('attendance_employee_mappings')->updateOrInsert(['device_id'=>$device->id,'external_id'=>$external],
            ['employee_idx'=>$employee,'updated_by'=>session('loginID'),'updated_at'=>now(),'created_at'=>now()]);
    }

    public function validateCutoff(array $input): array
    {
        $dates=\Illuminate\Support\Facades\Validator::make($input,[
            'cutoff_start'=>'required|date_format:Y-m-d',
            'cutoff_end'=>'required|date_format:Y-m-d|after_or_equal:cutoff_start',
        ])->validate();
        if (CarbonImmutable::parse($dates['cutoff_start'])->diffInDays(CarbonImmutable::parse($dates['cutoff_end']))>365) {
            throw ValidationException::withMessages(['cutoff_end'=>'Select a cutoff of up to 366 calendar days.']);
        }
        return $dates;
    }

    public function updateCutoff(string $batchId,array $input): void
    {
        $this->batch($batchId);$dates=$this->validateCutoff($input);
        DB::transaction(function()use($batchId,$dates){
            DB::table('attendance_imports')->where('id',$batchId)->lockForUpdate()->first();
            DB::table('attendance_imports')->where('id',$batchId)->update($dates+['updated_at'=>now()]);
        });
    }

    private function conflictView($log): array
    {
        return ['id'=>$log->employee_logs_id,'type'=>$log->log_type,'in'=>$log->log_in,'out'=>$log->log_out,
            'break_start'=>$log->breaktime_start,'break_end'=>$log->breaktime_end];
    }

    private function fingerprint($rows): string
    {
        return hash('sha256',json_encode($rows->sortBy('employee_logs_id')->values()->all(),JSON_THROW_ON_ERROR));
    }

    public function save(string $batchId,int $rowId,array $input): array
    {
        $batch=$this->batch($batchId); $device=$this->device($batch->device_id);
        return DB::transaction(function()use($batchId,$rowId,$input,$device) {
            $activeBatch=DB::table('attendance_imports')->where('id',$batchId)->lockForUpdate()->first();
            $row=DB::table('attendance_import_rows')->where('id',$rowId)->where('import_id',$batchId)->lockForUpdate()->first();
            abort_unless($row,404);
            if (($activeBatch->cutoff_start && $row->attendance_date<$activeBatch->cutoff_start)
                || ($activeBatch->cutoff_end && $row->attendance_date>$activeBatch->cutoff_end)) {
                throw ValidationException::withMessages(['cutoff'=>'This attendance date is outside the selected cutoff. Apply the desired cutoff and review again.']);
            }
            if ($row->status==='saved') return ['status'=>'saved','log_id'=>$row->saved_log_id,'already_saved'=>true];
            abort_unless((int)$row->version===(int)$input['version'],409,'This row changed. Refresh the preview.');
            if ($input['action']==='skip') {
                DB::table('attendance_import_rows')->where('id',$rowId)->update(['status'=>'skipped','version'=>$row->version+1,'updated_at'=>now()]);
                return ['status'=>'skipped'];
            }
            if (empty($input['confirmed'])) throw ValidationException::withMessages(['confirmed'=>'Review this row and confirm its punches and dates.']);
            $employee=EmployeeModel::where('employee_id',$input['employee_idx'])->where('branch_idx',$device->branch_idx)->lockForUpdate()->first();
            abort_unless($employee,422,'Select an employee from this branch.');
            $proposal=$input['proposed'];
            $proposal=$this->validateProposal($proposal,$row->attendance_date);
            $conflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)->lockForUpdate()->get();
            abort_unless(hash_equals($this->fingerprint($conflicts),$input['conflict_hash']),409,'Attendance or employee mapping changed. Refresh before saving.');
            $isOT=$proposal['overtime_status']==='Yes';
            $same=$conflicts->filter(fn($l)=>str_ends_with(strtolower($l->log_type),'ot')===$isOT);
            $target=0;
            if ($same->isNotEmpty()) {
                if (($input['conflict_action']??'keep')==='keep') return ['status'=>'kept','message'=>'Existing attendance retained.'];
                abort_unless($same->count()===1,409,'Multiple existing intervals need manual review in Employee Logs.');
                $target=$same->first()->employee_logs_id;
            }
            $request=Request::create('/attendance-import-save','POST',array_merge($proposal,[
                'branch_idx'=>$device->branch_idx,'employee_idx'=>$employee->employee_id,'employee_logs_id'=>$target,'attendance_date'=>$row->attendance_date,
            ]));
            $response=app(AttendanceLogWriter::class)->save($request);
            if (!$response || !in_array($response->getData(true)['success']??'', ['Created','Updated'],true)) {
                throw ValidationException::withMessages(['attendance'=>'Could not save this interval. No changes were committed.']);
            }
            $log=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)
                ->when($target,fn($q)=>$q->where('employee_logs_id',$target))->orderByDesc('employee_logs_id')->first();
            DB::table('attendance_import_rows')->where('id',$rowId)->update(['employee_idx'=>$employee->employee_id,'saved_log_id'=>$log->employee_logs_id,
                'status'=>'saved','version'=>$row->version+1,'review'=>json_encode(['proposal'=>$proposal,'replaced_log'=>$target?$this->conflictView($same->first()):null]),
                'saved_by'=>session('loginID'),'saved_at'=>now(),'updated_at'=>now()]);
            // Return the snapshot produced by this save so another reviewed interval on
            // the same date (e.g. regular plus OT) can use it without a false stale conflict.
            $current=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)->get();
            return ['status'=>'saved','log_id'=>$log->employee_logs_id,'version'=>$row->version+1,
                'conflict_hash'=>$this->fingerprint($current),'conflicts'=>$current->map(fn($l)=>$this->conflictView($l))->all()];
        });
    }

    public function validateProposal(array $p,string $date): array
    {
        $times=[];
        foreach (['log_in','log_out'] as $key) {
            $times[$key]=$this->stamp($p[$key]??null,$key);
        }
        $in=$times['log_in'];$out=$times['log_out'];
        if ($in->format('Y-m-d')!==$date || $out <= $in || $in->diffInHours($out)>24) {
            throw ValidationException::withMessages(['proposed.log_out'=>'Use explicit dates with an In on the attendance date and an Out within the following 24 hours.']);
        }
        if ($p['break_policy']==='none') {
            // Explicit reviewer choice. A zero-length break is accepted by the manual writer.
            $times['breaktime_start']=$in; $times['breaktime_end']=$in;
        } elseif ($p['break_policy']==='recorded') {
            $times['breaktime_start']=$this->stamp($p['breaktime_start']??null,'breaktime_start');
            $times['breaktime_end']=$this->stamp($p['breaktime_end']??null,'breaktime_end');
        } else {
            throw ValidationException::withMessages(['proposed.break_policy'=>'Choose recorded/manual break or explicitly confirm no break.']);
        }
        if ($times['breaktime_start']<$in || $times['breaktime_end']<$times['breaktime_start'] || $times['breaktime_end']>$out) {
            throw ValidationException::withMessages(['proposed.breaktime_end'=>'Break must be inside the working interval and in chronological order.']);
        }
        if ($p['overtime_status']==='Yes' && $times['breaktime_start']!=$times['breaktime_end']) {
            throw ValidationException::withMessages(['proposed.break_policy'=>'Split overtime around breaks into separate manual intervals. The existing overtime calculation does not deduct breaks.']);
        }
        foreach ($times as $key=>$time) $p[$key]=$time->format('Y-m-d H:i:s');
        return $p;
    }

    private function stamp(?string $text,string $key): CarbonImmutable
    {
        if (!$text || !preg_match('/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}(?::\d{2})?$/',$text)) {
            throw ValidationException::withMessages(['proposed.'.$key=>'Enter a full date and time.']);
        }
        try {
            $format=strlen($text)===16?'Y-m-d H:i':'Y-m-d H:i:s';
            $normalized=str_replace('T',' ',$text);
            $d=CarbonImmutable::createFromFormat('!'.$format,$normalized);
            if (!$d || $d->format($format)!==$normalized) throw new \InvalidArgumentException();
            return $d;
        } catch (\Throwable $e) { throw ValidationException::withMessages(['proposed.'.$key=>'Invalid date or time.']); }
    }
}
