<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('financing_request_invoices')) {
            return;
        }

        Schema::table('financing_request_invoices', function (Blueprint $table) {
            foreach ([1, 2, 3] as $term) {
                $dateColumn = "referral_transfer_date_{$term}";
                $proofColumn = "referral_transfer_proof_{$term}";

                if (! Schema::hasColumn('financing_request_invoices', $dateColumn)) {
                    $table->date($dateColumn)->nullable();
                }

                if (! Schema::hasColumn('financing_request_invoices', $proofColumn)) {
                    $table->string($proofColumn)->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('financing_request_invoices')) {
            return;
        }

        Schema::table('financing_request_invoices', function (Blueprint $table) {
            foreach ([1, 2, 3] as $term) {
                $proofColumn = "referral_transfer_proof_{$term}";
                $dateColumn = "referral_transfer_date_{$term}";

                if (Schema::hasColumn('financing_request_invoices', $proofColumn)) {
                    $table->dropColumn($proofColumn);
                }

                if (Schema::hasColumn('financing_request_invoices', $dateColumn)) {
                    $table->dropColumn($dateColumn);
                }
            }
        });
    }
};
