<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('phongs', function (Blueprint $table) {
            $table->id();
            $table->string('ten_phong')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('phongs');
    }
}; 