<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_disbursement_tomis', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->longText('disbursement_id');
            $table->longText('external_id');
            $table->string('bank_code', 255);
            $table->double('amount');
            $table->string('account_holder_name', 255);
            $table->string('disbursement_description', 255);
            $table->string('status', 255);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_disbursement_tomis');
    }
};
