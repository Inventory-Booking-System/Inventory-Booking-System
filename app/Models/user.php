<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user extends Model
{
    use HasFactory;

    protected $fillable = ['forename', 'surname', 'email'];

    /**
     * A user can have many loans.
     */
    public function loans()
    {
        return $this->hasMany(Loans::class);
    }

    /**
     * A user can belongs to many distribution groups.
     */
    public function distributionGroups()
    {
        return $this->belongsToMany(DistributionGroup::class, 'distribution_groups_user', 'user_id', 'distribution_group_id');
    }
}
