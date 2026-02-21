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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('barcode', 200)->unique()->nullable();
            $table->string('sku', 50)->nullable();
            $table->string('name', 150);
            $table->unsignedBigInteger('category_id');
            $table->integer('price');
            $table->integer('price_buy');
            $table->foreign('category_id')->references('id')->on('product_categories')->onDelete('cascade');
            $table->string('unit', 50);
            $table->integer('min_stock');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
