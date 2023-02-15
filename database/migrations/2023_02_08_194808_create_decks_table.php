<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('decks', function (Blueprint $table) {
            $table->id();
            //title, description, user (nullable)
            $table->string('title');
            $table->string('description')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            //image (nullable)
            $table->string('image')->nullable()->default('img/card.jpg');

            //public 
            $table->boolean('public')->default(false);

            //timestamps
            $table->softDeletes();
            $table->timestamps();

            //foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            //index
            $table->index('public');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('decks');
    }
};
