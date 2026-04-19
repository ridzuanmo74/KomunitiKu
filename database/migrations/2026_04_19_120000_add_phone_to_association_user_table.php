<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('association_user', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('is_voting_eligible');
        });
    }

    public function down(): void
    {
        Schema::table('association_user', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
