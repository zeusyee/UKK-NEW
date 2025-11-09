<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Create card_assignments table to track assignment history and lifecycle
     * for each card assignment to team members.
     */
    public function up(): void
    {
        // Drop existing table if it exists
        Schema::dropIfExists('card_assignments');
        
        // Recreate with optimized structure
        Schema::create('card_assignments', function (Blueprint $table) {
            // Primary Key
            $table->id('assignment_id');
            
            // Foreign Keys
            $table->integer('card_id');
            $table->integer('user_id');
            
            // Assignment Timeline
            $table->timestamp('assigned_at')->useCurrent()->comment('When card was assigned');
            $table->timestamp('started_at')->nullable()->comment('When member started working');
            $table->timestamp('completed_at')->nullable()->comment('When member completed');
            
            // Status Tracking
            $table->enum('assignment_status', [
                'assigned',      // Card assigned, waiting to start
                'in_progress',   // Member is working on it
                'completed',     // Work completed
                'rejected'       // Assignment rejected/cancelled
            ])->default('assigned')->index();
            
            // Timestamps
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('card_id')
                ->references('card_id')
                ->on('cards')
                ->onDelete('cascade');
                
            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('cascade');

            // Composite Index for fast lookups
            $table->index(['card_id', 'assignment_status']);
            $table->index(['user_id', 'assignment_status']);
            $table->index('assigned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_assignments');
    }
};
