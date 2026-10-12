<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            if (! Schema::hasColumn('privy_digital_id_sessions', 'callback_state')) {
                $table->string('callback_state')->nullable()->after('error_message');
            }

            if (! Schema::hasColumn('privy_digital_id_sessions', 'callback_token')) {
                $table->text('callback_token')->nullable()->after('callback_state');
            }

            if (! Schema::hasColumn('privy_digital_id_sessions', 'callback_shared_data')) {
                $table->json('callback_shared_data')->nullable()->after('callback_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('privy_digital_id_sessions', function (Blueprint $table) {
            if (Schema::hasColumn('privy_digital_id_sessions', 'callback_shared_data')) {
                $table->dropColumn('callback_shared_data');
            }

            if (Schema::hasColumn('privy_digital_id_sessions', 'callback_token')) {
                $table->dropColumn('callback_token');
            }

            if (Schema::hasColumn('privy_digital_id_sessions', 'callback_state')) {
                $table->dropColumn('callback_state');
            }
        });
    }
};
