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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'image')) {
                $table->string('image')->nullable()->after('country');
            }
            if (!Schema::hasColumn('users', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false)->after('image');
            }
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->after('is_deleted');
            }
            if (!Schema::hasColumn('users', 'login_type')) {
                $table->string('login_type')->nullable()->after('status');
            }
            if (!Schema::hasColumn('users', 'bot_id')) {
                $table->unsignedBigInteger('bot_id')->nullable()->after('login_type');
            }
            if (!Schema::hasColumn('users', 'is_first_login')) {
                $table->boolean('is_first_login')->default(true)->after('bot_id');
            }
            if (!Schema::hasColumn('users', 'last_active_at')) {
                $table->timestamp('last_active_at')->nullable()->after('is_first_login');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = ['image', 'is_deleted', 'status', 'login_type', 'bot_id', 'is_first_login', 'last_active_at'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

