<?php

namespace App\Http\Livewire\User;

use Livewire\Component;

class Show extends Component
{
    public $userID = '';
    public $forename = '';
    public $surname = '';
    public $email = '';

    public function mount($user)
    {
        $this->userID = $user->id;
        $this->forename = $user->forename;
        $this->surname = $user->surname;
        $this->email = $user->email;
    }

    public function render()
    {
        return view('livewire.user.show');
    }
}
