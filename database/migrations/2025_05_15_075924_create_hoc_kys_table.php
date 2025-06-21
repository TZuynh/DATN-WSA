<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoc_kys', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoc_kys');
    }
}; 