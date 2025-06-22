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
        Schema::table('nhoms', function (Blueprint $table) {
            $table->foreignId('de_tai_id')->nullable()->constrained('de_tais')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nhoms', function (Blueprint $table) {
            $table->dropForeign(['de_tai_id']);
            $table->dropColumn('de_tai_id');
        });
    }
};
