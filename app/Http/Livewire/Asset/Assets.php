<?php

namespace App\Http\Livewire\Asset;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Asset;

class Assets extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function render()
    {
        return view('livewire.asset.assets', [
            'assets' => Asset::search('name', $this->search)->paginate(10),
        ]);
    }
}
