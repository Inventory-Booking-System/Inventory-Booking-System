<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use App\Models\User;

class Edit extends Component
{
    public $userID = '';
    public $forename = '';
    public $surname = '';
    public $email = '';

    public function rules()
    {
        return [
            'forename' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email|unique:users,email,'.$this->userID,
        ];
    }

    public function mount($user)
    {
        $this->userID = $user->id;
        $this->forename = $user->forename;
        $this->surname = $user->surname;
        $this->email = $user->email;
    }

    public function updated()
    {
        $this->validate();
    }

    public function save()
    {
        $this->validate();

        $user = User::where('id', $this->userID)->update([
            'forename' => $this->forename,
            'surname' => $this->surname,
            'email' => $this->email,
        ]);

        //Alert the user the result
        if($user){
            $this->emit('alert', ['type' => 'success', 'message' => "$this->forename $this->surname has been modified"]);
        }else{
            $this->emit('alert', ['type' => 'failed', 'message' => "$this->forename $this->surname was not modified"]);
        }
    }

    public function render()
    {
        return view('livewire.user.edit');
    }
}
