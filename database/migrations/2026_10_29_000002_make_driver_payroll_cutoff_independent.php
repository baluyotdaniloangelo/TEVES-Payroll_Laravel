<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('driver_payrolls', function (Blueprint $table) {
            $table->unsignedInteger('cutoff_idx')->nullable()->change();
            $table->date('cutoff_start')->nullable()->after('cutoff_idx');
            $table->date('cutoff_end')->nullable()->after('cutoff_start');
            $table->unique(['employee_idx', 'cutoff_start', 'cutoff_end'], 'driver_payroll_employee_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('driver_payrolls', function (Blueprint $table) {
            $table->dropUnique('driver_payroll_employee_period_unique');
            $table->dropColumn(['cutoff_start', 'cutoff_end']);
            // Keep cutoff_idx nullable so newer records are never invalidated on rollback.
        });
    }
};
