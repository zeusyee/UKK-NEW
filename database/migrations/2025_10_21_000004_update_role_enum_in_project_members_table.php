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
        Schema::table('project_members', function (Blueprint $table) {
            // First drop the existing role column if it exists
            if (Schema::hasColumn('project_members', 'role')) {
                $table->dropColumn('role');
            }
            
            // Add the role column with the correct enum values
            $table->enum('role', ['admin', 'leader', 'member'])->default('member');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_members', function (Blueprint $table) {
            // Revert back to original role column
            $table->dropColumn('role');
            $table->enum('role', ['member', 'admin'])->default('member');
        });
    }
};