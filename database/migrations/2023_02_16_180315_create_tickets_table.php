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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            //desc
            $table->text('description')->nullable();
            //status
            $table->string('status')->default('open');
            //room_id
            $table->unsignedBigInteger('room_id')->nullable();
            //soft delete
            //slug
            $table->string('slug')->unique()->nullable();
            //soft delete
            $table->softDeletes();
            $table->timestamps();

            //foreign key
            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
