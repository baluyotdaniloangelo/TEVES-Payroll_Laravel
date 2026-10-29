<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmployeeTaxSettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['activitylog.enabled' => false]);
        Schema::create('teves_payroll_user_table', function (Blueprint $table) {
            $table->increments('user_id');
            $table->string('user_type');
            $table->integer('employee_idx')->nullable();
        });
        Schema::create('teves_branch_table', function (Blueprint $table) {
            $table->increments('branch_id'); $table->string('branch_code'); $table->string('branch_name');
        });
        Schema::create('teves_payroll_department_table', function (Blueprint $table) {
            $table->increments('department_id'); $table->string('department_name');
        });
        Schema::create('teves_payroll_employee_table', function (Blueprint $table) {
            $table->increments('employee_id');
            foreach (['employee_number','employee_last_name','employee_first_name','employee_middle_name','employee_extension_name','employee_full_name','employee_birthday','employee_position','employee_status','employee_night_diff_pay','employment_type','employee_picture','employee_phone','employee_email','time_in','break_time_in','break_time_out','time_out'] as $field) $table->string($field)->nullable();
            foreach (['branch_idx','department_idx','employee_rate','total_shift_hours','total_breaktime_hours','restday_monday','restday_tuesday','restday_wednesday','restday_thursday','restday_friday','restday_saturday','restday_sunday','created_by_user_idx','updated_by_user_idx'] as $field) $table->decimal($field, 12, 2)->nullable();
            $table->timestamps();
        });
        (require database_path('migrations/2026_10_29_000004_add_employee_tax_settings.php'))->up();
        DB::table('teves_payroll_user_table')->insert(['user_id'=>1, 'user_type'=>'Admin']);
        DB::table('teves_branch_table')->insert(['branch_id'=>1, 'branch_code'=>'B1', 'branch_name'=>'Branch']);
        DB::table('teves_payroll_department_table')->insert(['department_id'=>1, 'department_name'=>'Department']);
    }

    private function employeeData(): array
    {
        return ['employee_id'=>0, 'employee_number'=>'E01', 'employee_first_name'=>'Ana', 'employee_last_name'=>'Santos',
            'employee_birthday'=>'1990-01-01', 'employee_rate'=>100, 'branch_idx'=>1, 'department_idx'=>1,
            'time_in'=>'08:00', 'break_time_in'=>'12:00', 'break_time_out'=>'13:00', 'time_out'=>'17:00',
            'employee_tin'=>'123-456-789', 'employee_tax_status'=>'taxable'];
    }

    public function test_tax_details_are_saved_loaded_and_preserved_for_older_clients(): void
    {
        $payload = $this->employeeData();
        $this->withSession(['loginID'=>1])->postJson('/submit_employee_information', $payload)->assertOk()->assertJson(['success'=>'Created']);
        $id = DB::table('teves_payroll_employee_table')->value('employee_id');
        $this->withSession(['loginID'=>1])->postJson('/employee_info', ['employeeID'=>$id])->assertOk()
            ->assertJsonFragment(['employee_tin'=>'123-456-789', 'employee_tax_status'=>'taxable']);
        $payload['employee_id'] = $id;
        unset($payload['employee_tin'], $payload['employee_tax_status']);
        $this->withSession(['loginID'=>1])->postJson('/submit_employee_information', $payload)->assertOk()->assertJson(['success'=>'Updated']);
        $this->assertDatabaseHas('teves_payroll_employee_table', ['employee_id'=>$id, 'employee_tin'=>'123-456-789', 'employee_tax_status'=>'taxable']);
        $payload['employee_tin'] = ''; $payload['employee_tax_status'] = 'non_taxable';
        $this->withSession(['loginID'=>1])->postJson('/submit_employee_information', $payload)->assertOk();
        $this->assertDatabaseHas('teves_payroll_employee_table', ['employee_id'=>$id, 'employee_tin'=>null, 'employee_tax_status'=>'non_taxable']);
    }

    public function test_invalid_tax_settings_are_rejected(): void
    {
        $payload = $this->employeeData();
        $payload['employee_tin'] = 'invalid'; $payload['employee_tax_status'] = 'unknown';
        $this->withSession(['loginID'=>1])->postJson('/submit_employee_information', $payload)->assertUnprocessable()
            ->assertJsonValidationErrors(['employee_tin', 'employee_tax_status']);
        $this->assertDatabaseCount('teves_payroll_employee_table', 0);
    }
}
