<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//db
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //create RoomStatus
        Schema::create('room_status', function (Blueprint $table) {
            //fields
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            //timestamps
            $table->timestamps();
        });

        //insert data
        DB::table('room_status')->insert([
            ['name' => 'Por empezar', 'description' => 'Sala lista para empezar la partida'],
            ['name' => 'Jugando', 'description' => 'Sala en juego'],
            ['name' => 'Terminado', 'description' => 'Sala terminada'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // drop RoomStatus
        Schema::dropIfExists('room_status');
    }
};
