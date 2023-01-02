<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Profile extends Component
{
    public User $editing;

    public function mount()
    {
        $this->editing = User::find(Auth::id());
    }

    public function rules()
    {
        return [
            'editing.forename' => 'required|string',
            'editing.surname' => 'required|string',
            'editing.email' => 'required|email|unique:users,email,'.$this->editing->id,
            'editing.password' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate();

        $this->editing->password = Hash::make($this->editing->password);

        $this->editing->save();

        $this->emit('hideModal', 'edit');
    }

    public function render()
    {
        return view('livewire.profile.profile');
    }
}
