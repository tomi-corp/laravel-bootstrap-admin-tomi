<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('admins') || Schema::hasColumn('admins', 'lender_id')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->foreignId('lender_id')
                ->nullable()
                ->after('company_id')
                ->constrained('lenders')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('admins') || ! Schema::hasColumn('admins', 'lender_id')) {
            return;
        }

        Schema::table('admins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lender_id');
        });
    }
};
