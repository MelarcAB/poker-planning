<?php

namespace App\View\Components\user;

use Illuminate\View\Component;
//user
use App\Models\User;

class ConfigForm extends Component
{


    private User $user;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(User $user = null)
    {
        $this->user = $user;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        //obtener usuario logeado
        $user = $this->user;
        return view('components.user.config-form', compact('user'));
    }
}
