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
        // Add assignment fields to subtasks
        Schema::table('subtasks', function (Blueprint $table) {
            $table->integer('assigned_user_id')->nullable()->after('created_by');
            $table->timestamp('started_at')->nullable()->after('actual_hours');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            
            // Add foreign key for assigned user
            $table->foreign('assigned_user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });

        // Remove assignment fields from cards (they will be auto-calculated)
        Schema::table('cards', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['assigned_user_id']);
            
            // Drop columns
            $table->dropColumn([
                'assigned_user_id',
                'assignment_status',
                'started_at',
                'completed_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore assignment fields to cards
        Schema::table('cards', function (Blueprint $table) {
            $table->integer('assigned_user_id')->nullable()->after('created_by');
            $table->enum('assignment_status', ['not_assigned', 'in_progress', 'completed'])
                  ->default('not_assigned')
                  ->after('assigned_user_id');
            $table->timestamp('started_at')->nullable()->after('assignment_status');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            
            $table->foreign('assigned_user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });

        // Remove assignment fields from subtasks
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn([
                'assigned_user_id',
                'started_at',
                'completed_at'
            ]);
        });
    }
};

