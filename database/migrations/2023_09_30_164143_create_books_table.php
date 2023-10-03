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
        Schema::create('books', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->integer('user_id')->unsigned(); 
            $table->integer('addresse_id')->unsigned(); 

           $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
           $table->string('name');
           $table->string('status');
                   //مضفنهاش فى الباك اند
          $table->string('author')->nullable($value = true);
           $table->decimal('price',5,2);
           $table->foreign('addresse_id')->references('id')->on('addresses');
           $table->text('discription')->nullable()->default('their No discription');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
