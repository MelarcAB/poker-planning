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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('slug')->unique();

            //user creador del grupo
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
            //password
            $table->string('code')->nullable();
            //soft delete
            $table->softDeletes();

            //deck id
            $table->bigInteger('deck_id')->unsigned()->nullable();

            //foreign keys
            $table->foreign('deck_id')->references('id')->on('decks')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('groups');
    }
};
