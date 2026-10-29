<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendance_imports',function(Blueprint $t){
            $t->date('cutoff_start')->nullable();
            $t->date('cutoff_end')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('attendance_imports',fn(Blueprint $t)=>$t->dropColumn(['cutoff_start','cutoff_end']));
    }
};
