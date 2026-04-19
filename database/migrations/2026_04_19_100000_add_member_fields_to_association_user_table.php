<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('association_user', function (Blueprint $table) {
            $table->text('address')->nullable()->after('is_active');
            $table->string('postcode', 20)->nullable()->after('address');
            $table->string('city')->nullable()->after('postcode');
            $table->foreignId('state_id')->nullable()->after('city')->constrained('states')->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable()->after('state_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('property_relationship')->nullable()->after('longitude');
            $table->boolean('is_voting_eligible')->default(false)->after('property_relationship');
        });
    }

    public function down(): void
    {
        Schema::table('association_user', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropColumn([
                'address',
                'postcode',
                'city',
                'state_id',
                'latitude',
                'longitude',
                'property_relationship',
                'is_voting_eligible',
            ]);
        });
    }
};
