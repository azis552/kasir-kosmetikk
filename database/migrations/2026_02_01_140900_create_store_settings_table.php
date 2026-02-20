<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();

            // Identitas toko
            $table->string('store_name')->default('Kembangayu Cosmetics Shop');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            // Untuk struk / laporan
            $table->string('receipt_header')->nullable();
            $table->text('receipt_footer')->nullable();

            // Logo paths (storage/public)
            $table->string('logo_app_dark')->nullable();
            $table->string('logo_app_light')->nullable();
            $table->string('logo_doc')->nullable();
            $table->string('logo_icon')->nullable();
            $table->string('logo_receipt')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
