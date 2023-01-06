<?php

namespace App\Http\Livewire\DataTable;
use Illuminate\Support\Facades\Log;

trait WithBulkActions
{
    public $selectPage = false;
    public $selectAll = false;
    public $selected = [];

    public function renderingWithBulkActions()
    {
        if ($this->selectAll) $this->selectPageRows();
    }

    public function updatedSelected($value)
    {
        /**
         * When all rows have been selected, then one is unselected, we must
         * add all but one row to $this->selected
         */
        if ($this->selectAll) {
            /**
             * We must run $rowsAll query before $rowsPage, as otherwise the 
             * $rowsPage results will be cached and returned for both
             */
            $rowsAll = $this->rowsQuery->pluck('id')->map(fn($id) => (string) $id)->all();
            $rowsPage = $this->rows->pluck('id')->map(fn($id) => (string) $id)->all();
            
            // Find the row that was unchecked
            $diff = array_diff($rowsPage, $value);

            // Set all checkboxes apart from the one that was unchecked
            $this->selected = array_values(array_diff($rowsAll, $diff));
        }

        $this->selectAll = false;
        $this->selectPage = false;
    }

    public function updatedSelectPage($value)
    {
        if ($value) return $this->selectPageRows();
        
        $this->selectAll = false;

        $this->selected = [];
    }

    public function selectPageRows()
    {
        $this->selected = $this->rows->pluck('id')->map(fn($id) => (string) $id);
    }

    public function selectAll()
    {
        $this->selectAll = true;
    }

    public function getSelectedRowsQueryProperty()
    {
        return (clone $this->rowsQuery)
            ->unless($this->selectAll, fn($query) => $query->whereKey($this->selected));
    }
}