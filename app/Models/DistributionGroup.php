<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\DistributionGroupUser;

class DistributionGroup extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * A distribution group belongs to many users.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'distribution_group_user', 'distribution_group_id', 'user_id');
    }
}
