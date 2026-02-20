<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('primary_color')->default('#28a745');
            $table->string('secondary_color')->default('#6c757d');
            $table->string('sidebar_color')->default('#212529');
            $table->string('background_color')->default('#f6f7fb');
            $table->string('text_color')->default('#000000');
            $table->timestamps();
        });

        // insert default row
        DB::table('app_settings')->insert([
            'primary_color' => '#28a745',
            'secondary_color' => '#6c757d',
            'sidebar_color' => '#212529',
            'background_color' => '#f6f7fb',
            'text_color' => '#ffffff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

};
