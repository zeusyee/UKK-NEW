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
        // Add timestamps to project_members
        Schema::table('project_members', function (Blueprint $table) {
            if (!Schema::hasColumn('project_members', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('project_members', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Add timestamps to boards
        Schema::table('boards', function (Blueprint $table) {
            if (!Schema::hasColumn('boards', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Add timestamps to cards
        Schema::table('cards', function (Blueprint $table) {
            if (!Schema::hasColumn('cards', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Add timestamps to subtasks
        Schema::table('subtasks', function (Blueprint $table) {
            if (!Schema::hasColumn('subtasks', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Add timestamps to card_assignments
        Schema::table('card_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('card_assignments', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('card_assignments', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Add timestamps to comments
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });

        // Add timestamps to time_logs
        Schema::table('time_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('time_logs', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('time_logs', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'project_members',
            'boards',
            'cards',
            'subtasks',
            'card_assignments',
            'comments',
            'time_logs'
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn(['updated_at']);
                if ($table->hasColumn('created_at')) {
                    $table->dropColumn(['created_at']);
                }
            });
        }
    }
};