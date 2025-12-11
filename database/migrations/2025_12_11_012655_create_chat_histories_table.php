<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bot_id')->nullable();
            $table->string('subject')->nullable();
            $table->text('user_message');
            $table->text('bot_response');
            $table->integer('tokens_used')->default(0);
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('bot_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};