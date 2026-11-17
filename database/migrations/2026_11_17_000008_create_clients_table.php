<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('lender_id')->unsigned()->nullable();
            $table->string('name', 255);
            $table->string('logo', 255)->nullable();
            $table->string('person_in_charge', 255);
            $table->string('pic_position', 100)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 25)->nullable();
            $table->bigInteger('bank_id')->unsigned()->nullable();
            $table->string('account_name', 255)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->integer('province_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->integer('district_id')->nullable();
            $table->integer('village_id')->nullable();
            $table->text('address')->nullable();
            $table->string('president_commissioner', 255)->nullable();
            $table->string('commissioner', 255)->nullable();
            $table->string('president_director', 255)->nullable();
            $table->string('director_1', 255)->nullable();
            $table->string('director_2', 255)->nullable();
            $table->string('director_3', 255)->nullable();
            $table->decimal('rate', 5, 2)->nullable();
            $table->decimal('admin_fee', 15, 2)->default(0.00);
            $table->tinyInteger('status')->default(1)->comment('1 = Aktif, 0 = Tidak Aktif');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
            $table->unique(['email'], 'uq_clients_email');
            $table->index(['bank_id'], 'idx_clients_bank_id');
            $table->index(['province_id'], 'idx_clients_province_id');
            $table->index(['city_id'], 'idx_clients_city_id');
            $table->index(['district_id'], 'idx_clients_district_id');
            $table->index(['village_id'], 'idx_clients_village_id');
            $table->index(['status'], 'idx_clients_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
