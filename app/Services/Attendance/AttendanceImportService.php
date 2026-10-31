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
            'time_in','time_out','break_time_in','break_time_out',
        ])->map(fn($e)=>['id'=>$e->employee_id,'number'=>$e->employee_number,
            'name'=>$e->employee_full_name ?: trim($e->employee_first_name.' '.$e->employee_last_name),
            'time_in'=>$e->time_in,'time_out'=>$e->time_out,'break_time_in'=>$e->break_time_in,'break_time_out'=>$e->break_time_out]);
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
                $fallback=array_merge(json_decode($row->proposed,true)?:[],$review['proposal']??[]);
                $proposed=$row->status==='employee_pending'
                    ? ($review['admin_proposal']??$fallback)
                    : ($review['employee_proposal']??$review['admin_proposal']??$fallback);
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
                'employee_id','employee_full_name','employee_first_name','employee_last_name','employee_number','employee_email','branch_idx'
            ])->keyBy('employee_id');
            $accounts=DB::table('teves_payroll_user_table')->whereIn('employee_idx',$employeeIds)->where('user_type','Employee')
                ->get(['user_id','employee_idx','user_email_address'])->keyBy('employee_idx');
            $queuedCount=0;$autoApproved=0;$needsReview=0;$unmapped=0;$missingAccounts=[];$missingEmails=[];$groups=[];
            foreach($rows as $row){
                $employeeId=(int)($row->employee_idx?:($mappings[$row->external_id]??0));
                $employee=$employees->get($employeeId);$account=$accounts->get($employeeId);
                if(!$employee){$unmapped++;continue;}
                $review=json_decode($row->review,true)?:[];
                $proposal=array_merge(['break_policy'=>'review','overtime_status'=>'No','override_default_shift'=>'No'],
                    $review['admin_proposal']??(json_decode($row->proposed,true)?:[]));
                // A DTR explicitly returned for correction must be reviewed row by row.
                // Never repost the older admin proposal through the bulk save action.
                if ($row->status==='employee_pending' && !empty($review['allow_employee_correction'])) {
                    $needsReview++;
                    continue;
                }
                $conflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employeeId)
                    ->where('attendance_date',$row->attendance_date)->lockForUpdate()->get();
                $conflictHash=$this->fingerprint($conflicts);
                $isComplete=!empty($proposal['log_in']) && !empty($proposal['log_out'])
                    && (($proposal['break_policy']??'review')==='none'
                        || (($proposal['break_policy']??null)==='recorded' && !empty($proposal['breaktime_start']) && !empty($proposal['breaktime_end'])));

                if($isComplete){
                    try { $proposal=$this->validateProposal($proposal,$row->attendance_date); }
                    catch(ValidationException $exception){
                        $review['admin_proposal']=$proposal;$review['admin_issue']=$exception->getMessage();
                        DB::table('attendance_import_rows')->where('id',$row->id)->update([
                            'employee_idx'=>$employeeId,'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
                        ]);
                        $needsReview++;continue;
                    }
                    $isOT=$proposal['overtime_status']==='Yes';
                    $same=$conflicts->filter(fn($log)=>str_ends_with(strtolower($log->log_type),'ot')===$isOT);
                    if($same->isNotEmpty()){
                        $review['admin_proposal']=$proposal;$review['conflict_hash']=$conflictHash;
                        $review['conflict_action']='keep';$review['admin_issue']='Existing attendance found; payroll review is required.';
                        DB::table('attendance_import_rows')->where('id',$row->id)->update([
                            'employee_idx'=>$employeeId,'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
                        ]);
                        $needsReview++;continue;
                    }

                    $post=Request::create('/attendance-import-save','POST',array_merge($proposal,[
                        'branch_idx'=>$device->branch_idx,'employee_idx'=>$employeeId,'employee_logs_id'=>0,'attendance_date'=>$row->attendance_date,
                    ]));
                    $response=app(AttendanceLogWriter::class)->save($post);
                    if(!$response || !in_array($response->getData(true)['success']??'', ['Created','Updated'],true)){
                        $review['admin_proposal']=$proposal;$review['admin_issue']='Could not post this complete DTR. Review it in DTR Plotting.';
                        DB::table('attendance_import_rows')->where('id',$row->id)->update([
                            'employee_idx'=>$employeeId,'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
                        ]);
                        $needsReview++;continue;
                    }
                    $log=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employeeId)
                        ->where('attendance_date',$row->attendance_date)->orderByDesc('employee_logs_id')->first();
                    $review['admin_proposal']=$proposal;$review['final_proposal']=$proposal;
                    $review['conflict_hash']=$conflictHash;$review['conflict_action']='save';
                    $review['approved_by_user_idx']=session('loginID');$review['approved_at']=now()->toDateTimeString();
                    $review['allow_employee_correction']=false;
                    DB::table('attendance_import_rows')->where('id',$row->id)->update([
                        'employee_idx'=>$employeeId,'status'=>'saved','saved_log_id'=>$log->employee_logs_id,
                        'saved_by'=>session('loginID'),'saved_at'=>now(),'version'=>$row->version+1,
                        'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
                    ]);
                    $currentConflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employeeId)
                        ->where('attendance_date',$row->attendance_date)->get();
                    $currentFingerprint=$this->fingerprint($currentConflicts);
                    DB::table('attendance_import_rows')->where('employee_idx',$employeeId)->where('attendance_date',$row->attendance_date)
                        ->where('id','<>',$row->id)->whereIn('status',['pending','employee_pending','employee_submitted'])
                        ->get(['id','review'])->each(function($other)use($currentFingerprint){
                            $otherReview=json_decode($other->review,true)?:[];$otherReview['conflict_hash']=$currentFingerprint;
                            DB::table('attendance_import_rows')->where('id',$other->id)->update(['review'=>json_encode($otherReview,JSON_THROW_ON_ERROR),'updated_at'=>now()]);
                        });
                    $autoApproved++;continue;
                }

                $review['admin_proposal']=$proposal;$review['conflict_action']=$review['conflict_action']??'keep';
                $review['conflict_hash']=$conflictHash;
                $review['queued_by_user_idx']=$review['queued_by_user_idx']??session('loginID');
                $review['queued_at']=$review['queued_at']??now()->toDateTimeString();
                if($row->status==='pending'){
                    DB::table('attendance_import_rows')->where('id',$row->id)->update([
                        'employee_idx'=>$employeeId,'status'=>'employee_pending','version'=>$row->version+1,
                        'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
                    ]);
                    $queuedCount++;
                }

                $personName=$employee->employee_full_name?:trim($employee->employee_first_name.' '.$employee->employee_last_name);
                if(!$account){
                    $missingAccounts[$employeeId]=$employee->employee_number.' — '.$personName;
                    continue;
                }
                $email=filter_var(trim((string)$employee->employee_email),FILTER_VALIDATE_EMAIL)
                    ?trim((string)$employee->employee_email)
                    :(filter_var(trim((string)$account->user_email_address),FILTER_VALIDATE_EMAIL)?trim((string)$account->user_email_address):null);
                if(!$email){
                    $missingEmails[$employeeId]=$employee->employee_number.' — '.$personName;
                    continue;
                }
                if(!isset($groups[$employeeId]))$groups[$employeeId]=[
                    'employee_id'=>$employeeId,'user_id'=>$account->user_id,'email'=>$email,'name'=>$personName,
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
            return ['queued'=>$queuedCount,'auto_approved'=>$autoApproved,'needs_review'=>$needsReview,
                'unmapped'=>$unmapped,'missing_accounts'=>array_values($missingAccounts),'missing_emails'=>array_values($missingEmails),
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
        return DB::transaction(function()use($batchId,$rowId,$input,$device) {
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
            $review=json_decode($row->review,true)?:[];
            $review['admin_proposal']=$proposal;
            $review['conflict_action']=$input['conflict_action']??'keep';
            $review['conflict_hash']=$input['conflict_hash'];
            if ($target) $review['replaced_log']=$target;
            $review['reviewed_by_user_idx']=session('loginID');
            $review['reviewed_at']=now()->toDateTimeString();
            $request=Request::create('/attendance-import-approve','POST',array_merge($proposal,[
                'branch_idx'=>$device->branch_idx,'employee_idx'=>$employee->employee_id,'employee_logs_id'=>$target,'attendance_date'=>$row->attendance_date,
            ]));
            $response=app(AttendanceLogWriter::class)->save($request);
            if (!$response || !in_array($response->getData(true)['success']??'', ['Created','Updated'],true)) {
                throw ValidationException::withMessages(['attendance'=>'Could not approve and post this DTR to Employee Logs. No attendance changes were committed.']);
            }
            $log=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)
                ->when($target,fn($q)=>$q->where('employee_logs_id',$target))->orderByDesc('employee_logs_id')->first();
            $currentConflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)->get();
            $review['final_proposal']=$proposal;
            $review['approved_by_user_idx']=session('loginID');
            $review['approved_at']=now()->toDateTimeString();
            DB::table('attendance_import_rows')->where('id',$rowId)->update([
                'employee_idx'=>$employee->employee_id,'saved_log_id'=>$log->employee_logs_id,'saved_by'=>session('loginID'),
                'saved_at'=>now(),'status'=>'saved','version'=>$row->version+1,
                'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
            ]);
            DB::table('attendance_import_rows')->where('employee_idx',$employee->employee_id)->where('attendance_date',$row->attendance_date)
                ->where('id','<>',$rowId)->whereIn('status',['employee_pending','employee_submitted'])->get(['id','review'])->each(function($pending)use($currentConflicts){
                    $pendingReview=json_decode($pending->review,true)?:[];
                    $pendingReview['conflict_hash']=$this->fingerprint($currentConflicts);
                    DB::table('attendance_import_rows')->where('id',$pending->id)->update(['review'=>json_encode($pendingReview,JSON_THROW_ON_ERROR),'updated_at'=>now()]);
                });
            return ['status'=>'saved','log_id'=>$log->employee_logs_id,'version'=>$row->version+1,'admin_approved'=>true,
                'conflicts'=>$currentConflicts->map(fn($existing)=>$this->conflictView($existing))->all(),
                'conflict_hash'=>$this->fingerprint($currentConflicts)];
        });
    }

    public function employeeItems(int $employeeId)
    {
        $employee=EmployeeModel::where('employee_id',$employeeId)->first();
        return DB::table('attendance_import_rows as r')->join('attendance_imports as i','i.id','=','r.import_id')
            ->join('attendance_devices as d','d.id','=','i.device_id')->join('teves_branch_table as b','b.branch_id','=','d.branch_idx')
            ->where('r.employee_idx',$employeeId)->whereIn('r.status',['employee_pending','employee_submitted'])
            ->orderBy('i.cutoff_start')->orderBy('r.attendance_date')->get([
                'r.id','r.import_id','r.attendance_date','r.raw','r.proposed','r.review','r.warnings','r.status','r.version',
                'i.filename','i.cutoff_start','i.cutoff_end','b.branch_code','b.branch_name',
            ])->map(function($row) use ($employee) {
                $review=json_decode($row->review,true)?:[];
                $adminProposal=$review['admin_proposal']??(json_decode($row->proposed,true)?:[]);
                // A pending row must reflect the latest payroll review, not an older employee submission.
                $proposal=$row->status==='employee_submitted' ? ($review['employee_proposal']??$adminProposal) : $adminProposal;
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
                    'branch_code'=>$row->branch_code,'branch_name'=>$row->branch_name,'note'=>$review['employee_note']??null,'missing'=>$missing,
                    'work_hours'=>$this->payrollAdjustedHours($proposal,$row->attendance_date,$employee),
                    'allow_employee_correction'=>!empty($review['allow_employee_correction']),
                    'return_note'=>$review['return_note']??null,
                    'break_policy_editable'=>$row->status==='employee_pending' && $this->employeeCanChooseBreakPolicy($adminProposal)];
            });
    }

    private function payrollAdjustedHours(array $proposal,string $date,?EmployeeModel $employee): ?float
    {
        if (!$employee || empty($proposal['log_in']) || empty($proposal['log_out'])) return null;
        try {
            $in=$this->stamp($proposal['log_in'],'log_in');$out=$this->stamp($proposal['log_out'],'log_out');
            if($out<=$in)return null;
            $elapsed=($out->getTimestamp()-$in->getTimestamp())/3600;$breakHours=0.0;$earlyBreakReturn=0.0;
            if(($proposal['break_policy']??'review')==='review')return null;
            if(($proposal['break_policy']??null)==='recorded'){
                $breakStart=$this->stamp($proposal['breaktime_start']??null,'breaktime_start');
                $breakEnd=$this->stamp($proposal['breaktime_end']??null,'breaktime_end');
                if($breakStart<$in||$breakEnd<$breakStart||$breakEnd>$out)return null;
                $breakHours=($breakEnd->getTimestamp()-$breakStart->getTimestamp())/3600;
                if($employee->break_time_out && ($proposal['override_default_shift']??'No')!=='Yes'){
                    $scheduledBreakEnd=CarbonImmutable::parse($date.' '.$employee->break_time_out);
                    if($scheduledBreakEnd<$in)$scheduledBreakEnd=$scheduledBreakEnd->addDay();
                    $earlyBreakReturn=max(0,($scheduledBreakEnd->getTimestamp()-$breakEnd->getTimestamp())/3600);
                }
            }elseif(($proposal['break_policy']??null)!=='none')return null;
            $earlyStart=0.0;
            if($employee->time_in && ($proposal['override_default_shift']??'No')!=='Yes'){
                $scheduledStart=CarbonImmutable::parse($date.' '.$employee->time_in);
                $earlyStart=max(0,($scheduledStart->getTimestamp()-$in->getTimestamp())/3600);
            }
            $adjusted=max(0,$elapsed-$breakHours-$earlyStart-$earlyBreakReturn);
            return ($proposal['overtime_status']??'No')==='Yes'?$elapsed:min(8,$adjusted);
        } catch(\Throwable $exception) {
            return null;
        }
    }

    public function completePendingImportedDtr(int $rowId,array $input): array
    {
        $initial=DB::table('attendance_import_rows')->where('id',$rowId)->first();
        abort_unless($initial,404);
        $batch=$this->batch($initial->import_id);
        $device=$this->device($batch->device_id);

        return DB::transaction(function()use($rowId,$input,$batch,$device){
            $activeBatch=DB::table('attendance_imports')->where('id',$batch->id)->lockForUpdate()->first();
            $row=DB::table('attendance_import_rows')->where('id',$rowId)->where('import_id',$activeBatch->id)->lockForUpdate()->first();
            abort_unless($row && $row->status==='employee_pending',409,'This DTR is no longer awaiting employee completion.');
            abort_unless((int)$row->version===(int)$input['version'],409,'This DTR changed. Refresh Saved DTR Entries and try again.');
            if (($activeBatch->cutoff_start && $row->attendance_date<$activeBatch->cutoff_start)
                || ($activeBatch->cutoff_end && $row->attendance_date>$activeBatch->cutoff_end)) {
                throw ValidationException::withMessages(['cutoff'=>'This date is outside the import cutoff.']);
            }
            $employee=EmployeeModel::where('employee_id',$row->employee_idx)->where('branch_idx',$device->branch_idx)->lockForUpdate()->first();
            abort_unless($employee,409,'The mapped employee no longer belongs to this branch.');
            $review=json_decode($row->review,true)?:[];
            $previous=$review['admin_proposal']??(json_decode($row->proposed,true)?:[]);
            $proposal=[
                'log_in'=>$input['log_in'],
                'log_out'=>$input['log_out'],
                'break_policy'=>$input['break_policy'],
                'breaktime_start'=>$input['breaktime_start']??null,
                'breaktime_end'=>$input['breaktime_end']??null,
                'overtime_status'=>$previous['overtime_status']??'No',
                'override_default_shift'=>$input['override_default_shift'],
            ];
            $proposal=$this->validateProposal($proposal,$row->attendance_date);
            $conflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)
                ->where('attendance_date',$row->attendance_date)->lockForUpdate()->get();
            $expected=$review['conflict_hash']??null;
            if($expected && !hash_equals($this->fingerprint($conflicts),$expected)){
                throw ValidationException::withMessages(['attendance'=>'Attendance changed since this row was queued. Review the employee logs before posting.']);
            }
            $isOT=$proposal['overtime_status']==='Yes';
            if($conflicts->contains(fn($existing)=>str_ends_with(strtolower($existing->log_type),'ot')===$isOT)){
                throw ValidationException::withMessages(['attendance'=>'An attendance entry already exists for this date and type. Resolve it in Employee Logs first.']);
            }

            $post=Request::create('/attendance-import-admin-complete','POST',array_merge($proposal,[
                'branch_idx'=>$device->branch_idx,'employee_idx'=>$employee->employee_id,
                'employee_logs_id'=>0,'attendance_date'=>$row->attendance_date,
            ]));
            $response=app(AttendanceLogWriter::class)->save($post);
            if(!$response || !in_array($response->getData(true)['success']??'', ['Created','Updated'],true)){
                throw ValidationException::withMessages(['attendance'=>'Could not post this DTR. No changes were committed.']);
            }
            $log=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)
                ->where('attendance_date',$row->attendance_date)->where('log_in',$proposal['log_in'])
                ->where('log_out',$proposal['log_out'])->orderByDesc('employee_logs_id')->first();
            abort_unless($log,500,'The DTR was written but its attendance record could not be linked.');
            $current=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)
                ->where('attendance_date',$row->attendance_date)->get();
            $review['admin_proposal']=$proposal;$review['final_proposal']=$proposal;
            $review['conflict_hash']=$this->fingerprint($current);
            $review['admin_edited_by_user_idx']=session('loginID');$review['admin_edited_at']=now()->toDateTimeString();
            $review['approved_by_user_idx']=session('loginID');$review['approved_at']=now()->toDateTimeString();
            $review['allow_employee_correction']=false;
            DB::table('attendance_import_rows')->where('id',$row->id)->update([
                'status'=>'saved','saved_log_id'=>$log->employee_logs_id,'saved_by'=>session('loginID'),
                'saved_at'=>now(),'version'=>$row->version+1,'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
            ]);
            DB::table('attendance_import_rows')->where('employee_idx',$employee->employee_id)
                ->where('attendance_date',$row->attendance_date)->where('id','<>',$row->id)
                ->whereIn('status',['employee_pending','employee_submitted'])->get(['id','review'])
                ->each(function($pending)use($current){
                    $pendingReview=json_decode($pending->review,true)?:[];
                    $pendingReview['conflict_hash']=$this->fingerprint($current);
                    DB::table('attendance_import_rows')->where('id',$pending->id)
                        ->update(['review'=>json_encode($pendingReview,JSON_THROW_ON_ERROR),'updated_at'=>now()]);
                });
            return ['status'=>'saved','log_id'=>$log->employee_logs_id];
        });
    }

    public function updateSavedImportedDtr(int $logId,array $input): array
    {
        abort_unless(DB::getSchemaBuilder()->hasTable('attendance_import_rows'),404);
        $links=DB::table('attendance_import_rows')->where('saved_log_id',$logId)->where('status','saved')->get();
        abort_unless($links->count()===1,409,'This saved attendance log has no unique import link and cannot be edited here.');
        $linked=$links->first();

        return DB::transaction(function()use($logId,$linked,$input){
            $row=DB::table('attendance_import_rows')->where('id',$linked->id)->lockForUpdate()->first();
            abort_unless($row && $row->status==='saved' && (int)$row->saved_log_id===$logId,409,'This DTR is no longer posted.');
            $batch=DB::table('attendance_imports')->where('id',$row->import_id)->lockForUpdate()->first();
            abort_unless($batch,404);
            $device=DB::table('attendance_devices')->where('id',$batch->device_id)->first();
            abort_unless($device && $this->branches()->contains('branch_id',(int)$device->branch_idx),403);
            if (($batch->cutoff_start && $row->attendance_date<$batch->cutoff_start)
                || ($batch->cutoff_end && $row->attendance_date>$batch->cutoff_end)) {
                throw ValidationException::withMessages(['cutoff'=>'This DTR date is outside its cutoff. Review the import cutoff before editing.']);
            }

            $log=EmployeeLogsModel::where('employee_logs_id',$logId)->lockForUpdate()->first();
            abort_unless($log && (int)$log->employee_idx===(int)$row->employee_idx
                && $log->attendance_date===$row->attendance_date,409,'The linked attendance log could not be verified.');
            abort_unless($log->cutoff_idx===null,409,'This attendance is locked in a generated payroll cutoff.');
            $employee=EmployeeModel::where('employee_id',$row->employee_idx)
                ->where('branch_idx',$device->branch_idx)->lockForUpdate()->first();
            abort_unless($employee,409,'The saved DTR employee no longer belongs to its import branch.');

            $review=json_decode($row->review,true)?:[];
            $proposal=[
                'log_in'=>$input['log_in']??null,
                'log_out'=>$input['log_out']??null,
                'break_policy'=>$input['break_policy']??'',
                'breaktime_start'=>$input['breaktime_start']??null,
                'breaktime_end'=>$input['breaktime_end']??null,
                'overtime_status'=>str_ends_with(strtolower($log->log_type),'ot')?'Yes':'No',
                'override_default_shift'=>$input['override_default_shift'],
            ];
            $proposal=$this->validateProposal($proposal,$row->attendance_date);
            $isOT=$proposal['overtime_status']==='Yes';
            $conflicts=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)
                ->where('attendance_date',$row->attendance_date)->where('employee_logs_id','<>',$logId)->lockForUpdate()->get();
            $same=$conflicts->filter(fn($existing)=>str_ends_with(strtolower($existing->log_type),'ot')===$isOT);
            if($same->isNotEmpty()){
                throw ValidationException::withMessages(['log_in'=>'Another attendance entry already exists for this date and attendance type. Resolve it in Employee Logs before updating this DTR.']);
            }

            $request=Request::create('/attendance-import-saved-update','POST',array_merge($proposal,[
                'branch_idx'=>$device->branch_idx,'employee_idx'=>$employee->employee_id,
                'employee_logs_id'=>$logId,'attendance_date'=>$row->attendance_date,
            ]));
            $response=app(AttendanceLogWriter::class)->save($request);
            if(!$response || !in_array($response->getData(true)['success']??'', ['Created','Updated'],true)){
                throw ValidationException::withMessages(['attendance'=>'Could not recalculate this DTR. No changes were committed.']);
            }

            $current=DB::table('teves_payroll_employee_logs')->where('employee_idx',$employee->employee_id)
                ->where('attendance_date',$row->attendance_date)->get();
            $review['admin_proposal']=$proposal;
            $review['final_proposal']=$proposal;
            $review['conflict_hash']=$this->fingerprint($current);
            $review['admin_edited_by_user_idx']=session('loginID');
            $review['admin_edited_at']=now()->toDateTimeString();
            $review['approved_by_user_idx']=session('loginID');
            $review['approved_at']=now()->toDateTimeString();
            $review['allow_employee_correction']=false;
            DB::table('attendance_import_rows')->where('id',$row->id)->update([
                'version'=>$row->version+1,'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
            ]);
            DB::table('attendance_import_rows')->where('employee_idx',$employee->employee_id)
                ->where('attendance_date',$row->attendance_date)->where('id','<>',$row->id)
                ->whereIn('status',['employee_pending','employee_submitted'])->get(['id','review'])
                ->each(function($pending)use($current){
                    $pendingReview=json_decode($pending->review,true)?:[];
                    $pendingReview['conflict_hash']=$this->fingerprint($current);
                    DB::table('attendance_import_rows')->where('id',$pending->id)
                        ->update(['review'=>json_encode($pendingReview,JSON_THROW_ON_ERROR),'updated_at'=>now()]);
                });

            return ['status'=>'updated','log_id'=>$logId,'version'=>$row->version+1];
        });
    }

    public function returnPostedImportToEmployee(int $logId): array
    {
        $links=DB::table('attendance_import_rows')->where('saved_log_id',$logId)->get();
        abort_unless($links->count()===1,409,'This attendance log has no unique DTR import link and cannot be returned safely.');
        $rowMatch=$links->first();
        $batch=DB::table('attendance_imports')->where('id',$rowMatch->import_id)->first();
        abort_unless($batch,404);
        $device=DB::table('attendance_devices')->where('id',$batch->device_id)->first();
        abort_unless($device && $this->branches()->contains('branch_id',(int)$device->branch_idx),403);

        $returned=DB::transaction(function()use($rowMatch,$logId,$device,$batch) {
            $row=DB::table('attendance_import_rows')->where('id',$rowMatch->id)->lockForUpdate()->first();
            abort_unless($row && $row->status==='saved' && (int)$row->saved_log_id===$logId,409,'This imported DTR is no longer posted.');
            $log=EmployeeLogsModel::where('employee_logs_id',$logId)->lockForUpdate()->first();
            abort_unless($log && (int)$log->employee_idx===(int)$row->employee_idx
                && $log->attendance_date===$row->attendance_date,409,'The linked attendance log could not be verified.');
            abort_unless($log->cutoff_idx===null,409,'This attendance is locked in a generated payroll cutoff. Correct it through payroll adjustment instead.');

            $log->delete();
            $review=json_decode($row->review,true)?:[];
            $review['allow_employee_correction']=true;
            $review['return_note']='Payroll returned this posted DTR for correction. Please check and correct the punches, then submit it again.';
            $review['returned_log_id']=$logId;
            $review['returned_by_user_idx']=session('loginID');
            $review['returned_at']=now()->toDateTimeString();
            $remaining=DB::table('teves_payroll_employee_logs')->where('employee_idx',$row->employee_idx)
                ->where('attendance_date',$row->attendance_date)->get();
            $review['conflict_hash']=$this->fingerprint($remaining);
            DB::table('attendance_import_rows')->where('id',$row->id)->update([
                'status'=>'employee_pending','saved_log_id'=>null,'saved_by'=>null,'saved_at'=>null,
                'version'=>$row->version+1,'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
            ]);
            DB::table('attendance_import_rows')->where('employee_idx',$row->employee_idx)->where('attendance_date',$row->attendance_date)
                ->where('id','<>',$row->id)->whereIn('status',['employee_pending','employee_submitted'])->get(['id','review'])->each(function($pending)use($remaining){
                    $pendingReview=json_decode($pending->review,true)?:[];
                    $pendingReview['conflict_hash']=$this->fingerprint($remaining);
                    DB::table('attendance_import_rows')->where('id',$pending->id)->update(['review'=>json_encode($pendingReview,JSON_THROW_ON_ERROR),'updated_at'=>now()]);
                });

            $employee=EmployeeModel::where('employee_id',$row->employee_idx)->where('branch_idx',$device->branch_idx)->first();
            $account=DB::table('teves_payroll_user_table')->where('employee_idx',$row->employee_idx)->where('user_type','Employee')->first(['user_id','user_email_address']);
            $email=filter_var(trim((string)($employee->employee_email??'')),FILTER_VALIDATE_EMAIL)
                ?trim((string)$employee->employee_email)
                :(filter_var(trim((string)($account->user_email_address??'')),FILTER_VALIDATE_EMAIL)?trim((string)$account->user_email_address):null);
            if ($account && $email && DB::getSchemaBuilder()->hasTable('attendance_employee_review_notifications')) {
                $notice=DB::table('attendance_employee_review_notifications')->where('import_id',$row->import_id)->where('employee_idx',$row->employee_idx)->first();
                $values=['user_idx'=>$account->user_id,'recipient_email'=>$email,'status'=>'pending','sent_at'=>null,'failure_message'=>null,'updated_at'=>now()];
                if ($notice) DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->update($values);
                else DB::table('attendance_employee_review_notifications')->insert($values+['import_id'=>$row->import_id,'employee_idx'=>$row->employee_idx,'created_at'=>now()]);
            }
            return ['import_id'=>$row->import_id,'employee_id'=>$row->employee_idx,'user_id'=>$account->user_id??null,
                'email'=>$email,'name'=>$employee->employee_full_name??$row->external_name,
                'cutoff_start'=>$batch->cutoff_start,'cutoff_end'=>$batch->cutoff_end];
        });

        $result=['status'=>'returned','email_sent'=>false];
        if ($returned['user_id'] && $returned['email'] && DB::getSchemaBuilder()->hasTable('attendance_employee_review_notifications')) {
            $notice=DB::table('attendance_employee_review_notifications')->where('import_id',$returned['import_id'])->where('employee_idx',$returned['employee_id'])->first();
            try {
                Mail::to($returned['email'])->send(new EmployeeDtrReviewMail(
                    $returned['name'],$returned['cutoff_start'],$returned['cutoff_end'],route('my-dtr.index',['batch'=>$returned['import_id']])
                ));
                if ($notice) DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->update([
                    'status'=>'sent','sent_at'=>now(),'failure_message'=>null,'updated_at'=>now(),
                ]);
                $result['email_sent']=true;
            } catch (\Throwable $exception) {
                Log::warning('Returned employee DTR correction email failed.',[
                    'import_id'=>$returned['import_id'],'employee_idx'=>$returned['employee_id'],'user_id'=>$returned['user_id'],
                ]);
                if ($notice) DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->update([
                    'status'=>'failed','failure_message'=>'Mail transport failed. Review Payroll mail settings and retry.','updated_at'=>now(),
                ]);
                $result['email_message']='The DTR was returned to My DTR, but the email notification could not be sent.';
            }
        }
        return $result;
    }

    private function employeeCanChooseBreakPolicy(array $adminProposal): bool
    {
        $policy=$adminProposal['break_policy']??'review';
        return $policy==='review' || ($policy==='recorded' && empty($adminProposal['breaktime_start']) && empty($adminProposal['breaktime_end']));
    }

    public function submitEmployeeDtr(int $employeeId,int $rowId,array $input): void
    {
        DB::transaction(function()use($employeeId,$rowId,$input){
            $row=DB::table('attendance_import_rows')->where('id',$rowId)->where('employee_idx',$employeeId)->lockForUpdate()->first();
            abort_unless($row,404);
            abort_unless($row->status==='employee_pending',409,'This DTR is no longer awaiting employee completion.');
            abort_unless((int)$row->version===(int)$input['version'],409,'This DTR changed. Refresh the page and review again.');
            $review=json_decode($row->review,true)?:[];
            $adminProposal=$review['admin_proposal']??(json_decode($row->proposed,true)?:[]);
            $employeeFields=array_intersect_key($input['proposed'],array_flip(['log_in','log_out','break_policy','breaktime_start','breaktime_end']));
            $allowCorrection=!empty($review['allow_employee_correction']);
            if (!$allowCorrection) {
                foreach(['log_in','log_out','breaktime_start','breaktime_end'] as $field){
                    if(!empty($adminProposal[$field]))$employeeFields[$field]=$adminProposal[$field];
                }
            }
            if(!$allowCorrection && !$this->employeeCanChooseBreakPolicy($adminProposal))$employeeFields['break_policy']=$adminProposal['break_policy']??'review';
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
        $batch=DB::table('attendance_imports')->where('id',$batchId)->first();
        abort_unless($batch,404);
        $device=$this->device($batch->device_id);
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
            $review['allow_employee_correction']=false;
            DB::table('attendance_import_rows')->where('id',$rowId)->update([
                'saved_log_id'=>$log->employee_logs_id,'status'=>'saved','version'=>$row->version+1,
                'review'=>json_encode($review,JSON_THROW_ON_ERROR),'saved_by'=>session('loginID'),'saved_at'=>now(),'updated_at'=>now(),
            ]);
            return ['status'=>'saved','log_id'=>$log->employee_logs_id,'version'=>$row->version+1];
        });
    }

    public function returnSubmittedDtrToEmployee(int $rowId,string $reason): array
    {
        $row=DB::transaction(function()use($rowId,$reason){
            $row=DB::table('attendance_import_rows')->where('id',$rowId)->lockForUpdate()->first();
            abort_unless($row && $row->status==='employee_submitted',409,'This DTR is no longer awaiting payroll review.');
            $batch=DB::table('attendance_imports')->where('id',$row->import_id)->first();
            abort_unless($batch,404);
            $this->device($batch->device_id);
            $review=json_decode($row->review,true)?:[];
            $review['admin_proposal']=$review['employee_proposal']??$review['admin_proposal']??[];
            $review['allow_employee_correction']=true;
            $review['return_note']=$reason;
            $review['returned_by_user_idx']=session('loginID');
            $review['returned_at']=now()->toDateTimeString();
            DB::table('attendance_import_rows')->where('id',$rowId)->update([
                'status'=>'employee_pending','version'=>$row->version+1,
                'review'=>json_encode($review,JSON_THROW_ON_ERROR),'updated_at'=>now(),
            ]);
            return $row;
        });
        try{
            return $this->resendEmployeeDtrNotice($row->import_id,(int)$row->employee_idx);
        }catch(\Symfony\Component\HttpKernel\Exception\HttpExceptionInterface $exception){
            return ['email_sent'=>false,'message'=>'DTR returned to My DTR. Add or fix the Employee account/email, then resend the notification.'];
        }
    }

    public function resendEmployeeDtrNotice(string $batchId,int $employeeId): array
    {
        $batch=DB::table('attendance_imports')->where('id',$batchId)->first();
        abort_unless($batch,404);
        $device=$this->device($batch->device_id);
        abort_unless(DB::table('attendance_import_rows')->where('import_id',$batchId)
            ->where('employee_idx',$employeeId)->where('status','employee_pending')->exists(),
            409,'No DTR is awaiting this employee in the selected import.');
        $employee=EmployeeModel::where('employee_id',$employeeId)->where('branch_idx',$device->branch_idx)->first();
        $account=DB::table('teves_payroll_user_table')->where('employee_idx',$employeeId)
            ->where('user_type','Employee')->first(['user_id','user_email_address']);
        abort_unless($employee && $account,422,'Link an Employee account before sending the DTR email.');
        $email=filter_var(trim((string)$employee->employee_email),FILTER_VALIDATE_EMAIL)
            ?trim((string)$employee->employee_email)
            :(filter_var(trim((string)$account->user_email_address),FILTER_VALIDATE_EMAIL)
                ?trim((string)$account->user_email_address):null);
        abort_unless($email,422,'Add a valid employee email address before sending the DTR email.');
        abort_unless(DB::getSchemaBuilder()->hasTable('attendance_employee_review_notifications'),422,
            'Run the employee DTR notifications migration before sending email.');
        $notice=DB::table('attendance_employee_review_notifications')
            ->where('import_id',$batchId)->where('employee_idx',$employeeId)->first();
        $values=['user_idx'=>$account->user_id,'recipient_email'=>$email,'status'=>'pending',
            'failure_message'=>null,'updated_at'=>now()];
        if($notice){
            DB::table('attendance_employee_review_notifications')->where('id',$notice->id)->update($values);
            $noticeId=$notice->id;
        }else{
            $noticeId=DB::table('attendance_employee_review_notifications')->insertGetId(
                $values+['import_id'=>$batchId,'employee_idx'=>$employeeId,'created_at'=>now()]);
        }
        try{
            Mail::to($email)->send(new EmployeeDtrReviewMail(
                $employee->employee_full_name,$batch->cutoff_start,$batch->cutoff_end,route('my-dtr.index')
            ));
            DB::table('attendance_employee_review_notifications')->where('id',$noticeId)->update([
                'status'=>'sent','sent_at'=>now(),'failure_message'=>null,'updated_at'=>now(),
            ]);
            return ['email_sent'=>true,'message'=>'DTR email sent to the employee.'];
        }catch(\Throwable $exception){
            Log::warning('Employee DTR retry email failed.',[
                'import_id'=>$batchId,'employee_idx'=>$employeeId,
            ]);
            DB::table('attendance_employee_review_notifications')->where('id',$noticeId)->update([
                'status'=>'failed','failure_message'=>'Mail transport failed. Review Payroll mail settings and retry.',
                'updated_at'=>now(),
            ]);
            return ['email_sent'=>false,'message'=>'DTR remains available in My DTR, but the email could not be sent.'];
        }
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
