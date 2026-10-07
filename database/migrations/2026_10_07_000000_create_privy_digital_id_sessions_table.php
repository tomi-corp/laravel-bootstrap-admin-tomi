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
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('exchanged_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('privy_digital_id_sessions');
    }
};
