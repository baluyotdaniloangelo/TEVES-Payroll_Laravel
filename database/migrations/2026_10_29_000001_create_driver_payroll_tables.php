<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_payrolls', function (Blueprint $table) {
            $table->increments('driver_payroll_id');
            $table->unsignedInteger('cutoff_idx');
            $table->unsignedInteger('employee_idx');
            $table->unsignedInteger('branch_idx');
            $table->string('payroll_no', 40)->nullable()->unique();
            $table->string('trip_reference', 100);
            $table->date('payroll_date');
            $table->decimal('rate_per_trip', 8, 2)->nullable();
            $table->decimal('total_volume', 14, 2)->default(0);
            $table->decimal('total_gross_amount', 14, 2)->default(0);
            $table->decimal('total_trip_pay', 14, 2)->default(0);
            $table->unsignedInteger('reviewed_by_user_idx')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->unsignedInteger('created_by_user_idx')->nullable();
            $table->timestamps();
            $table->unique(['cutoff_idx', 'employee_idx'], 'driver_payroll_cutoff_employee_unique');
            $table->index(['branch_idx', 'cutoff_idx']);
        });

        Schema::create('driver_payroll_items', function (Blueprint $table) {
            $table->increments('driver_payroll_item_id');
            $table->unsignedInteger('driver_payroll_id');
            $table->unsignedInteger('drivers_logs_id')->unique();
            $table->date('travel_date');
            $table->decimal('current_rate', 8, 2)->nullable();
            $table->string('plate_number')->nullable();
            $table->string('loading_terminal')->nullable();
            $table->string('destination')->nullable();
            $table->decimal('volume', 14, 2)->default(0);
            $table->decimal('rate_per_liter', 12, 4)->default(0);
            $table->decimal('gross_amount', 14, 2)->default(0);
            $table->decimal('trip_pay', 14, 2)->default(0);
            $table->timestamps();
            $table->index('driver_payroll_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_payroll_items');
        Schema::dropIfExists('driver_payrolls');
    }
};
