<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->dropColumn(['decrypted_identity_card_url', 'decrypted_selfie_url']);
        });
    }

    public function down(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->text('decrypted_identity_card_url')->nullable()->after('decrypted_phone');
            $table->text('decrypted_selfie_url')->nullable()->after('decrypted_identity_card_url');
        });
    }
};
