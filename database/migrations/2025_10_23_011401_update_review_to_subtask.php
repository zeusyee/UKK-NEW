<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove review columns from cards table
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['reviewed_by', 'reviewed_at', 'review_notes']);
        });

        // Add review columns to subtasks table
        Schema::table('subtasks', function (Blueprint $table) {
            $table->integer('created_by')->nullable()->after('card_id');
            $table->integer('reviewed_by')->nullable()->after('actual_hours');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('review_notes')->nullable()->after('reviewed_at');
            $table->text('completion_notes')->nullable()->after('review_notes');
            
            $table->foreign('created_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('reviewed_by')->references('user_id')->on('users')->onDelete('set null');
        });

        // Update subtask status enum to include 'review'
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('subtasks', function (Blueprint $table) {
            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo')->after('description');
        });

        // Update card status enum to remove 'review'
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('cards', function (Blueprint $table) {
            $table->enum('status', ['todo', 'in_progress', 'done'])->default('todo')->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['created_by', 'reviewed_by', 'reviewed_at', 'review_notes', 'completion_notes']);
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn('status');
        });
        
        Schema::table('cards', function (Blueprint $table) {
            $table->enum('status', ['todo', 'in_progress', 'review', 'done'])->default('todo')->after('due_date');
            $table->integer('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            
            $table->foreign('reviewed_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }
};