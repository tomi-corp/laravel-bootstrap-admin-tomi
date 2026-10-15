<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->string('local_ktp_path')->nullable()->after('decrypted_selfie_url');
            $table->string('local_selfie_path')->nullable()->after('local_ktp_path');
        });
    }

    public function down(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->dropColumn(['local_ktp_path', 'local_selfie_path']);
        });
    }
};