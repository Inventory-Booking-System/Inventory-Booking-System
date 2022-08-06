<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class distributionGroup extends Model
{
    use HasFactory;


    /**
     * A distribution group can belongs to many users.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
