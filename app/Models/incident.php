<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class incident extends Model
{
    use HasFactory;

    protected $fillable = ['start_date_time', 'location_id', 'distribution_id', 'evidence', 'details'];

    protected $with = ['issues', 'group', 'location'];

    /**
     * Get the issues for the incident.
     */
    public function issues()
    {
        return $this->belongsToMany(EquipmentIssue::class)->withPivot('quantity');
    }

    /**
     * A incident belongs to a location
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * A incident belongs to a distribution group
     */
    public function group()
    {
        return $this->belongsTo(DistributionGroup::class, 'distribution_id', 'id');
    }

    // /**
    //  * Get all the users that the indident has been assigned too
    //  */
    // public function groups()
    // {
    //     return $this->hasManyThrough(DistributionGroup::class, DistributionGroupUser::class, 'id', 'distribution_group_id', 'distribution_id');
    // }
}
