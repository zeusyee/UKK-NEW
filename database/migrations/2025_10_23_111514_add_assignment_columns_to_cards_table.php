<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add assigned_user_id directly to cards table
        if (!Schema::hasColumn('cards', 'assigned_user_id')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->integer('assigned_user_id')->nullable()->after('created_by');
                $table->enum('assignment_status', ['not_assigned', 'in_progress', 'completed'])->nullable()->after('assigned_user_id');
                $table->timestamp('started_at')->nullable()->after('assignment_status');
                $table->timestamp('completed_at')->nullable()->after('started_at');
                
                $table->foreign('assigned_user_id')->references('user_id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cards', 'assigned_user_id')) {
            Schema::table('cards', function (Blueprint $table) {
                $table->dropForeign(['assigned_user_id']);
                $table->dropColumn(['assigned_user_id', 'assignment_status', 'started_at', 'completed_at']);
            });
        }
    }
};