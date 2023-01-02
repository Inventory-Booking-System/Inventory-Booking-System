<?php

namespace App\Http\Livewire\AppSettings;

use Livewire\Component;
use Spatie\LaravelSettings\Settings; // import the Settings class
use App\Settings\SystemSettings;

class AppSettings extends Component
{
    public $settings;

    public function rules()
    {
        return [
            'settings' => 'array',
            'settings.mailer' => 'required|string',
            'settings.host' => 'required|string',
            'settings.port' => 'required|string',
            'settings.username' => 'required|string',
            'settings.password' => 'required|string',
            'settings.encryption' => 'required|string',
            'settings.from_address' => 'required|string',
        ];
    }

    public function save()
    {
        $this->validate();
    }

    public function mount()
    {
        $this->settings = new SystemSettings();

        dd($this->settings->mailer);

        //$settings->set('mailer', 'abc');
        $settings->save();
    }

    public function render()
    {
        return view('livewire.app-settings.app-settings');
    }
}
