<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privy_digital_id_sessions', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('reference_number', 255);
            $table->string('privy_session_id', 255)->nullable();
            $table->string('exchange_code', 255)->nullable();
            $table->text('session_url')->nullable();
            $table->string('status', 255)->default('pending');
            $table->string('event', 255)->nullable();
            $table->string('privy_id', 255)->nullable();
            $table->string('decrypted_privy_id', 255)->nullable();
            $table->string('decrypted_name', 255)->nullable();
            $table->string('decrypted_identity_number', 255)->nullable();
            $table->date('decrypted_dob')->nullable();
            $table->string('decrypted_email', 255)->nullable();
            $table->string('decrypted_phone', 255)->nullable();
            $table->string('local_ktp_path', 255)->nullable();
            $table->string('local_selfie_path', 255)->nullable();
            $table->longText('decrypted_raw')->nullable();
            $table->timestamp('decrypted_at')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('full_name', 255)->nullable();
            $table->date('dob')->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('ktp', 255)->nullable();
            $table->string('selfie', 255)->nullable();
            $table->string('processed_by', 255)->nullable();
            $table->integer('attempts')->unsigned()->default(0);
            $table->string('error_code', 50)->nullable();
            $table->string('error_message', 255)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('exchanged_at')->nullable();
            $table->timestamp('exchange_received_at')->nullable();
            $table->timestamp('callback_received_at')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('callback_state', 255)->nullable();
            $table->text('callback_token')->nullable();
            $table->longText('callback_shared_data')->nullable();
            $table->unique(['reference_number'], 'privy_digital_id_sessions_reference_number_unique');
            $table->unique(['exchange_code'], 'privy_digital_id_sessions_exchange_code_unique');
            $table->index(['user_id'], 'privy_digital_id_sessions_user_id_index');
            $table->index(['privy_session_id'], 'privy_digital_id_sessions_privy_session_id_index');
            $table->index(['status'], 'privy_digital_id_sessions_status_index');
            $table->index(['privy_id'], 'privy_digital_id_sessions_privy_id_index');
            $table->index(['expires_at'], 'privy_digital_id_sessions_expires_at_index');
            $table->index(['user_id', 'status', 'expires_at'], 'privy_digital_id_sessions_user_id_status_expires_at_index');
            $table->index(['decrypted_privy_id'], 'privy_digital_id_sessions_decrypted_privy_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privy_digital_id_sessions');
    }
};
