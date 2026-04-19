<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->string('ros_registration_number')->nullable()->after('is_active');
            $table->date('established_date')->nullable()->after('ros_registration_number');
            $table->text('address')->nullable()->after('established_date');
            $table->string('postcode', 20)->nullable()->after('address');
            $table->string('city')->nullable()->after('postcode');
            $table->foreignId('state_id')->nullable()->after('city')->constrained('states')->nullOnDelete();
            $table->string('phone')->nullable()->after('state_id');
            $table->string('official_email')->nullable()->after('phone');
            $table->decimal('latitude', 10, 8)->nullable()->after('official_email');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('associations', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropColumn([
                'ros_registration_number',
                'established_date',
                'address',
                'postcode',
                'city',
                'state_id',
                'phone',
                'official_email',
                'latitude',
                'longitude',
            ]);
        });
    }
};
