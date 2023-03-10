<?php

namespace App\View\Components\gestor;

use App\Models\Groups;
use Illuminate\View\Component;
use PHPUnit\TextUI\XmlConfiguration\Group;

class NewPasswordForm extends Component
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

        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.gestor.new-password-form', ['group' => $this->group]);
    }
}
