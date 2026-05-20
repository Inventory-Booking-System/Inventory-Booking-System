<?php

namespace App\Http\Livewire\AppSettings;

use Livewire\Component;
use App\Models\Staff;
use App\Models\Student;

class ArchivedUsers extends Component
{
    public $confirmingForceDeleteId = null;
    public $confirmingForceDeleteType = null;

    public function confirmForceDelete($id, $type)
    {
        $this->confirmingForceDeleteId = $id;
        $this->confirmingForceDeleteType = $type;
        $this->emit('showModal', 'confirm');
    }

    public function restore($id, $type)
    {
        $model = $type === 'staff' ? Staff::class : Student::class;
        $model::withTrashed()->findOrFail($id)->restore();
    }

    public function forceDelete()
    {
        $model = $this->confirmingForceDeleteType === 'staff' ? Staff::class : Student::class;
        $model::withTrashed()->findOrFail($this->confirmingForceDeleteId)->forceDelete();
        $this->confirmingForceDeleteId = null;
        $this->confirmingForceDeleteType = null;
        $this->emit('hideModal', 'confirm');
    }

    public function render()
    {
        return view('livewire.app-settings.archived-users', [
            'archivedStaff' => Staff::onlyTrashed()->orderBy('deleted_at', 'desc')->get(),
            'archivedStudents' => Student::onlyTrashed()->orderBy('deleted_at', 'desc')->get(),
        ]);
    }
}
