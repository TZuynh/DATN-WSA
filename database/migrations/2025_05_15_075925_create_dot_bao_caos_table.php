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
        Schema::create('dot_bao_caos', function (Blueprint $table) {
            $table->id();
            $table->string('hoc_ky'); // VD: "HK1"
            $table->year('khoa_hoc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dot_bao_caos');
    }
};
