<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_histories', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->double('point');
            $table->longText('description');
            $table->enum('type', ['daily_checkin', 'ksp']);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['user_id'], 'point_histories_users_FK');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_histories');
    }
};
