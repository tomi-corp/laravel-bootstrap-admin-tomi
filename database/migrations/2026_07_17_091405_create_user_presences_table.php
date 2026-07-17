<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('user_presences', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('user_id');

            $table->date('presence_date')->nullable();

            // Data clock in
            $table->timestamp('clock_in_at')->nullable();
            $table->decimal('clock_in_latitude', 10, 7)->nullable();
            $table->decimal('clock_in_longitude', 10, 7)->nullable();
            $table->text('clock_in_address')->nullable();
            $table->string('clock_in_image')->nullable();

            // Data clock out
            $table->timestamp('clock_out_at')->nullable();
            $table->decimal('clock_out_latitude', 10, 7)->nullable();
            $table->decimal('clock_out_longitude', 10, 7)->nullable();
            $table->text('clock_out_address')->nullable();
            $table->string('clock_out_image')->nullable();

            $table->string('status')->default('present');
            $table->string('approval_status')->default('approved');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            /*
            |--------------------------------------------------------------------------
            | Foreign Key
            |--------------------------------------------------------------------------
            */

            $table->foreign('company_id', 'fk_user_presences_company')
                ->references('id')
                ->on('list_perusahaans')
                ->cascadeOnDelete();

            $table->foreign('employee_id', 'fk_user_presences_employee')
                ->references('id')
                ->on('list_karyawans')
                ->cascadeOnDelete();

            $table->foreign('user_id', 'fk_user_presences_user')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('approved_by', 'fk_user_presences_approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::table('user_presences', function (Blueprint $table) {
            $table->dropForeign('fk_user_presences_approved_by');
            $table->dropForeign('fk_user_presences_company');
            $table->dropForeign('fk_user_presences_employee');
            $table->dropForeign('fk_user_presences_user');
        });

        Schema::dropIfExists('user_presences');
    }
};
