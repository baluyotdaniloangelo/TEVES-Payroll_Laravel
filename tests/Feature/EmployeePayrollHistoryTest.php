<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmployeePayrollHistoryTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['activitylog.enabled' => false]);
        Schema::create('teves_payroll_user_table', function (Blueprint $table) {
            $table->increments('user_id');
            $table->unsignedInteger('employee_idx')->nullable();
            $table->string('user_type');
            $table->string('user_real_name');
            $table->string('user_password')->nullable();
        });
        Schema::create('teves_branch_table', function (Blueprint $table) {
            $table->increments('branch_id');
            $table->string('branch_code');
            $table->string('branch_name')->nullable();
        });
        Schema::create('teves_payroll_employee_table', function (Blueprint $table) {
            $table->increments('employee_id');
            $table->unsignedInteger('branch_idx');
            $table->string('employee_full_name');
            $table->string('employee_number');
            $table->string('employee_position')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('employee_email')->nullable();
            $table->string('employee_phone')->nullable();
            $table->string('employee_tin')->nullable();
            $table->string('employee_tax_status')->nullable();
        });
        Schema::create('teves_payroll_cutoff_table', function (Blueprint $table) {
            $table->increments('cutoff_id');
            $table->date('cut_off_period_start');
            $table->date('cut_off_period_end');
        });
        Schema::create('teves_payroll_employee_salary_per_cut_off', function (Blueprint $table) {
            $table->increments('employee_salary_id');
            $table->unsignedInteger('employee_idx');
            $table->unsignedInteger('branch_idx');
            $table->unsignedInteger('cutoff_idx');
            $table->decimal('gross_salary');
            $table->decimal('allowance_amount_total');
            $table->decimal('deduction_amount_total');
            $table->decimal('net_salary');
        });
        Schema::create('driver_payrolls', function (Blueprint $table) {
            $table->increments('driver_payroll_id');
            $table->unsignedInteger('employee_idx');
            $table->unsignedInteger('branch_idx');
            $table->date('cutoff_start');
            $table->date('cutoff_end');
            $table->string('payroll_no');
            $table->string('trip_reference');
            $table->date('payroll_date');
            $table->decimal('total_volume');
            $table->decimal('total_gross_amount');
            $table->decimal('total_trip_pay');
        });
        Schema::create('driver_payroll_items', function (Blueprint $table) {
            $table->increments('driver_payroll_item_id');
            $table->unsignedInteger('driver_payroll_id');
            $table->date('travel_date');
            $table->string('plate_number');
            $table->string('loading_terminal');
            $table->string('destination');
            $table->decimal('trip_pay');
        });

        DB::table('teves_payroll_user_table')->insert([
            ['user_id' => 1, 'employee_idx' => 10, 'user_type' => 'Employee', 'user_real_name' => 'Own Employee', 'user_password' => Hash::make('old-password')],
            ['user_id' => 2, 'employee_idx' => 20, 'user_type' => 'Employee', 'user_real_name' => 'Other Employee', 'user_password' => Hash::make('other-password')],
        ]);
        DB::table('teves_branch_table')->insert(['branch_id' => 1, 'branch_code' => 'BR-1', 'branch_name' => 'Branch One']);
        DB::table('teves_payroll_employee_table')->insert([
            ['employee_id'=>10,'branch_idx'=>1,'employee_full_name'=>'Own Employee','employee_number'=>'OWN'],
            ['employee_id'=>20,'branch_idx'=>1,'employee_full_name'=>'Other Employee','employee_number'=>'OTHER'],
        ]);
        DB::table('teves_payroll_cutoff_table')->insert(['cutoff_id' => 1,
            'cut_off_period_start' => '2026-10-01', 'cut_off_period_end' => '2026-10-15']);
        DB::table('teves_payroll_employee_salary_per_cut_off')->insert([
            ['employee_idx' => 10, 'branch_idx' => 1, 'cutoff_idx' => 1,
                'gross_salary' => 1200, 'allowance_amount_total' => 100,
                'deduction_amount_total' => 50, 'net_salary' => 1250],
            ['employee_idx' => 20, 'branch_idx' => 1, 'cutoff_idx' => 1,
                'gross_salary' => 9000, 'allowance_amount_total' => 0,
                'deduction_amount_total' => 0, 'net_salary' => 9000],
        ]);
        DB::table('driver_payrolls')->insert([
            ['driver_payroll_id' => 1, 'employee_idx' => 10, 'branch_idx' => 1,
                'cutoff_start' => '2026-10-16', 'cutoff_end' => '2026-10-31',
                'payroll_no' => 'DP-OWN', 'trip_reference' => 'OWN-TRIP',
                'payroll_date' => '2026-10-31', 'total_volume' => 100,
                'total_gross_amount' => 200, 'total_trip_pay' => 26],
            ['driver_payroll_id' => 2, 'employee_idx' => 20, 'branch_idx' => 1,
                'cutoff_start' => '2026-10-16', 'cutoff_end' => '2026-10-31',
                'payroll_no' => 'DP-OTHER', 'trip_reference' => 'OTHER-TRIP',
                'payroll_date' => '2026-10-31', 'total_volume' => 100,
                'total_gross_amount' => 200, 'total_trip_pay' => 26],
        ]);
        DB::table('driver_payroll_items')->insert([
            ['driver_payroll_id' => 1, 'travel_date' => '2026-10-20',
                'plate_number' => 'OWN-PLATE', 'loading_terminal' => 'A',
                'destination' => 'B', 'trip_pay' => 26],
            ['driver_payroll_id' => 2, 'travel_date' => '2026-10-20',
                'plate_number' => 'OTHER-PLATE', 'loading_terminal' => 'A',
                'destination' => 'B', 'trip_pay' => 26],
        ]);
    }

    public function test_employee_sees_only_their_saved_cutoff_and_driver_records(): void
    {
        $this->withSession(['loginID' => 1])->get('/my-payroll')
            ->assertOk()
            ->assertSee('DP-OWN')
            ->assertSee('OWN-PLATE')
            ->assertSee('1,250.00')
            ->assertDontSee('DP-OTHER')
            ->assertDontSee('OTHER-PLATE')
            ->assertDontSee('9,000.00');
    }

    public function test_employee_cannot_open_admin_payroll_page(): void
    {
        $this->withSession(['loginID' => 1])->get('/create-payroll')->assertForbidden();
    }

    public function test_employee_profile_and_password_change_use_the_linked_account(): void
    {
        $this->withSession(['loginID'=>1])->get('/my-profile')->assertOk()
            ->assertSee('Own Employee')->assertDontSee('Other Employee');
        $this->withSession(['loginID'=>1])->post('/my-profile/password',[
            'current_password'=>'wrong-password','password'=>'new-password','password_confirmation'=>'new-password',
        ])->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('old-password',DB::table('teves_payroll_user_table')->where('user_id',1)->value('user_password')));
        $this->withSession(['loginID'=>1])->post('/my-profile/password',[
            'current_password'=>'old-password','password'=>'new-password','password_confirmation'=>'new-password',
        ])->assertRedirect('/my-profile');
        $this->assertTrue(Hash::check('new-password',DB::table('teves_payroll_user_table')->where('user_id',1)->value('user_password')));
        $this->assertTrue(Hash::check('other-password',DB::table('teves_payroll_user_table')->where('user_id',2)->value('user_password')));
    }
}
