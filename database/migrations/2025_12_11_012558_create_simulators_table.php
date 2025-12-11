<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('simulators', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('embed_url');
            $table->string('thumbnail')->nullable();
            $table->enum('status', ['active', 'inactive', 'draft'])->default('draft');
            $table->integer('sort_order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->index('subject');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('simulators');
    }
};