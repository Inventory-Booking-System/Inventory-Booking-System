<?php

namespace App\Http\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Profile extends Component
{
    public User $editing;

    /**
     * User password is a protected field, so will always appear blank on the
     * frontend. We need this separate field to update the password
     */
    public string $newPassword;

    public bool $saved = false;

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
            'newPassword' => 'required|string',
        ];
    }

    public function save()
    {
        $this->saved = false;

        $this->validate();

        $this->editing->password = Hash::make($this->newPassword);

        $this->editing->save();

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.profile.profile');
    }
}
