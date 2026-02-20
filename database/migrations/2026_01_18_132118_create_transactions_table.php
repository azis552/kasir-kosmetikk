<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Dibuat saat checkout, jadi nullable saat DRAFT
            $table->string('transaction_code', 50)->nullable()->unique();

            $table->dateTime('transaction_date');

            // Semua uang pakai bigInteger (rupiah tanpa desimal)
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('diskon_item')->default(0);
            $table->foreignId('voucher')->nullable()->constrained('vouchers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('potongan_voucher')->default(0);
            $table->bigInteger('total')->default(0);

            // Baru terisi saat bayar
            $table->bigInteger('dibayar')->nullable();
            $table->bigInteger('kembalian')->nullable();
            $table->string('payment_method', 30)->nullable();

            $table->string('pelanggan_name', 100)->nullable();

            $table->enum('status', ['DRAFT','VOID', 'PAID', 'CANCELLED', 'HOLD'])->default('DRAFT');
            $table->timestamp('paid_at')->nullable();

            // Opsional, tapi berguna kalau ada banyak device kasir
            $table->string('terminal_id', 50)->nullable();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->decimal('tax', 5, 2)->nullable();
            $table->integer('tax_amount')->default(0);

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
