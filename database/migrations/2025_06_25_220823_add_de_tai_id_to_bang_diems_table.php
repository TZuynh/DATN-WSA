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
        Schema::table('bang_diems', function (Blueprint $table) {
            $table->unsignedBigInteger('de_tai_id')->nullable()->index()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bang_diems', function (Blueprint $table) {
            $table->dropColumn('de_tai_id');
        });
    }
};
