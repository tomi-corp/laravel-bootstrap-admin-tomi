<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_payment_terms', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_general_ci';
            $table->bigIncrements('id');
            $table->bigInteger('vendor_id')->unsigned();
            $table->decimal('term_1_percentage', 5, 2)->default(100.00);
            $table->decimal('term_2_percentage', 5, 2)->default(0.00);
            $table->decimal('term_3_percentage', 5, 2)->default(0.00);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();
            $table->index(['vendor_id'], 'idx_vendor_payment_terms_vendor_id');
            $table->index(['deleted_at'], 'idx_vendor_payment_terms_deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_payment_terms');
    }
};
