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
        Schema::create('chats', function (Blueprint $table) {
            $table->integer('user1_id')->unsigned(); 
            $table->integer('user2_id')->unsigned(); 
            $table->foreign('user1_id')->references('id')->on('users')->onDelete('cascade');  
            $table->foreign('user2_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user1_id', 'user2_id'], 'id');
            $table->timestamps();

        });
        DB::statement('ALTER TABLE chats ADD CONSTRAINT check_column1_greater_than_column2 CHECK (user1_id > user2_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
