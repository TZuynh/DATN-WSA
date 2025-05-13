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
        Schema::table('evaluations', function (Blueprint $table) {
            $table->decimal('report_score', 4, 2)->nullable();
            $table->decimal('demo_score', 4, 2)->nullable();
            $table->decimal('presentation_score', 4, 2)->nullable();
            $table->decimal('qna_score', 4, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
