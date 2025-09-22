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
        Schema::create('lender_updates', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lender_id')->unsigned();
            $table->foreign('lender_id')->references('id')->on('lenders');
            $table->json('new_data');
            $table->bigInteger('requester_id')->unsigned();
            $table->foreign('requester_id')->references('id')->on('admins');
            $table->enum('status_pertama', ['pending', 'approve', 'reject'])->default('pending');
            $table->enum('status_kedua', ['pending', 'approve', 'reject'])->default('pending');
            $table->bigInteger('first_approver_id')->nullable()->unsigned();
            $table->foreign('first_approver_id')->references('id')->on('admins');
            $table->bigInteger('second_approver_id')->nullable()->unsigned();
            $table->foreign('second_approver_id')->references('id')->on('admins');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lender_updates');
    }
};
