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
        Schema::create('defense_committee_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('committee_id')->constrained('defense_committees')->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained('lecturers')->cascadeOnDelete();
            $table->string('role', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defense_committee_members');
    }
};
