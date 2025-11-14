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
        Schema::table('cards', function (Blueprint $table) {
            $table->boolean('help_requested')->default(false)->after('assigned_user_id');
            $table->timestamp('help_requested_at')->nullable()->after('help_requested');
            $table->text('help_message')->nullable()->after('help_requested_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->dropColumn(['help_requested', 'help_requested_at', 'help_message']);
        });
    }
};
