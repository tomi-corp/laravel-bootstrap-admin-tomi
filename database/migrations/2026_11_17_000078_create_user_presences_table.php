<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_presences', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('company_id')->unsigned();
            $table->bigInteger('employee_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->date('presence_date')->nullable();
            $table->timestamp('clock_in_at')->nullable();
            $table->decimal('clock_in_latitude', 10, 7)->nullable();
            $table->decimal('clock_in_longitude', 10, 7)->nullable();
            $table->text('clock_in_address')->nullable();
            $table->string('clock_in_image', 255)->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->decimal('clock_out_latitude', 10, 7)->nullable();
            $table->decimal('clock_out_longitude', 10, 7)->nullable();
            $table->text('clock_out_address')->nullable();
            $table->string('clock_out_image', 255)->nullable();
            $table->string('status', 255)->default('present');
            $table->string('approval_status', 255)->default('approved');
            $table->bigInteger('approved_by')->unsigned()->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_presences');
    }
};
