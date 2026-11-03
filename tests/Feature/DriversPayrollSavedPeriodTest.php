<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DriversPayrollSavedPeriodTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['activitylog.enabled'=>false]);
        Schema::create('teves_payroll_user_table', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('user_type');
            $table->string('user_real_name');
            $table->unsignedInteger('employee_idx')->nullable();
        });
        Schema::create('teves_branch_table', function (Blueprint $table) {
            $table->increments('branch_id');
            $table->string('branch_name');
            $table->string('branch_code');
        });
        Schema::create('teves_payroll_employee_table', function (Blueprint $table) {
            $table->increments('employee_id');
            $table->unsignedInteger('branch_idx');
            $table->string('employee_number');
            $table->string('employee_full_name');
            $table->string('employment_type');
            $table->decimal('employee_rate')->default(13);
            $table->unsignedInteger('department_idx')->nullable();
        });
        Schema::create('teves_payroll_drivers_logs', function (Blueprint $table) {
            $table->increments('drivers_logs_id');
            $table->unsignedInteger('employee_idx');
            $table->unsignedInteger('branch_idx');
            $table->date('travel_date');
            $table->unsignedInteger('cutoff_idx')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('loading_terminal')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('volume')->nullable();
            $table->decimal('rate_per_liter')->nullable();
            $table->decimal('gross_amount')->nullable();
            $table->decimal('trip_pay')->nullable();
            $table->decimal('current_rate')->nullable();
            $table->timestamps();
        });
        Schema::create('driver_payrolls', function (Blueprint $table) {
            $table->increments('driver_payroll_id');
            $table->unsignedInteger('cutoff_idx')->nullable();
            $table->unsignedInteger('employee_idx');
            $table->unsignedInteger('branch_idx');
            $table->date('cutoff_start');
            $table->date('cutoff_end');
            $table->string('payroll_no')->nullable();
            $table->string('trip_reference');
            $table->date('payroll_date')->nullable();
            $table->decimal('rate_per_trip')->nullable();
            $table->decimal('total_volume')->default(0);
            $table->decimal('total_gross_amount')->default(0);
            $table->decimal('total_trip_pay');
            $table->unsignedInteger('reviewed_by_user_idx')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->unsignedInteger('created_by_user_idx')->nullable();
            $table->timestamps();
        });
        Schema::create('driver_payroll_items', function (Blueprint $table) {
            $table->increments('driver_payroll_item_id');
            $table->unsignedInteger('driver_payroll_id');
            $table->unsignedInteger('drivers_logs_id');
            $table->date('travel_date')->nullable();
            $table->decimal('current_rate')->nullable();
            $table->string('plate_number')->nullable();
            $table->string('loading_terminal')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('volume')->nullable();
            $table->decimal('rate_per_liter')->nullable();
            $table->decimal('gross_amount')->nullable();
            $table->decimal('trip_pay')->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_11_01_000001_create_driver_payroll_revocations.php'))->up();
        DB::table('teves_payroll_user_table')->insert(['user_id'=>1,'user_type'=>'Admin','user_real_name'=>'Admin']);
        DB::table('teves_payroll_user_table')->insert(['user_id'=>2,'user_type'=>'Supervisor','user_real_name'=>'Supervisor']);
        DB::table('teves_branch_table')->insert(['branch_id'=>1,'branch_name'=>'Branch One','branch_code'=>'BR1']);
        DB::table('teves_payroll_employee_table')->insert(['employee_id'=>7,'branch_idx'=>1,
            'employee_number'=>'007','employee_full_name'=>'Test Driver','employment_type'=>'Others']);
        DB::table('driver_payrolls')->insert([
            ['driver_payroll_id'=>1,'employee_idx'=>7,'branch_idx'=>1,'cutoff_start'=>'2026-10-17',
                'cutoff_end'=>'2026-10-23','payroll_no'=>'DP-OCT','trip_reference'=>'OCT','total_trip_pay'=>2184],
            ['driver_payroll_id'=>2,'employee_idx'=>7,'branch_idx'=>1,'cutoff_start'=>'2026-12-01',
                'cutoff_end'=>'2026-12-15','payroll_no'=>'DP-DEC','trip_reference'=>'DEC','total_trip_pay'=>6240],
        ]);
        DB::table('driver_payroll_items')->insert([
            ['driver_payroll_id'=>1,'drivers_logs_id'=>100],
            ['driver_payroll_id'=>2,'drivers_logs_id'=>101],
        ]);
        DB::table('teves_payroll_drivers_logs')->insert(['drivers_logs_id'=>100,'employee_idx'=>7,
            'branch_idx'=>1,'travel_date'=>'2026-10-29','plate_number'=>'PLATE','loading_terminal'=>'A',
            'destination'=>'B','volume'=>24000,'rate_per_liter'=>0.7,'gross_amount'=>16800,
            'trip_pay'=>2184,'current_rate'=>13]);
        DB::table('teves_payroll_drivers_logs')->insert(['drivers_logs_id'=>101,'employee_idx'=>7,
            'branch_idx'=>1,'travel_date'=>'2026-12-03','plate_number'=>'DEC-PLATE','loading_terminal'=>'A',
            'destination'=>'B','volume'=>24000,'rate_per_liter'=>2,'gross_amount'=>48000,
            'trip_pay'=>6240,'current_rate'=>13]);
    }

    public function test_saved_cutoff_inside_selected_period_is_visible_with_its_own_print_link(): void
    {
        $this->withSession(['loginID'=>1])->get('/drivers-payroll?employee_id=7&cutoff_start=2026-10-01&cutoff_end=2026-11-30&tab=saved')
            ->assertOk()->assertSee('DP-OCT')->assertSee('2026-10-17 to 2026-10-23')
            ->assertSee('2,184.00')->assertDontSee('DP-DEC')
            ->assertSee('cutoff_start=2026-10-17&amp;cutoff_end=2026-10-23',false);
    }

    public function test_saved_driver_trip_cannot_be_edited_deleted_or_recalculated(): void
    {
        $this->withSession(['loginID'=>1])->postJson('/submit_drivers_logs_information',[
            'drivers_logs_id'=>100,'branch_idx'=>1,'employee_idx'=>7,'travel_date'=>'2026-10-29',
            'plate_number'=>'CHANGED','loading_terminal'=>'A','destination'=>'B',
            'volume'=>24000,'rate_per_liter'=>0.7,
        ])->assertUnprocessable()->assertJsonValidationErrors('drivers_logs_id');
        $this->withSession(['loginID'=>1])->postJson('/delete_driver_log_confirmed',[
            'DriverlogsID'=>100,
        ])->assertStatus(409);
        $this->withSession(['loginID'=>1])->postJson('/drivers-logs/recalculate',[
            'drivers_logs_id'=>100,
        ])->assertStatus(409);
        $this->assertDatabaseHas('teves_payroll_drivers_logs',[
            'drivers_logs_id'=>100,'plate_number'=>'PLATE','trip_pay'=>2184,
        ]);
    }

    public function test_only_admin_can_revoke_latest_cutoff_and_original_trips_become_available_again(): void
    {
        $payload=['reason'=>'Correct the destination and review the trip.','confirmed'=>1];
        $this->withSession(['loginID'=>2])->postJson('/drivers-payroll/2/revoke',$payload)->assertForbidden();
        $this->withSession(['loginID'=>1])->postJson('/drivers-payroll/1/revoke',$payload)
            ->assertUnprocessable()->assertJsonValidationErrors('payroll');
        $this->assertDatabaseCount('driver_payroll_revocations',0);

        $this->withSession(['loginID'=>1])->postJson('/drivers-payroll/2/revoke',$payload)
            ->assertOk()->assertJsonPath('redirect',route('drivers_payroll.index',[
                'employee_id'=>7,'cutoff_start'=>'2026-12-01','cutoff_end'=>'2026-12-15','tab'=>'generate',
            ]));
        $this->assertDatabaseMissing('driver_payrolls',['driver_payroll_id'=>2]);
        $this->assertDatabaseMissing('driver_payroll_items',['drivers_logs_id'=>101]);
        $this->assertDatabaseHas('driver_payroll_revocations',[
            'original_driver_payroll_id'=>2,'payroll_no'=>'DP-DEC',
            'reason'=>'Correct the destination and review the trip.','revoked_by_user_idx'=>1,
        ]);
        $archive=DB::table('driver_payroll_revocations')->first();
        $this->assertSame(101,json_decode($archive->items_snapshot,true)[0]['drivers_logs_id']);
        $this->withSession(['loginID'=>1])->get('/drivers-payroll?employee_id=7&cutoff_start=2026-12-01&cutoff_end=2026-12-15')
            ->assertOk()->assertSee('DEC-PLATE')->assertSee('Save payroll');
        $this->assertDatabaseHas('driver_payroll_items',['drivers_logs_id'=>100]);
        $this->withSession(['loginID'=>1])->post('/drivers-payroll/7',[
            'cutoff_start'=>'2026-12-01','cutoff_end'=>'2026-12-15',
            'trip_reference'=>'DEC-REVISED','reviewed'=>1,
        ])->assertRedirect();
        $this->assertDatabaseHas('driver_payrolls',['employee_idx'=>7,'trip_reference'=>'DEC-REVISED']);
        $this->assertDatabaseHas('driver_payroll_items',['drivers_logs_id'=>101]);
    }

    public function test_driver_logs_saved_payrolls_tab_lists_cutoffs_and_latest_revoke_action(): void
    {
        $this->withSession(['loginID'=>1])->get('/drivers-attendance-logs')
            ->assertOk()->assertSee('Saved Driver Payrolls')->assertSee('DriversSavedPayrollsTable');
        $response=$this->withSession(['loginID'=>1])->withHeader('X-Requested-With','XMLHttpRequest')
            ->getJson('/drivers-logs/saved-payrolls?draw=1&start=0&length=10')->assertOk();
        $rows=collect($response->json('data'))->keyBy('payroll_no');
        $this->assertCount(2,$rows);
        $this->assertStringContainsString('Revoke',$rows['DP-DEC']['action']);
        $this->assertStringNotContainsString('Revoke',$rows['DP-OCT']['action']);
    }
}
