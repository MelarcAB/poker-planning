<?php

namespace App\View\Components\user;

use App\Models\Groups;
use Illuminate\View\Component;

class RoomForm extends Component
{

    private Groups $group;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Groups $group)
    {
        $this->group = $group;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        //obtener usuario logueado
        $user = auth()->user();
        //obtener el grupo
        $group = $this->group;

        return view('components.user.room-form', compact('user', 'group'));
    }
}
