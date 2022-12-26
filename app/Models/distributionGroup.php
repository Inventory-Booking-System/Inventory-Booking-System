<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DistributionGroupUser;

class distributionGroup extends Model
{
    use HasFactory;

    /**
     * A distribution group belongs to many users.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'distribution_group_user', 'distribution_group_id', 'user_id');
    }
}
