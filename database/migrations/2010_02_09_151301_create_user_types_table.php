<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//import bd
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
        Schema::create('user_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->timestamps();
        });

        //insert default user types
        //1 - user_web
        //2 - gestor
        //3 - admin
        DB::table('user_types')->insert([
            ['name' => 'user_web'],
            ['name' => 'gestor'],
            ['name' => 'admin']
        ]);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_types');
    }
};
