<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $table = config('activitylog.table_name', 'activity_log');
        $schema = Schema::connection(config('activitylog.database_connection'));
        if (!$schema->hasTable($table)) {
            return;
        }
        if (!$schema->hasColumn($table, 'event')) {
            $schema->table($table, function (Blueprint $blueprint) {
                $blueprint->string('event')->nullable();
            });
        }
        if (!$schema->hasColumn($table, 'batch_uuid')) {
            $schema->table($table, function (Blueprint $blueprint) {
                $blueprint->uuid('batch_uuid')->nullable();
            });
        }
    }

    public function down(): void
    {
        // Preserve audit metadata and any columns that predated this upgrade.
    }
};
