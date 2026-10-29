<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('teves_payroll_user_table', 'employee_idx')) {
            Schema::table('teves_payroll_user_table', function (Blueprint $table) {
                $table->unsignedInteger('employee_idx')->nullable()->unique();
            });
        }

        if (!Schema::hasTable('attendance_employee_review_notifications')) {
            Schema::create('attendance_employee_review_notifications', function (Blueprint $table) {
                $table->id();
                $table->uuid('import_id');
                $table->unsignedInteger('employee_idx');
                $table->unsignedInteger('user_idx');
                $table->string('recipient_email', 150);
                $table->string('status', 20)->default('pending');
                $table->timestamp('sent_at')->nullable();
                $table->text('failure_message')->nullable();
                $table->timestamps();
                $table->unique(['import_id', 'employee_idx'], 'attendance_review_notice_employee_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_employee_review_notifications');
        if (Schema::hasColumn('teves_payroll_user_table', 'employee_idx')) {
            Schema::table('teves_payroll_user_table', fn (Blueprint $table) => $table->dropUnique(['employee_idx'])->dropColumn('employee_idx'));
        }
    }
};
