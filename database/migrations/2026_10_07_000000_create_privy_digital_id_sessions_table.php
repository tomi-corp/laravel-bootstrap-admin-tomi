<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privy_digital_id_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->index();
            $table->string('reference_number')->unique();
            $table->string('privy_session_id')->nullable()->index();
            $table->string('exchange_code')->nullable()->unique();
            $table->text('session_url')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('event')->nullable();
            $table->string('privy_id')->nullable()->index();
            $table->string('processed_by')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->string('error_code', 50)->nullable();
            $table->string('error_message', 255)->nullable();
            $table->string('callback_state')->nullable();
            $table->text('callback_token')->nullable();
            $table->json('callback_shared_data')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('exchanged_at')->nullable();
            $table->timestamp('exchange_received_at')->nullable();
            $table->timestamp('callback_received_at')->nullable();
            $table->timestamp('processing_started_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privy_digital_id_sessions');
    }
};
