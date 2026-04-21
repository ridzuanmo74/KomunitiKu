<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->string('frequency', 20)->default('yearly')->after('amount');
        });

        DB::table('fees')
            ->whereNotNull('due_day')
            ->update(['frequency' => 'monthly']);

        DB::table('fees')
            ->whereNull('due_day')
            ->update(['frequency' => 'yearly']);
    }

    public function down(): void
    {
        Schema::table('fees', function (Blueprint $table) {
            $table->dropColumn('frequency');
        });
    }
};
