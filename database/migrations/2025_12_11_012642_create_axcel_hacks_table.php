<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('axcel_hacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->text('description');
            $table->string('project_url')->nullable();
            $table->string('demo_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('tags')->nullable();
            $table->enum('user_type', ['student', 'teacher'])->default('student');
            $table->enum('status', ['pending', 'approved', 'rejected', 'featured'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('status');
            $table->index('user_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('axcel_hacks');
    }
};