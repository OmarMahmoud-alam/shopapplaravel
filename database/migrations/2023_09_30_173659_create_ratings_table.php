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
        Schema::create('ratings', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->integer('user_id')->unsigned(); 
            $table->integer('seller_id')->unsigned(); 
            $table->tinyInteger('rating'); 

            $table->index(['user_id', 'seller_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;  
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');; 
            $table->string('comment')->nullable($value=true);


          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
