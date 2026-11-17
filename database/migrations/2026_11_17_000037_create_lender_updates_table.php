<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lender_updates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('lender_id')->unsigned();
            $table->longText('new_data');
            $table->bigInteger('requester_id')->unsigned();
            $table->enum('status_pertama', ['pending', 'approve', 'reject'])->default('pending');
            $table->enum('status_kedua', ['pending', 'approve', 'reject'])->default('pending');
            $table->bigInteger('first_approver_id')->unsigned()->nullable();
            $table->bigInteger('second_approver_id')->unsigned()->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index(['lender_id'], 'lender_updates_lender_id_foreign');
            $table->index(['requester_id'], 'lender_updates_requester_id_foreign');
            $table->index(['first_approver_id'], 'lender_updates_first_approver_id_foreign');
            $table->index(['second_approver_id'], 'lender_updates_second_approver_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lender_updates');
    }
};
