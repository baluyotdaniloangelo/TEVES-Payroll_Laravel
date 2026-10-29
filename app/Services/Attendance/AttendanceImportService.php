<?php

namespace App\Services\Attendance;

use App\Models\EmployeeModel;
use App\Models\EmployeeLogsModel;
use App\Models\User;
use App\Mail\EmployeeDtrReviewMail;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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
                $review=$row->review?json_decode($row->review,true):[];
                $proposed=$review['employee_proposal']??$review['admin_proposal']??array_merge(json_decode($row->proposed,true),$review['proposal']??[]);
                $mapped=$mapping[$row->external_id]??null;
                if (!$employees->contains('id',$mapped)) $mapped=null;
                $employee=$row->employee_idx ?: $mapped;
                if (!$employees->contains('id',$employee)) $employee=null;
                $conflicts=$existing->filter(fn($log)=>(int)$log->employee_idx===(int)$employee && $log->attendance_date===$row->attendance_date)->values();
                return ['id'=>$row->id,'external_id'=>$row->external_id,'external_name'=>$row->external_name,
                    'date'=>$row->attendance_date,'raw'=>json_decode($row->raw,true),'proposed'=>$proposed,
                    'warnings'=>json_decode($row->warnings,true),'employee_idx'=>$employee,'mapped_employee_idx'=>$mapped,'status'=>$row->status,
                    'version'=>$row->version,'saved_log_id'=>$row->saved_log_id,
                    'employee_note'=>$review['employee_note']??null,'employee_submitted_at'=>$review['employee_submitted_at']??null,
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

    public function queueMappedRows(string $batchId): array
    {
        $batch=$this->batch($batchId);$device=$this->device($batch->device_id);
        $queued=DB::transaction(function()use($batchId,$device){
            $activeBatch=DB::table('attendance_imports')->where('id',$batchId)->lockForUpdate()->first();
            $rows=DB::table('attendance_import_rows')->where('import_id',$batchId)
                ->whereIn('status',['pending','employee_pending'])
                ->when($activeBatch->cutoff_start,fn($q)=>$q->where('attendance_date','>=',$activeBatch->cutoff_start))
                ->when($activeBatch->cutoff_end,fn($q)=>$q->where('attendance_date','<=',$activeBatch->cutoff_end))
                ->orderBy('attendance_date')->orderBy('id')->lockForUpdate()->get();
            $mappings=DB::table('attendance_employee_mappings')->where('device_id',$device->id)->pluck('employee_idx','external_id');
            $employeeIds=$rows->map(fn($row)=>(int)($row->employee_idx?:($mappings[$row->external_id]??0)))->filter()->unique()->values();
            $employees=EmployeeModel::where('branch_idx',$device->branch_idx)->whereIn('employee_id',$employeeIds)->get([
                'employee_id','employee_full_name','employee_first_name','employee_last_name','employee_number','branch_idx'
            ])->keyBy('employee_id');
            $accounts=DB::table('teves_payroll_user_table')->whereIn('employee_idx',$employeeIds)->where('user_type','Employee')
                ->get(['user_id','employee_idx','user_email_address'])->keyBy('employee_idx');
            $queuedCount=0;$unmapped=0;$missingAccounts=[];$groups=[];$conflicts=[];
            foreach($rows as $row){
                $employeeId=(int)($row->employee_idx?:($mappings[$row->external_id]??0));
                $employee=$employees->get($employeeId);$account=$accounts->get($employeeId);
                if(!$employee){$unmapped++;continue;}
                if(!$account||!filter_var($account->user_email_address,FILTER_VALIDATE_EMAIL)){
                    $missingAccounts[$employeeId]=$employee->employee_number.' — '.($employee->employee_full_name?:trim($employee->employee_first_name.' '.$employee->employee_last_name));
                    continue;
                }
                if($row->status==='pending'){
                    $review=json_decode($row->review,true)?:[];
                    $proposal=array_merge(['break_policy'=>'review','overtime_status'=>'No','override_default_shift'=>'No'],json_decode($row->proposed,true)?:[]);
                    $conflictKey=$employeeId.'|'.$row->attendance_date;
                    if(!array_key_exists($conflictKey,$conflicts)){
                        $matches=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employeeId)->where('attendance_date',$row->attendance_date)->lockForUpdate()->get();
                        $conflicts[$conflictKey]=$this->fingerprint($matches);
                    }
                    $review['admin_proposal']=$proposal;
                    $review['conflict_action']='keep';
                    $review['conflict_hash']=$conflicts[$conflictKey];
                    $review['queued_by_user_idx']=session('loginID');
                    $review['queued_at']=now()->toDateTimeString();
                    DB::table('attendance_import_rows')->where('id',$row->id)->update([
                        'employee_idx'=>$employeeId,'status'=>'employee_pending','version'=>$row->version+1,
                        'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
                    ]);
                    $queuedCount++;
                }
                if(!isset($groups[$employeeId]))$groups[$employeeId]=[
                    'employee_id'=>$employeeId,'user_id'=>$account->user_id,'email'=>$account->user_email_address,
                    'name'=>$employee->employee_full_name?:trim($employee->employee_first_name.' '.$employee->employee_last_name),
                ];
            }
            $notifications=[];$alreadySent=0;
            foreach($groups as $employeeId=>$group){
                $notice=DB::table('attendance_employee_review_notifications')->where('import_id',$batchId)->where('employee_idx',$employeeId)->first();
                if(!$notice){
                    $noticeId=DB::table('attendance_employee_review_notifications')->insertGetId([
                        'import_id'=>$batchId,'employee_idx'=>$employeeId,'user_idx'=>$group['user_id'],'recipient_email'=>$group['email'],
                        'status'=>'pending','created_at'=>now(),'updated_at'=>now(),
                    ]);
                    $notice=DB::table('attendance_employee_review_notifications')->where('id',$noticeId)->first();
                }elseif($notice->status!=='sent'){
                    DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->update([
                        'user_idx'=>$group['user_id'],'recipient_email'=>$group['email'],'status'=>'pending','failure_message'=>null,'updated_at'=>now(),
                    ]);
                    $notice=DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->first();
                }
                if($notice->status==='sent'){$alreadySent++;continue;}
                $notifications[]=$group+['notice_id'=>$notice->id,'cutoff_start'=>$activeBatch->cutoff_start,'cutoff_end'=>$activeBatch->cutoff_end];
            }
            return ['queued'=>$queuedCount,'unmapped'=>$unmapped,'missing_accounts'=>array_values($missingAccounts),
                'already_sent'=>$alreadySent,'notifications'=>$notifications];
        });

        $sent=0;$failed=[];
        foreach($queued['notifications'] as $notification){
            try{
                Mail::to($notification['email'])->send(new EmployeeDtrReviewMail(
                    $notification['name'],$notification['cutoff_start'],$notification['cutoff_end'],route('my-dtr.index',['batch'=>$batchId])
                ));
                DB::table('attendance_employee_review_notifications')->where('id',$notification['notice_id'])->update([
                    'status'=>'sent','sent_at'=>now(),'failure_message'=>null,'updated_at'=>now(),
                ]);
                $sent++;
            }catch(\Throwable $exception){
                Log::warning('Employee DTR review email failed.',[
                    'import_id'=>$batchId,'employee_idx'=>$notification['employee_id'],'user_id'=>$notification['user_id'],
                ]);
                DB::table('attendance_employee_review_notifications')->where('id',$notification['notice_id'])->update([
                    'status'=>'failed','failure_message'=>'Mail transport failed. Review Payroll mail settings and retry.','updated_at'=>now(),
                ]);
                $failed[]=$notification['name'];
            }
        }
        unset($queued['notifications']);
        return $queued+['emails_sent'=>$sent,'email_failures'=>$failed];
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
        $notification = null;
        $result = DB::transaction(function()use($batchId,$rowId,$input,$device,&$notification) {
            $activeBatch=DB::table('attendance_imports')->where('id',$batchId)->lockForUpdate()->first();
            $row=DB::table('attendance_import_rows')->where('id',$rowId)->where('import_id',$batchId)->lockForUpdate()->first();
            abort_unless($row,404);
            if (($activeBatch->cutoff_start && $row->attendance_date<$activeBatch->cutoff_start)
                || ($activeBatch->cutoff_end && $row->attendance_date>$activeBatch->cutoff_end)) {
                throw ValidationException::withMessages(['cutoff'=>'This attendance date is outside the selected cutoff. Apply the desired cutoff and review again.']);
            }
            if ($row->status==='saved') return ['status'=>'saved','log_id'=>$row->saved_log_id,'already_saved'=>true];
            abort_unless($row->status!=='employee_submitted',409,'This DTR was already submitted by the employee. Review and approve it before making another change.');
            abort_unless((int)$row->version===(int)$input['version'],409,'This row changed. Refresh the preview.');
            if ($input['action']==='skip') {
                DB::table('attendance_import_rows')->where('id',$rowId)->update(['status'=>'skipped','version'=>$row->version+1,'updated_at'=>now()]);
                return ['status'=>'skipped'];
            }
            if ($input['action']==='restore') {
                abort_unless($row->status==='skipped',409,'Only a skipped interval can be restored.');
                DB::table('attendance_import_rows')->where('id',$rowId)->update(['status'=>'pending','version'=>$row->version+1,'updated_at'=>now()]);
                return ['status'=>'pending','version'=>$row->version+1];
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
            $account=DB::table('teves_payroll_user_table')->where('employee_idx',$employee->employee_id)->where('user_type','Employee')->first(['user_id','user_email_address']);
            abort_unless($account && filter_var($account->user_email_address,FILTER_VALIDATE_EMAIL),422,
                'Create and link an Employee user account with a valid email before sending this DTR for completion.');
            $review=json_decode($row->review,true)?:[];
            $review['admin_proposal']=$proposal;
            $review['conflict_action']=$input['conflict_action']??'keep';
            $review['conflict_hash']=$input['conflict_hash'];
            $review['reviewed_by_user_idx']=session('loginID');
            $review['reviewed_at']=now()->toDateTimeString();
            DB::table('attendance_import_rows')->where('id',$rowId)->update([
                'employee_idx'=>$employee->employee_id,'status'=>'employee_pending','version'=>$row->version+1,
                'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
            ]);
            $notice=DB::table('attendance_employee_review_notifications')->where('import_id',$batchId)->where('employee_idx',$employee->employee_id)->first();
            if (!$notice) {
                DB::table('attendance_employee_review_notifications')->insert([
                    'import_id'=>$batchId,'employee_idx'=>$employee->employee_id,'user_idx'=>$account->user_id,
                    'recipient_email'=>$account->user_email_address,'status'=>'pending','created_at'=>now(),'updated_at'=>now(),
                ]);
            }
            $notification=['employee_id'=>$employee->employee_id,'user_id'=>$account->user_id,'email'=>$account->user_email_address,
                'name'=>$employee->employee_full_name?:trim($employee->employee_first_name.' '.$employee->employee_last_name),
                'cutoff_start'=>$activeBatch->cutoff_start,'cutoff_end'=>$activeBatch->cutoff_end];
            return ['status'=>'employee_pending','version'=>$row->version+1];
        });
        if ($notification && $result['status']==='employee_pending') {
            $notice=DB::table('attendance_employee_review_notifications')->where('import_id',$batchId)->where('employee_idx',$notification['employee_id'])->first();
            if ($notice && $notice->status!=='sent') {
                try {
                    Mail::to($notice->recipient_email)->send(new EmployeeDtrReviewMail(
                        $notification['name'], $notification['cutoff_start'], $notification['cutoff_end'], route('my-dtr.index',['batch'=>$batchId])
                    ));
                    DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->update(['status'=>'sent','sent_at'=>now(),'failure_message'=>null,'updated_at'=>now()]);
                    $result['email_sent']=true;
                } catch (\Throwable $exception) {
                    Log::warning('Employee DTR review email failed.', ['import_id'=>$batchId,'employee_idx'=>$notification['employee_id'],'user_id'=>$notification['user_id']]);
                    DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->update(['status'=>'failed','failure_message'=>'Mail transport failed. Review Payroll mail settings and retry.','updated_at'=>now()]);
                    $result['email_sent']=false;
                    $result['email_message']='DTR was saved for employee review, but the email could not be sent. Check mail settings and retry by saving a reviewed interval again.';
                }
            } else { $result['email_sent']=true; $result['email_already_sent']=true; }
        }
        return $result;
    }

    public function employeeItems(int $employeeId)
    {
        return DB::table('attendance_import_rows as r')->join('attendance_imports as i','i.id','=','r.import_id')
            ->join('attendance_devices as d','d.id','=','i.device_id')->join('teves_branch_table as b','b.branch_id','=','d.branch_idx')
            ->where('r.employee_idx',$employeeId)->whereIn('r.status',['employee_pending','employee_submitted'])
            ->orderBy('i.cutoff_start')->orderBy('r.attendance_date')->get([
                'r.id','r.import_id','r.attendance_date','r.raw','r.proposed','r.review','r.warnings','r.status','r.version',
                'i.filename','i.cutoff_start','i.cutoff_end','b.branch_code','b.branch_name',
            ])->map(function($row){
                $review=json_decode($row->review,true)?:[];
                $proposal=$review['employee_proposal']??$review['admin_proposal']??json_decode($row->proposed,true);
                $missing=[];
                foreach(['log_in'=>'time-in','log_out'=>'time-out'] as $field=>$label){if(empty($proposal[$field]))$missing[]=$label;}
                if(($proposal['break_policy']??'review')==='review')$missing[]='break treatment';
                elseif(($proposal['break_policy']??null)==='recorded'){
                    if(empty($proposal['breaktime_start']))$missing[]='break-out';
                    if(empty($proposal['breaktime_end']))$missing[]='break-in';
                }
                return ['id'=>$row->id,'import_id'=>$row->import_id,'date'=>$row->attendance_date,'raw'=>json_decode($row->raw,true),
                    'proposal'=>$proposal,'warnings'=>json_decode($row->warnings,true),'status'=>$row->status,'version'=>$row->version,
                    'filename'=>$row->filename,'cutoff_start'=>$row->cutoff_start,'cutoff_end'=>$row->cutoff_end,
                    'branch_code'=>$row->branch_code,'branch_name'=>$row->branch_name,'note'=>$review['employee_note']??null,'missing'=>$missing];
            });
    }

    public function submitEmployeeDtr(int $employeeId,int $rowId,array $input): void
    {
        DB::transaction(function()use($employeeId,$rowId,$input){
            $row=DB::table('attendance_import_rows')->where('id',$rowId)->where('employee_idx',$employeeId)->lockForUpdate()->first();
            abort_unless($row,404);
            abort_unless($row->status==='employee_pending',409,'This DTR is no longer awaiting employee completion.');
            abort_unless((int)$row->version===(int)$input['version'],409,'This DTR changed. Refresh the page and review again.');
            $review=json_decode($row->review,true)?:[];
            $adminProposal=$review['admin_proposal']??json_decode($row->proposed,true);
            $employeeFields=array_intersect_key($input['proposed'],array_flip(['log_in','log_out','break_policy','breaktime_start','breaktime_end']));
            foreach(['log_in','log_out','breaktime_start','breaktime_end'] as $field){
                if(!empty($adminProposal[$field]))$employeeFields[$field]=$adminProposal[$field];
            }
            if(($adminProposal['break_policy']??'review')!=='review')$employeeFields['break_policy']=$adminProposal['break_policy'];
            $proposal=array_merge($adminProposal,$employeeFields);
            $proposal['overtime_status']=$review['admin_proposal']['overtime_status']??$proposal['overtime_status'];
            $proposal['override_default_shift']=$review['admin_proposal']['override_default_shift']??$proposal['override_default_shift'];
            $proposal=$this->validateProposal($proposal,$row->attendance_date);
            $review['employee_proposal']=$proposal;
            $review['employee_note']=trim((string)($input['note']??''));
            $review['employee_submitted_at']=now()->toDateTimeString();
            DB::table('attendance_import_rows')->where('id',$rowId)->update([
                'status'=>'employee_submitted','version'=>$row->version+1,'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
            ]);
        });
    }

    public function approveEmployeeDtr(string $batchId,int $rowId): array
    {
        $batch=$this->batch($batchId); $device=$this->device($batch->device_id);
        return DB::transaction(function()use($batchId,$rowId,$device){
            $activeBatch=DB::table('attendance_imports')->where('id',$batchId)->lockForUpdate()->first();
            $row=DB::table('attendance_import_rows')->where('id',$rowId)->where('import_id',$batchId)->lockForUpdate()->first();
            abort_unless($row && $row->status==='employee_submitted',409,'This interval has no employee submission awaiting approval.');
            if (($activeBatch->cutoff_start && $row->attendance_date<$activeBatch->cutoff_start)||($activeBatch->cutoff_end && $row->attendance_date>$activeBatch->cutoff_end)) {
                throw ValidationException::withMessages(['cutoff'=>'This date is outside the selected cutoff.']);
            }
            $review=json_decode($row->review,true)?:[];
            $proposal=$this->validateProposal($review['employee_proposal']??$review['admin_proposal'],$row->attendance_date);
            $employee=EmployeeModel::where('employee_id',$row->employee_idx)->where('branch_idx',$device->branch_idx)->lockForUpdate()->first();
            abort_unless($employee,422,'The DTR employee mapping is no longer valid.');
            $conflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)->lockForUpdate()->get();
            abort_unless(hash_equals($this->fingerprint($conflicts),$review['conflict_hash']??''),409,'Attendance changed while the employee was completing this DTR. Review the conflict in Employee Logs first.');
            $isOT=$proposal['overtime_status']==='Yes';
            $same=$conflicts->filter(fn($l)=>str_ends_with(strtolower($l->log_type),'ot')===$isOT);
            $target=0;
            if ($same->isNotEmpty()) {
                if (($review['conflict_action']??'keep')==='keep') {
                    DB::table('attendance_import_rows')->where('id',$rowId)->update(['status'=>'kept','version'=>$row->version+1,'updated_at'=>now()]);
                    return ['status'=>'kept'];
                }
                abort_unless($same->count()===1,409,'Multiple existing intervals need manual review in Employee Logs.');
                $target=$same->first()->employee_logs_id;
            }
            $request=Request::create('/attendance-import-approve','POST',array_merge($proposal,[
                'branch_idx'=>$device->branch_idx,'employee_idx'=>$employee->employee_id,'employee_logs_id'=>$target,'attendance_date'=>$row->attendance_date,
            ]));
            $response=app(AttendanceLogWriter::class)->save($request);
            if (!$response || !in_array($response->getData(true)['success']??'', ['Created','Updated'],true)) {
                throw ValidationException::withMessages(['attendance'=>'Could not finalize this DTR. No attendance changes were committed.']);
            }
            $log=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)
                ->when($target,fn($q)=>$q->where('employee_logs_id',$target))->orderByDesc('employee_logs_id')->first();
            $currentConflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)->get();
            $currentFingerprint=$this->fingerprint($currentConflicts);
            DB::table('attendance_import_rows')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)
                ->where('id','<>',$rowId)->whereIn('status',['employee_pending','employee_submitted'])->get(['id','review'])->each(function($pending)use($currentFingerprint){
                    $pendingReview=json_decode($pending->review,true)?:[];
                    $pendingReview['conflict_hash']=$currentFingerprint;
                    DB::table('attendance_import_rows')->where('id',$pending->id)->update(['review'=>json_encode($pendingReview,JSON_THROW_ON_ERROR),'updated_at'=>now()]);
                });
            $review['final_proposal']=$proposal; $review['approved_by_user_idx']=session('loginID'); $review['approved_at']=now()->toDateTimeString();
            DB::table('attendance_import_rows')->where('id',$rowId)->update([
                'saved_log_id'=>$log->employee_logs_id,'status'=>'saved','version'=>$row->version+1,
                'review'=>json_encode($review,JSON_THROW_ON_ERROR),'saved_by'=>session('loginID'),'saved_at'=>now(),'updated_at'=>now(),
            ]);
            return ['status'=>'saved','log_id'=>$log->employee_logs_id,'version'=>$row->version+1];
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
