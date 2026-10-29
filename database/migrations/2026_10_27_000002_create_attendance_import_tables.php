<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('attendance_devices', function (Blueprint $t) {
            $t->id(); $t->unsignedInteger('branch_idx')->index(); $t->string('name',100);
            $t->timestamps(); $t->unique(['branch_idx','name']);
        });
        Schema::create('attendance_employee_mappings', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('device_id'); $t->string('external_id',100);
            $t->unsignedInteger('employee_idx'); $t->unsignedInteger('updated_by'); $t->timestamps();
            $t->unique(['device_id','external_id']);
        });
        Schema::create('attendance_imports', function (Blueprint $t) {
            $t->uuid('id')->primary(); $t->unsignedBigInteger('device_id')->index(); $t->unsignedInteger('created_by')->index();
            $t->string('filename'); $t->string('storage_path'); $t->string('file_hash',64)->index();
            $t->string('format',50); $t->timestamps();
        });
        Schema::create('attendance_import_rows', function (Blueprint $t) {
            $t->id(); $t->uuid('import_id')->index(); $t->string('external_id',100); $t->string('external_name')->nullable();
            $t->date('attendance_date'); $t->json('raw'); $t->json('proposed'); $t->json('warnings');
            $t->unsignedInteger('employee_idx')->nullable(); $t->string('status',20)->default('pending');
            $t->unsignedInteger('saved_log_id')->nullable()->index(); $t->unsignedInteger('saved_by')->nullable();
            $t->unsignedInteger('version')->default(1); $t->json('review')->nullable(); $t->timestamp('saved_at')->nullable();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        foreach (['attendance_import_rows','attendance_imports','attendance_employee_mappings','attendance_devices'] as $name) Schema::dropIfExists($name);
    }
};
