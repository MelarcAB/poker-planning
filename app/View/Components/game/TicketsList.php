<?php

namespace App\View\Components\game;

use Illuminate\View\Component;

//room
use App\Models\Room;

class TicketsList extends Component
{

    private Room $room;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($slug)
    {
        $this->room = Room::where('slug', $slug)->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $room = $this->room;
        return view('components.game.tickets-list', compact('room'));
    }
}
