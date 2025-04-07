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
        Schema::create('album_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('album_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('vote'); // 1 for upvote, -1 for downvote
            $table->timestamps();

            // Ensure a user can only vote once per album
            $table->unique(['user_id', 'album_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_votes');
    }
};

