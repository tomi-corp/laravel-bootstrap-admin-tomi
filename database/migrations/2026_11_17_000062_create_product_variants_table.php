<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->string('sku', 255);
            $table->decimal('price', 15, 2)->default(0.00);
            $table->integer('stock')->default(0);
            $table->string('image_url', 255)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
