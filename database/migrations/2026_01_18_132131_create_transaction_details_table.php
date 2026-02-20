<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->constrained('transactions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->integer('quantity');

            // Snapshot harga saat transaksi, jangan ambil dari tabel produk saat checkout
            $table->bigInteger('price');
            $table->bigInteger('price_buy');
            $table->foreignId('diskon_id')->nullable()->constrained('diskon_produks')->cascadeOnDelete()->cascadeOnUpdate();

            $table->bigInteger('discount')->nullable();

            // Total per baris item
            $table->bigInteger('line_total');

            $table->timestamps();

            // Biar 1 transaksi tidak punya 2 baris untuk produk yang sama
            $table->unique(['transaction_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
