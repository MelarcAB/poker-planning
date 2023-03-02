<?php

namespace App\View\Components\group;

use Illuminate\View\Component;
//group
use App\Models\Groups;

//user
use App\Models\User;

class InvitationForm extends Component
{


    private Groups $group;

    private User $user;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Groups $group = null, User $user = null)
    {
        $this->group = $group;
        $this->user = $user;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $group = $this->group;
        $user = $this->user;
        return view('components.group.invitation-form', compact('group', 'user'));
    }
}
