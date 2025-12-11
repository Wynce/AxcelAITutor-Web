<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add user_type and parent_id to users table
        Schema::table('users', function (Blueprint $table) {
            $table->enum('user_type', ['student', 'parent', 'teacher'])->default('student')->after('email');
            $table->unsignedBigInteger('parent_id')->nullable()->after('user_type');
            $table->string('school_name')->nullable()->after('parent_id');
            $table->string('grade_level')->nullable()->after('school_name');
            $table->string('subjects_teaching')->nullable()->after('grade_level'); // For teachers - comma separated
            
            $table->foreign('parent_id')->references('id')->on('users')->onDelete('set null');
            $table->index('user_type');
        });

        // Create student_teacher pivot table
        Schema::create('student_teacher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('classroom_id')->nullable();
            $table->string('subject')->nullable();
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->onDelete('set null');
            
            $table->unique(['student_id', 'teacher_id', 'subject']);
        });

        // Create user_analytics table
        Schema::create('user_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->integer('chat_count')->default(0);
            $table->integer('simulator_views')->default(0);
            $table->integer('quiz_attempts')->default(0);
            $table->integer('quiz_correct')->default(0);
            $table->integer('time_spent_minutes')->default(0);
            $table->json('subjects_studied')->nullable(); // {"Physics": 10, "Chemistry": 5}
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'date']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_analytics');
        Schema::dropIfExists('student_teacher');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['user_type']);
            $table->dropColumn(['user_type', 'parent_id', 'school_name', 'grade_level', 'subjects_teaching']);
        });
    }
};