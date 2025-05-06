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
        Schema::create('defense_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects');
            $table->foreignId('chairman_id')->constrained('lecturers');
            $table->foreignId('secretary_id')->constrained('lecturers');
            $table->dateTime('schedule_date')->nullable();
            $table->string('venue', 200)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defense_sessions');
    }
};
