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
        Schema::create('comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->unsignedBigInteger('card_id')->nullable();
            $table->unsignedBigInteger('subtask_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('comment_text');
            $table->enum('comment_type', ['general', 'system', 'mention'])->default('general');
            $table->timestamps();

            // Foreign keys
            $table->foreign('card_id')->references('card_id')->on('cards')->onDelete('cascade');
            $table->foreign('subtask_id')->references('subtask_id')->on('subtasks')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');

            // Indexes
            $table->index('card_id');
            $table->index('subtask_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
