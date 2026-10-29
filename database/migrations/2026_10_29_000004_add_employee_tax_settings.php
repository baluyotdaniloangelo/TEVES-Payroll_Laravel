<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('teves_payroll_employee_table', function (Blueprint $table) {
            $table->string('employee_tin', 30)->nullable();
            $table->string('employee_tax_status', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('teves_payroll_employee_table', function (Blueprint $table) {
            $table->dropColumn(['employee_tin', 'employee_tax_status']);
        });
    }
};
