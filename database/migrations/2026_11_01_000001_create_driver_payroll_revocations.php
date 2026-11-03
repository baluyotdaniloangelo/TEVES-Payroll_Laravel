<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_payroll_revocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('original_driver_payroll_id')->unique();
            $table->unsignedInteger('employee_idx');
            $table->unsignedInteger('branch_idx');
            $table->string('payroll_no', 40)->nullable();
            $table->date('cutoff_start')->nullable();
            $table->date('cutoff_end')->nullable();
            $table->json('payroll_snapshot');
            $table->json('items_snapshot');
            $table->string('reason', 500);
            $table->unsignedInteger('revoked_by_user_idx');
            $table->timestamps();
            $table->index(['employee_idx', 'cutoff_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_payroll_revocations');
    }
};
