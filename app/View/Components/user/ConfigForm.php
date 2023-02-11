<?php

namespace App\View\Components\user;

use Illuminate\View\Component;

class ConfigForm extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        //obtener usuario logeado
        $user = auth()->user();
        return view('components.user.config-form', compact('user'));
    }
}
