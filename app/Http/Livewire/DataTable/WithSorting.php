<?php

namespace App\Http\Livewire\DataTable;

trait WithSorting
{
    public $sorts = [];

    public function sortBy($field)
    {
        if(! isset($this->sorts[$field])) return $this->sorts[$field] = 'asc';

        if($this->sorts[$field] === 'asc') return $this->sorts[$field] = 'desc';

        unset($this->sorts[$field]);
    }

    public function applySorting($query, $defaultSortField = 'id', $defaultSortDirection = 'asc')
    {
        if($this->sorts != null){
            //Apply user selected sort
            foreach($this->sorts as $field => $direction){
                if ($field === 'users') {
                    $query->orderByRaw('CONCAT(users.forename, users.surname) '.$direction);
                } else {
                    $query->orderBy($field, $direction);
                }
            }
        }else{
            //Apply default sort
            $query->orderBy($defaultSortField, $defaultSortDirection);
        }

        return $query;
    }
}