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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->date('due_date');
            $table->integer('priority'); #ranked 1 to 5
            $table->enum('status', ['To Do', 'In Progress', 'Under Review', 'Completed'])->default('To Do');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('assignee_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
