<?php

namespace Tests\Feature;

use App\Mail\EmployeeDtrReviewMail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmployeeDtrReviewActionsTest extends TestCase
{
    private const BATCH = '751a28b3-7313-417a-9d63-e5e375469bb7';

    protected function setUp(): void
    {
        parent::setUp();
        config(['activitylog.enabled' => false]);
        Mail::fake();
        Schema::create('teves_payroll_user_table', function (Blueprint $table) {
            $table->increments('user_id');
            $table->unsignedInteger('employee_idx')->nullable();
            $table->string('user_type');
            $table->string('user_branch_access_type')->default('ALL');
            $table->string('user_email_address')->nullable();
        });
        Schema::create('teves_branch_table', function (Blueprint $table) {
            $table->increments('branch_id');
            $table->string('branch_code');
            $table->string('branch_name');
        });
        Schema::create('teves_payroll_employee_table', function (Blueprint $table) {
            $table->increments('employee_id');
            $table->unsignedInteger('branch_idx');
            $table->string('employee_full_name');
            $table->string('employee_email')->nullable();
        });
        Schema::create('teves_payroll_user_branch_access', function (Blueprint $table) {
            $table->unsignedInteger('user_idx');
            $table->unsignedInteger('branch_idx');
        });
        (require database_path('migrations/2026_10_27_000002_create_attendance_import_tables.php'))->up();
        (require database_path('migrations/2026_10_27_000003_add_attendance_import_cutoff.php'))->up();
        Schema::create('attendance_employee_review_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('import_id');
            $table->unsignedInteger('employee_idx');
            $table->unsignedInteger('user_idx');
            $table->string('recipient_email');
            $table->string('status');
            $table->timestamp('sent_at')->nullable();
            $table->text('failure_message')->nullable();
            $table->timestamps();
        });
        DB::table('teves_payroll_user_table')->insert([
            ['user_id' => 1, 'user_type' => 'Admin', 'employee_idx' => null,
                'user_email_address' => null],
            ['user_id' => 2, 'user_type' => 'Employee', 'employee_idx' => 10,
                'user_email_address' => 'employee@example.test'],
        ]);
        DB::table('teves_branch_table')->insert(['branch_id' => 1,
            'branch_code' => 'BR1', 'branch_name' => 'Branch One']);
        DB::table('teves_payroll_employee_table')->insert(['employee_id' => 10,
            'branch_idx' => 1, 'employee_full_name' => 'Own Employee',
            'employee_email' => 'employee@example.test']);
        DB::table('attendance_devices')->insert(['id' => 1, 'branch_idx' => 1, 'name' => 'Device']);
        DB::table('attendance_imports')->insert([
            'id' => self::BATCH, 'device_id' => 1, 'created_by' => 1,
            'filename' => 'report.xls', 'storage_path' => 'test/report.xls',
            'file_hash' => str_repeat('a', 64), 'format' => 'monthly',
            'cutoff_start' => '2026-10-01', 'cutoff_end' => '2026-10-15',
        ]);
        DB::table('attendance_import_rows')->insert([
            'id' => 1, 'import_id' => self::BATCH, 'external_id' => '10',
            'attendance_date' => '2026-10-10', 'raw' => '{}',
            'proposed' => '{}', 'warnings' => '[]', 'employee_idx' => 10,
            'status' => 'employee_submitted', 'version' => 2,
            'review' => json_encode(['employee_proposal' => [
                'log_in' => '2026-10-10T08:00', 'log_out' => '2026-10-10T17:00',
                'break_policy' => 'none', 'overtime_status' => 'No',
                'override_default_shift' => 'No',
            ]]),
        ]);
        session(['loginID' => 1]);
    }

    public function test_admin_can_return_submission_and_employee_receives_notice(): void
    {
        $this->postJson('/attendance-import/saved/rows/1/return',
            ['reason' => 'Please check your time out.'])
            ->assertOk()->assertJsonPath('email_sent', true);
        $row = DB::table('attendance_import_rows')->where('id', 1)->first();
        $this->assertSame('employee_pending', $row->status);
        $this->assertSame(3, $row->version);
        $this->assertSame('Please check your time out.', json_decode($row->review, true)['return_note']);
        Mail::assertSent(EmployeeDtrReviewMail::class, 1);
    }

    public function test_admin_can_resend_failed_notice_without_reimporting(): void
    {
        DB::table('attendance_import_rows')->where('id', 1)->update(['status' => 'employee_pending']);
        DB::table('attendance_employee_review_notifications')->insert([
            'import_id' => self::BATCH, 'employee_idx' => 10, 'user_idx' => 2,
            'recipient_email' => 'employee@example.test', 'status' => 'failed',
        ]);
        $this->postJson('/attendance-import/'.self::BATCH.'/resend/10')
            ->assertOk()->assertJsonPath('email_sent', true);
        $this->assertSame('sent', DB::table('attendance_employee_review_notifications')->value('status'));
        Mail::assertSent(EmployeeDtrReviewMail::class, 1);
    }

    public function test_employee_updates_show_their_saved_driver_cutoff(): void
    {
        Schema::create('driver_payrolls',function(Blueprint $table){
            $table->increments('driver_payroll_id');
            $table->unsignedInteger('employee_idx');
            $table->date('cutoff_start');
            $table->date('cutoff_end');
            $table->decimal('total_trip_pay');
        });
        DB::table('driver_payrolls')->insert([
            ['employee_idx'=>10,'cutoff_start'=>'2026-10-01','cutoff_end'=>'2026-10-15','total_trip_pay'=>260],
            ['employee_idx'=>20,'cutoff_start'=>'2026-10-01','cutoff_end'=>'2026-10-15','total_trip_pay'=>999],
        ]);
        $this->withSession(['loginID'=>2])->get('/my-updates')->assertOk()
            ->assertSee('Driver trips saved')->assertSee('260.00')->assertDontSee('999.00');
    }
}
