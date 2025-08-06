<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate follows
            $table->unique(['follower_id', 'following_id']);
            
            // Add indexes for better performance
            $table->index(['following_id', 'created_at']);
            $table->index(['follower_id', 'created_at']);
        });

        // Add check constraint using raw SQL to prevent self-follows
        DB::statement('ALTER TABLE follows ADD CONSTRAINT follows_no_self_follow CHECK (follower_id != following_id)');
    }

    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};