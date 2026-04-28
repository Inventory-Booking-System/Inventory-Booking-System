<?php

namespace App\Http\Livewire\AppSettings;

use Livewire\Component;
use App\Models\User;

class ArchivedUsers extends Component
{
    public $confirmingForceDeleteId = null;

    public function confirmForceDelete($id)
    {
        $this->confirmingForceDeleteId = $id;
        $this->emit('showModal', 'confirm');
    }

    public function restore($id)
    {
        User::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete()
    {
        User::withTrashed()->findOrFail($this->confirmingForceDeleteId)->forceDelete();
        $this->confirmingForceDeleteId = null;
        $this->emit('hideModal', 'confirm');
    }

    public function render()
    {
        return view('livewire.app-settings.archived-users', [
            'archivedUsers' => User::onlyTrashed()->orderBy('deleted_at', 'desc')->get(),
        ]);
    }
}
