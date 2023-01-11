<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class Register extends Component
{
    public $password = '';
    public $passwordConfirmation = '';

    protected $rules = [
        'password' => 'required',
        'passwordConfirmation' => 'required',
    ];

    public function login()
    {
        $credentials = $this->validate();

        if($credentials['password'] == $credentials['passwordConfirmation']){
            $userId = Auth::id();

            $user = User::find(Auth::id());

            $user->password = Hash::make($credentials['password']);
            $user->password_set = 1;
            $user->save();

            return redirect()->route('login');
        }
        return $this->addError('passwordConfirmation', trans('auth.password_mismatch'));
    }


    public function render()
    {
        return view('livewire.auth.register');
    }
}
