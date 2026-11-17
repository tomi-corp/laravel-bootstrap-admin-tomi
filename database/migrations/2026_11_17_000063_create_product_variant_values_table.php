<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->charset = 'latin1';
            $table->collation = 'latin1_swedish_ci';
            $table->bigInteger('id')->unsigned();
            $table->bigInteger('product_variant_id')->unsigned();
            $table->bigInteger('product_option_value_id')->unsigned();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_values');
    }
};
