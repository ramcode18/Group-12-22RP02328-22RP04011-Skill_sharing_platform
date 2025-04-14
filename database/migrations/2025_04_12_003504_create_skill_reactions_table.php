<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('skill_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('skill_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['like', 'dislike']);
            $table->timestamps();
            
            // Ensure a user can only react once to a skill
            $table->unique(['user_id', 'skill_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('skill_reactions');
    }
};
