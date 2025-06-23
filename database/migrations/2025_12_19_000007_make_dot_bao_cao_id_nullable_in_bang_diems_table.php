<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bang_diems', function (Blueprint $table) {
            $table->unsignedBigInteger('dot_bao_cao_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('bang_diems', function (Blueprint $table) {
            $table->unsignedBigInteger('dot_bao_cao_id')->nullable(false)->change();
        });
    }
};
