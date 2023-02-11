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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            //name
            $table->string('name');
            //description type text
            $table->text('description')->nullable();
            //slug
            $table->string('slug')->nullable()->unique();
            //user_id
            $table->bigInteger('user_id')->unsigned()->nullable();
            //group_id
            $table->bigInteger('group_id')->unsigned()->nullable();
            //soft delete
            $table->softDeletes();
            //status int 
            $table->bigInteger('room_status_id')->unsigned()->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('group_id')->references('id')->on('groups');

            //status foreign 
            $table->foreign('room_status_id')->references('id')->on('room_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};
