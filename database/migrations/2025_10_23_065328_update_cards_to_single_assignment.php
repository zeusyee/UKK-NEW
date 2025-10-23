<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old card_assignments table
        Schema::dropIfExists('card_assignments');
        
        // Add assigned_user_id directly to cards table
        Schema::table('cards', function (Blueprint $table) {
            $table->integer('assigned_user_id')->nullable()->after('created_by');
            $table->enum('assignment_status', ['not_assigned', 'in_progress', 'completed'])->default('not_assigned')->after('assigned_user_id');
            $table->timestamp('started_at')->nullable()->after('assignment_status');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            
            $table->foreign('assigned_user_id')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['assigned_user_id']);
            $table->dropColumn(['assigned_user_id', 'assignment_status', 'started_at', 'completed_at']);
        });
        
        // Recreate the old table structure
        Schema::create('card_assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->integer('card_id');
            $table->integer('user_id');
            $table->enum('assignment_status', ['not_assigned', 'in_progress', 'completed'])->default('not_assigned');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->foreign('card_id')->references('card_id')->on('cards')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }
};