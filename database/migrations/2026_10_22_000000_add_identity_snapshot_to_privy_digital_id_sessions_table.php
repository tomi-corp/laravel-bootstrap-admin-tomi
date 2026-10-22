<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->string('nik')->nullable()->after('user_id');
            $table->string('full_name')->nullable()->after('nik');
            $table->date('dob')->nullable()->after('full_name');
            $table->string('email')->nullable()->after('dob');
            $table->string('phone')->nullable()->after('email');
            $table->string('ktp')->nullable()->after('phone');
            $table->string('selfie')->nullable()->after('ktp');
        });
    }

    public function down(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->dropColumn(['nik', 'full_name', 'dob', 'email', 'phone', 'ktp', 'selfie']);
        });
    }
};
