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
        Schema::table('defense_sessions', function (Blueprint $table) {
            $table->foreignId('committee_id')->nullable()->constrained('defense_committees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('defense_sessions', function (Blueprint $table) {
            //
        });
    }
};
