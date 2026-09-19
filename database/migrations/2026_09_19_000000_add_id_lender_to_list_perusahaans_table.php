<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            ! Schema::hasTable('list_perusahaans')
            || ! Schema::hasTable('lenders')
            || Schema::hasColumn('list_perusahaans', 'id_lender')
        ) {
            return;
        }

        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->foreignId('id_lender')
                ->nullable()
                ->after('unique_code')
                ->constrained('lenders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('list_perusahaans') || ! Schema::hasColumn('list_perusahaans', 'id_lender')) {
            return;
        }

        Schema::table('list_perusahaans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_lender');
        });
    }
};
