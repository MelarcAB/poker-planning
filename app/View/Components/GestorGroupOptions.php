<?php

namespace App\View\Components;

use Illuminate\View\Component;
//groups
use App\Models\Groups;


class GestorGroupOptions extends Component
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
        return view('components.gestor-group-options', ['group' => $this->group]);
    }
}
