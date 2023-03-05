<?php

namespace App\View\Components\game;

use Illuminate\View\Component;
//group
use App\Models\Groups;
//decks
use App\Models\Deck;
use PHPUnit\TextUI\XmlConfiguration\Group;

class Tablero extends Component
{

    private Groups $group;

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
        return view('components.game.tablero', ['deck' => $this->deck]);
    }
}
