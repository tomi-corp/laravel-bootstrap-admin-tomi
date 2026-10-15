<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->string('decrypted_privy_id')->nullable()->index()->after('privy_id');
            $table->string('decrypted_name')->nullable()->after('decrypted_privy_id');
            $table->string('decrypted_identity_number')->nullable()->after('decrypted_name');
            $table->date('decrypted_dob')->nullable()->after('decrypted_identity_number');
            $table->string('decrypted_email')->nullable()->after('decrypted_dob');
            $table->string('decrypted_phone')->nullable()->after('decrypted_email');
            $table->text('decrypted_identity_card_url')->nullable()->after('decrypted_phone');
            $table->text('decrypted_selfie_url')->nullable()->after('decrypted_identity_card_url');
            $table->json('decrypted_raw')->nullable()->after('decrypted_selfie_url');
            $table->timestamp('decrypted_at')->nullable()->after('decrypted_raw');
        });
    }

    public function down(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'decrypted_privy_id',
                'decrypted_name',
                'decrypted_identity_number',
                'decrypted_dob',
                'decrypted_email',
                'decrypted_phone',
                'decrypted_identity_card_url',
                'decrypted_selfie_url',
                'decrypted_raw',
                'decrypted_at',
            ]);
        });
    }
};