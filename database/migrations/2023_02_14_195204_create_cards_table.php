<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

//deck
use App\Models\Deck;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id();
            $table->string('value')->default('');
            //deck
            $table->bigInteger('deck_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('deck_id')->references('id')->on('decks');
        });


        //crear cartas y deck por defecto
        $deck = new Deck();
        $deck->title = 'Default';
        $deck->description = 'Default deck';
        $deck->public = true;
        $deck->slug = "default-deck";
        $deck->save();

        //crear cartas
        $cards = [
            '0',
            '1/2',
            '1',
            '2',
            '3',
            '5',
            '8',
            '13',
            '20',
            '40',
            '100',
            '?',
            ',',
            '∞',
            '☕',
        ];

        foreach ($cards as $card) {
            $deck->cards()->create([
                'value' => $card,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
};
