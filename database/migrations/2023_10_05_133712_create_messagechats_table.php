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
        Schema::create('messagechats', function (Blueprint $table) {
            $table->integer('sender_id')->unsigned(); 
            $table->integer('reciever_id')->unsigned(); 
            $table->integer('chat_id')->unsigned(); 
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');  
            $table->foreign('reciever_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('message')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messagechats');
    }
};
