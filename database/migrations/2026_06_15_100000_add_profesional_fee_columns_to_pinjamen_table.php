<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->decimal('profesional_fee_1', 15, 2)->default(0)->after('jumlah_provisi');
            $table->decimal('profesional_fee_2', 15, 2)->default(0)->after('profesional_fee_1');
            $table->decimal('jumlah_profesional_1', 15, 2)->default(0)->after('profesional_fee_2');
            $table->decimal('jumlah_profesional_2', 15, 2)->default(0)->after('jumlah_profesional_1');
            $table->decimal('ksp_fee', 15, 2)->default(0)->after('jumlah_profesional_2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pinjamen', function (Blueprint $table) {
            $table->dropColumn([
                'profesional_fee_1',
                'profesional_fee_2',
                'jumlah_profesional_1',
                'jumlah_profesional_2',
                'ksp_fee',
            ]);
        });
    }
};
