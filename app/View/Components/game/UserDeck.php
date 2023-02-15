<?php

namespace App\View\Components\game;

use Illuminate\View\Component;
//deck
use App\Models\Deck;

class UserDeck extends Component
{

    private Deck $deck;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Deck $deck)
    {
        $this->deck = $deck;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        //obtener las cartas del deck
        $cards = $this->deck->cards;
        return view('components.game.user-deck', compact('cards'));
    }
}
