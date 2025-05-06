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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('project_groups');
            $table->foreignId('supervisor_id')->constrained('lecturers');
            $table->foreignId('co_supervisor_id')->nullable()->constrained('lecturers');
            $table->foreignId('status_id')->constrained('project_statuses');
            $table->string('title', 200)->nullable();
            $table->text('description')->nullable();
            $table->date('proposal_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
