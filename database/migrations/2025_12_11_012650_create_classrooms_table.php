<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('invite_code', 8)->unique();
            $table->string('subject')->nullable();
            $table->enum('status', ['active', 'archived'])->default('active');
            $table->integer('max_students')->default(50);
            $table->timestamps();
            
            $table->index('teacher_id');
            $table->index('invite_code');
        });

        Schema::create('classroom_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['active', 'removed'])->default('active');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            
            $table->unique(['classroom_id', 'user_id']);
            $table->index('classroom_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_students');
        Schema::dropIfExists('classrooms');
    }
};