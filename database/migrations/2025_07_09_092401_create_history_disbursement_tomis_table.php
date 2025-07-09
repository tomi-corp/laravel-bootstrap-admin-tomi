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
        Schema::create('history_disbursement_tomis', function (Blueprint $table) {
            $table->id();
            $table->longText('disbursement_id');
            $table->longText('external_id');
            $table->string('bank_code');
            $table->double('amount');
            $table->string('account_holder_name');
            $table->string('disbursement_description');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_disbursement_tomis');
    }
};
