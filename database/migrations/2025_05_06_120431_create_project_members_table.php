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
            Schema::create('project_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('group_id')->constrained('project_groups');
                $table->foreignId('student_id')->constrained('students');
                $table->string('role', 50)->nullable();
                $table->timestamp('joined_at')->useCurrent();
                $table->unique(['group_id', 'student_id']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
    }
};
