<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class distributionGroup extends Model
{
    use HasFactory;

    /**
     * A distribution group can have many users.
     */
    public function users()
    {
        return $this->hasMany(DistributionGroupUser::class, 'distribution_group_id');
    }
}
