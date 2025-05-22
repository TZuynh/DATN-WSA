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
        Schema::create('dot_de_tais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('de_tai_id')->constrained('de_tais');
            $table->foreignId('giang_vien_id')->constrained('tai_khoans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dot_de_tais');
    }
};
