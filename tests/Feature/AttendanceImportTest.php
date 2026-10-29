<?php

namespace Tests\Feature;

use App\Services\Attendance\AttendanceFileParser;
use App\Services\Attendance\AttendanceImportService;
use App\Services\Attendance\AttendanceLogWriter;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AttendanceImportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['activitylog.enabled'=>false]);
        Storage::fake('local');
        Schema::create('teves_payroll_user_table',function(Blueprint $t){$t->increments('user_id');$t->string('user_branch_access_type');$t->string('user_real_name')->default('Tester');$t->string('user_type')->default('Admin');});
        Schema::create('teves_branch_table',function(Blueprint $t){$t->increments('branch_id');$t->string('branch_name');});
        Schema::create('teves_payroll_user_branch_access',function(Blueprint $t){$t->integer('user_idx');$t->integer('branch_idx');});
        Schema::create('teves_payroll_employee_table',function(Blueprint $t){
            $t->increments('employee_id');$t->integer('branch_idx');$t->integer('department_idx')->default(1);
            foreach(['employee_number','employee_full_name','employee_first_name','employee_last_name'] as $c)$t->string($c)->default('Employee');
            $t->double('employee_rate')->default(100);$t->string('employee_night_diff_pay')->default('No');
            $t->string('time_in')->default('08:00');$t->string('time_out')->default('17:00');$t->string('break_time_in')->default('12:00');$t->string('break_time_out')->default('13:00');
            $t->double('total_shift_hours')->default(9);$t->double('total_breaktime_hours')->default(1);
            foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)$t->integer('restday_'.$day)->default(0);
        });
        Schema::create('teves_payroll_employee_logs',function(Blueprint $t){
            $t->increments('employee_logs_id');$t->integer('employee_idx');$t->integer('branch_idx');$t->integer('department_idx');
            $t->date('attendance_date');$t->string('log_type');$t->string('override_shift');$t->double('current_rate');
            foreach(['log_in','log_out','breaktime_start','breaktime_end']as$c)$t->dateTime($c);
            foreach(['total_hours','total_regular_hours','total_regular_overtime_hours','total_restday_hours','total_restday_overtime_hours','total_breaktime_hours','total_tardiness_hours','total_undertime_hours','total_night_differential_hours','regular_pay','regular_overtime_pay','restday_pay','restday_overtime_pay','night_differential_pay','regular_holiday_pay','special_holiday_pay']as$c)$t->double($c)->default(0);
            $t->integer('created_by_user_idx')->nullable();$t->integer('updated_by_user_idx')->nullable();$t->timestamps();
        });
        Schema::create('teves_payroll_holiday_table',function(Blueprint $t){$t->increments('holiday_id');$t->date('holiday_date');$t->string('holiday_type');$t->string('holiday_description');});
        (require database_path('migrations/2026_10_27_000002_create_attendance_import_tables.php'))->up();
        (require database_path('migrations/2026_10_27_000003_add_attendance_import_cutoff.php'))->up();
        DB::table('teves_payroll_user_table')->insert(['user_id'=>1,'user_branch_access_type'=>'All']);
        DB::table('teves_branch_table')->insert([['branch_id'=>1,'branch_name'=>'Branch A'],['branch_id'=>2,'branch_name'=>'Branch B']]);
        DB::table('teves_payroll_employee_table')->insert([['employee_id'=>1,'branch_idx'=>1,'employee_number'=>'EMP1'],['employee_id'=>2,'branch_idx'=>2,'employee_number'=>'EMP2']]);
        DB::table('attendance_devices')->insert(['id'=>1,'branch_idx'=>1,'name'=>'Device A']);
        session(['loginID'=>1]);
    }

    private function csv(string $times='08:00,12:00,13:00,17:00',string $date='2026-10-19'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('export.csv',"employee_no,log_date,time_in,breaktime_start,breaktime_end,time_out\n00004,$date,$times\n");
    }

    private function ready(): array
    {
        $s=app(AttendanceImportService::class);$id=$s->upload($this->csv(),1);$s->mapEmployee($id,'00004',1);
        $row=$s->preview($id)['rows'][0];$p=$row['proposed'];$p['override_default_shift']='Yes';
        return [$s,$id,$row,['action'=>'save','version'=>$row['version'],'employee_idx'=>1,'confirmed'=>true,'proposed'=>$p,'conflict_hash'=>$row['conflict_hash'],'conflict_action'=>'keep']];
    }

    public function test_preview_and_mapping_do_not_write_payroll_logs(): void
    {
        [$s,$id,$row]=$this->ready();
        $this->assertSame('00004',$row['external_id']);$this->assertSame(1,$row['employee_idx']);
        $this->assertSame('2026-10-19T08:00',$row['proposed']['log_in']);
        $this->assertSame(0,DB::table('teves_payroll_employee_logs')->count());
        $this->assertSame(1,DB::table('attendance_employee_mappings')->count());
        $other=$s->upload($this->csv(),1);$this->assertSame(1,$s->preview($other)['rows'][0]['employee_idx']);
    }

    public function test_reviewed_import_uses_manual_computation_and_retries_are_idempotent(): void
    {
        [$s,$id,$row,$input]=$this->ready();
        $this->assertSame('saved',$s->save($id,$row['id'],$input)['status']);
        $log=DB::table('teves_payroll_employee_logs')->first();$this->assertEquals(8,$log->total_regular_hours);$this->assertEquals(800,$log->regular_pay);
        $this->assertTrue($s->save($id,$row['id'],$input)['already_saved']);$this->assertSame(1,DB::table('teves_payroll_employee_logs')->count());
        $this->assertSame($log->employee_logs_id,DB::table('attendance_import_rows')->value('saved_log_id'));
    }

    public function test_manual_and_imported_attendance_have_equal_computed_values(): void
    {
        [$s,$id,$row,$input]=$this->ready();
        $p=$s->validateProposal($input['proposed'],$row['date']);
        $request=Request::create('/manual','POST',array_merge($p,['employee_idx'=>1,'branch_idx'=>1,'employee_logs_id'=>0,'attendance_date'=>$row['date']]));
        app(\App\Http\Controllers\EmployeeLogsController::class)->submit_employee_regular_logs_information($request);
        $manual=(array)DB::table('teves_payroll_employee_logs')->first();DB::table('teves_payroll_employee_logs')->delete();
        $s->save($id,$row['id'],$input);$imported=(array)DB::table('teves_payroll_employee_logs')->first();
        foreach(['total_regular_hours','total_breaktime_hours','regular_pay','total_night_differential_hours','night_differential_pay','regular_holiday_pay','special_holiday_pay','log_type']as$key)$this->assertSame($manual[$key],$imported[$key],$key);
    }

    public function test_conflicts_require_a_fresh_snapshot_and_explicit_replacement(): void
    {
        [$s,$id,$row,$input]=$this->ready();$s->save($id,$row['id'],$input);
        $id2=$s->upload($this->csv(),1);$preview=$s->preview($id2)['rows'][0];
        $input['version']=1;$input['conflict_hash']=$preview['conflict_hash'];
        $this->assertSame('kept',$s->save($id2,$preview['id'],$input)['status']);
        $input['conflict_action']='replace';$s->save($id2,$preview['id'],$input);
        $this->assertSame(1,DB::table('teves_payroll_employee_logs')->count());
        $this->assertNotNull(json_decode(DB::table('attendance_import_rows')->where('id',$preview['id'])->value('review'),true)['replaced_log']);
    }

    public function test_changed_existing_logs_are_not_overwritten_from_a_stale_preview(): void
    {
        [$s,$id,$row,$input]=$this->ready();$s->save($id,$row['id'],$input);
        $id2=$s->upload($this->csv(),1);$r=$s->preview($id2)['rows'][0];$input['version']=1;$input['conflict_hash']=$r['conflict_hash'];$input['conflict_action']='replace';
        DB::table('teves_payroll_employee_logs')->update(['regular_pay'=>999]);
        try{$s->save($id2,$r['id'],$input);$this->fail('Expected conflict');}catch(\Symfony\Component\HttpKernel\Exception\HttpException $e){$this->assertSame(409,$e->getStatusCode());}
        $this->assertEquals(999,DB::table('teves_payroll_employee_logs')->value('regular_pay'));
    }

    public function test_missing_out_and_unknown_break_are_rejected_without_writing(): void
    {
        [$s,$id,$row,$input]=$this->ready();$input['proposed']['log_out']=null;
        try{$s->save($id,$row['id'],$input);$this->fail('Missing out accepted');}catch(ValidationException $e){$this->assertArrayHasKey('proposed.log_out',$e->errors());}
        $input['proposed']=$row['proposed'];$input['proposed']['break_policy']='review';
        try{$s->save($id,$row['id'],$input);$this->fail('Unknown break accepted');}catch(ValidationException $e){$this->assertArrayHasKey('proposed.break_policy',$e->errors());}
        $this->assertSame(0,DB::table('teves_payroll_employee_logs')->count());
    }

    public function test_overnight_dates_are_explicit_and_invalid_calendar_dates_are_rejected(): void
    {
        $s=app(AttendanceImportService::class);
        $p=['log_in'=>'2026-10-19T20:00','log_out'=>'2026-10-20T06:00','break_policy'=>'none','overtime_status'=>'No','override_default_shift'=>'Yes'];
        $v=$s->validateProposal($p,'2026-10-19');$this->assertSame('2026-10-20 06:00:00',$v['log_out']);
        $p['log_out']='2026-10-19T06:00';
        $this->expectException(ValidationException::class);$s->validateProposal($p,'2026-10-19');
    }

    public function test_branch_mapping_and_batch_ownership_are_enforced(): void
    {
        [$s,$id]=$this->ready();
        $this->postJson('/attendance-import/'.$id.'/mapping',['external_id'=>'00004','employee_idx'=>2])->assertStatus(422);
        DB::table('teves_payroll_user_table')->insert(['user_id'=>2,'user_branch_access_type'=>'All']);
        $this->withSession(['loginID'=>2])->getJson('/attendance-import/'.$id.'/preview')->assertNotFound();
        $this->withSession(['loginID'=>2])->get('/attendance-import/'.$id.'/source')->assertNotFound();
        DB::table('teves_payroll_user_table')->where('user_id',2)->update(['user_branch_access_type'=>'Selected']);
        $this->withSession(['loginID'=>2])->postJson('/attendance-import/devices',['branch_idx'=>1,'name'=>'Unauthorized'])->assertForbidden();
    }

    public function test_existing_uppercase_all_and_bysite_branch_permissions_are_respected(): void
    {
        $service=app(AttendanceImportService::class);
        foreach (['ALL','All','all'] as $access) {
            DB::table('teves_payroll_user_table')->where('user_id',1)->update(['user_branch_access_type'=>$access]);
            $this->assertSame([1,2],$service->branches()->pluck('branch_id')->all());
            $this->get('/attendance-import')->assertOk()->assertSee('<option value="1">Branch A</option>',false)->assertSee('<option value="2">Branch B</option>',false);
        }
        DB::table('teves_payroll_user_table')->where('user_id',1)->update(['user_branch_access_type'=>'BYSITE']);
        DB::table('teves_payroll_user_branch_access')->insert(['user_idx'=>1,'branch_idx'=>2]);
        $this->assertSame([2],$service->branches()->pluck('branch_id')->all());
        $this->get('/attendance-import')->assertOk()->assertDontSee('<option value="1">Branch A</option>',false)->assertSee('<option value="2">Branch B</option>',false);
        $this->postJson('/attendance-import/devices',['branch_idx'=>1,'name'=>'Denied'])->assertForbidden();
        DB::table('teves_payroll_user_table')->where('user_id',1)->update(['user_branch_access_type'=>'BYBRANCH']);
        DB::table('teves_payroll_user_branch_access')->where('user_idx',1)->delete();
        $this->get('/attendance-import')->assertOk()->assertSee('No branches are assigned to this account.')->assertSee('No assigned branches');
    }

    public function test_authentication_preview_routes_and_page_render(): void
    {
        $this->withSession(['loginID'=>null])->get('/attendance-import')->assertRedirect('/');
        $this->withSession(['loginID'=>1])->get('/attendance-import')->assertOk()->assertSee('DTR Plotting')->assertSee('Employee mapping');
        [$s,$id,$row,$input]=$this->ready();
        $this->postJson('/attendance-import/'.$id.'/rows/'.$row['id'],$input)->assertOk()->assertJson(['status'=>'saved']);
    }

    public function test_sunday_restday_is_used_and_multiple_night_saves_do_not_redeclare_constants(): void
    {
        DB::table('teves_payroll_employee_table')->where('employee_id',1)->update(['restday_sunday'=>1,'employee_night_diff_pay'=>'Yes']);
        $s=app(AttendanceImportService::class);
        foreach (['2026-10-18','2026-10-19'] as $date) {
            $id=$s->upload($this->csv('20:00,,,23:00',$date),1);$s->mapEmployee($id,'00004',1);$r=$s->preview($id)['rows'][0];
            $p=$r['proposed'];$p['break_policy']='none';$p['override_default_shift']='Yes';
            $s->save($id,$r['id'],['action'=>'save','version'=>1,'employee_idx'=>1,'confirmed'=>true,'proposed'=>$p,'conflict_hash'=>$r['conflict_hash']]);
        }
        $this->assertSame('RestDay',DB::table('teves_payroll_employee_logs')->where('attendance_date','2026-10-18')->value('log_type'));
        $this->assertSame(2,DB::table('teves_payroll_employee_logs')->count());
    }

    public function test_one_mapping_applies_to_every_employee_date_and_future_uploads_but_not_other_devices(): void
    {
        $s=app(AttendanceImportService::class);
        $file=UploadedFile::fake()->createWithContent('dtr.csv',"employee_no,log_date,time_in,breaktime_start,breaktime_end,time_out\n00004,2026-10-19,08:00,12:00,13:00,17:00\n00004,2026-10-20,08:05,12:00,13:00,17:00\n00005,2026-10-19,08:00,12:00,13:00,17:00\n");
        $id=$s->upload($file,1);$s->mapEmployee($id,'00004',1);
        $preview=$s->preview($id);
        $rows=collect($preview['rows'])->where('external_id','00004');
        $this->assertCount(2,$rows);
        foreach($rows as $row){$this->assertSame(1,$row['employee_idx']);$this->assertSame(1,$row['mapped_employee_idx']);}
        $this->assertNull(collect($preview['rows'])->firstWhere('external_id','00005')['employee_idx']);
        $this->assertSame('2026-10-19',$preview['batch']['period_start']);$this->assertSame('2026-10-20',$preview['batch']['period_end']);
        $future=$s->upload($this->csv(),1);$this->assertSame(1,$s->preview($future)['rows'][0]['mapped_employee_idx']);
        DB::table('attendance_devices')->insert(['id'=>2,'branch_idx'=>1,'name'=>'Other device']);
        $other=$s->upload($this->csv(),2);$this->assertNull($s->preview($other)['rows'][0]['mapped_employee_idx']);
    }

    public function test_regular_and_overtime_on_one_date_can_be_saved_from_one_review(): void
    {
        $s=app(AttendanceImportService::class);
        $file=UploadedFile::fake()->createWithContent('dtr.csv',"employee_no,log_date,time_in,breaktime_start,breaktime_end,time_out,time_in_ot,time_out_ot\n00004,2026-10-19,08:00,12:00,13:00,17:00,18:00,20:00\n");
        $id=$s->upload($file,1);$s->mapEmployee($id,'00004',1);$rows=$s->preview($id)['rows'];$this->assertCount(2,$rows);
        $hash=$rows[0]['conflict_hash'];
        foreach($rows as $r){
            $p=$r['proposed'];$p['override_default_shift']='Yes';
            $response=$s->save($id,$r['id'],['action'=>'save','version'=>1,'employee_idx'=>1,'confirmed'=>true,'proposed'=>$p,'conflict_hash'=>$hash,'conflict_action'=>'keep']);
            $this->assertSame('saved',$response['status']);$this->assertArrayHasKey('conflicts',$response);$hash=$response['conflict_hash'];
        }
        $this->assertSame(2,DB::table('teves_payroll_employee_logs')->count());
        $this->assertEquals(250,DB::table('teves_payroll_employee_logs')->where('log_type','RegularOT')->value('regular_overtime_pay'));
    }

    public function test_plotting_page_has_navigation_row_rules_and_employee_confirmation_before_save(): void
    {
        $response=$this->get('/attendance-import')->assertOk()->assertSee('DTR Plotting')->assertSee('Break treatment')->assertSee('Attendance type')->assertSee('Override default shift');
        $html=$response->getContent();
        $this->assertStringContainsString('href="http://localhost/attendance-import"',$html);
        $this->assertStringContainsString('id="dtr-export-employee"',$html);
        $this->assertStringContainsString('I reviewed the employee, punches, break, dates, and classification.',$html);
        $this->assertLessThan(strpos($html,'id="dtr-save-selected"'),strpos($html,'id="dtr-employee-confirm"'));
        $this->assertStringContainsString('js/attendance-dtr.js',$html);
        $this->assertStringContainsString('id="dtr-cutoff-start"',$html);
        $this->assertStringContainsString('id="dtr-cutoff-end"',$html);
    }

    public function test_cutoff_is_remembered_and_outside_dates_are_not_saved_or_removed(): void
    {
        $s=app(AttendanceImportService::class);
        $csv="employee_no,log_date,time_in,breaktime_start,breaktime_end,time_out\n00004,2026-10-19,08:00,12:00,13:00,17:00\n00004,2026-10-20,08:00,12:00,13:00,17:00\n00004,2026-10-21,08:00,12:00,13:00,17:00\n";
        $id=$s->upload(UploadedFile::fake()->createWithContent('cutoff.csv',$csv),1,['cutoff_start'=>'2026-10-20','cutoff_end'=>'2026-10-20']);
        $s->mapEmployee($id,'00004',1);$preview=$s->preview($id);
        $this->assertSame('2026-10-20',$preview['batch']['cutoff_start']);$this->assertSame('2026-10-20',$preview['batch']['cutoff_end']);
        $this->assertCount(3,$preview['rows']);
        foreach($preview['rows'] as $row){
            $p=$row['proposed'];$p['override_default_shift']='Yes';
            $input=['action'=>'save','version'=>1,'employee_idx'=>1,'confirmed'=>true,'proposed'=>$p,'conflict_hash'=>$row['conflict_hash']];
            if($row['date']==='2026-10-20'){$this->assertSame('saved',$s->save($id,$row['id'],$input)['status']);}
            else {try{$s->save($id,$row['id'],$input);$this->fail('Outside cutoff saved');}catch(ValidationException $e){$this->assertArrayHasKey('cutoff',$e->errors());}}
        }
        $this->assertSame(1,DB::table('teves_payroll_employee_logs')->count());
        $this->assertSame(3,DB::table('attendance_import_rows')->where('import_id',$id)->count());
        $this->postJson('/attendance-import/'.$id.'/cutoff',['cutoff_start'=>'2026-10-19','cutoff_end'=>'2026-10-21'])->assertOk();
        $this->assertSame('2026-10-19',$s->preview($id)['batch']['cutoff_start']);
        $this->assertSame(2,DB::table('attendance_import_rows')->where('import_id',$id)->where('status','pending')->count());
    }

    public function test_cutoff_end_includes_an_overnight_shift_ending_the_next_day(): void
    {
        $s=app(AttendanceImportService::class);
        $file=UploadedFile::fake()->createWithContent('night.csv',"employee_no,log_date,time_in,time_out\n00004,2026-10-20,2026-10-20 20:00,2026-10-21 06:00\n");
        $id=$s->upload($file,1,['cutoff_start'=>'2026-10-20','cutoff_end'=>'2026-10-20']);$s->mapEmployee($id,'00004',1);$r=$s->preview($id)['rows'][0];
        $p=$r['proposed'];$p['break_policy']='none';$p['override_default_shift']='Yes';
        $s->save($id,$r['id'],['action'=>'save','version'=>1,'employee_idx'=>1,'confirmed'=>true,'proposed'=>$p,'conflict_hash'=>$r['conflict_hash']]);
        $this->assertSame('2026-10-20',DB::table('teves_payroll_employee_logs')->value('attendance_date'));
        $this->assertSame('2026-10-21 06:00:00',DB::table('teves_payroll_employee_logs')->value('log_out'));
    }

    public function test_cutoff_validation_ownership_and_upload_requirement(): void
    {
        [$s,$id]=$this->ready();
        $this->postJson('/attendance-import/'.$id.'/cutoff',['cutoff_start'=>'2026-10-22','cutoff_end'=>'2026-10-19'])->assertUnprocessable();
        $this->postJson('/attendance-import/'.$id.'/cutoff',['cutoff_start'=>'2026-02-30','cutoff_end'=>'2026-03-10'])->assertUnprocessable();
        $this->postJson('/attendance-import/'.$id.'/cutoff',['cutoff_start'=>'2025-01-01','cutoff_end'=>'2026-12-31'])->assertUnprocessable();
        $this->postJson('/attendance-import/upload',['device_id'=>1,'file'=>$this->csv()])->assertUnprocessable()->assertJsonValidationErrors(['cutoff_start','cutoff_end']);
        DB::table('teves_payroll_user_table')->insert(['user_id'=>2,'user_branch_access_type'=>'ALL']);
        $this->withSession(['loginID'=>2])->postJson('/attendance-import/'.$id.'/cutoff',['cutoff_start'=>'2026-10-19','cutoff_end'=>'2026-10-20'])->assertNotFound();
    }
}
