<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->bigInteger('id')->unsigned();
            $table->string('store_name', 255);
            $table->string('store_slug', 255);
            $table->string('owner_name', 255)->nullable();
            $table->string('username', 100);
            $table->string('email', 255);
            $table->string('phone', 50)->nullable();
            $table->string('password', 255);
            $table->text('description')->nullable();
            $table->string('logo_url', 255)->nullable();
            $table->string('banner_url', 255)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 255)->nullable();
            $table->string('province', 255)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->tinyInteger('is_verified')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
