<?php

namespace App\Http\Livewire\DataTable;

use Livewire\WithPagination;

trait WithDetailsPerPagePagination
{
    use WithPagination;

    public $perPage = 10;

    public function renderingWithDetailsPerPagePagination()
    {
        $this->perPage = session()->get('perPageDetails');
    }

    public function updatedPerPage($value)
    {
        session()->put('perPageDetails', $value);
    }

    public function applyPagination($query)
    {
        return $query->paginate($this->perPage);
    }
}