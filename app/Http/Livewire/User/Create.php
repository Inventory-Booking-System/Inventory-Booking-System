<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use App\Models\User;

class Create extends Component
{
    public $forename;
    public $surname;
    public $email;

    protected $rules = [
        'forename' => 'required|string',
        'surname' => 'required|string',
        'email' => 'required|email|unique:users',
    ];

    public function updated()
    {
        $this->validate();
    }

    public function save()
    {
        //Check that the data we have recieved is valid
        $this->validate();

        //Create User in the database
        $user = User::create([
            'forename' => $this->forename,
            'surname' => $this->surname,
            'email' => $this->email,
        ]);

        //Alert the user the result
        if($user->exists()){
            $this->emit('alert', ['type' => 'success', 'message' => "$user->forename $user->surname has been created"]);
        }else{
            $this->emit('alert', ['type' => 'failed', 'message' => "$user->forename $user->surname was not created"]);
        }
    }

    public function render()
    {
        return view('livewire.user.create');
    }
}
