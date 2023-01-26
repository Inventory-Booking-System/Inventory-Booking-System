<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Incident extends Model
{
    use HasFactory;

    protected $fillable = ['start_date_time', 'location_id', 'distribution_id', 'evidence', 'details', 'status_id'];

    protected $with = ['issues', 'group', 'location', 'user_created_by'];

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

    /**
     * Get the user who created the loan.
     */
    public function user_created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function getStatusIds()
    {
        return [
            '0' => 'Outstanding',
            '1' => 'Resolved',
        ];
    }

    /**
     * Convert status id to name
     */
    public function getStatusAttribute()
    {
        return $this->getStatusIds()[$this->status_id] ?? 'Error';
    }

    public function getStartDateTimeAttribute($value)
    {
        $date = Carbon::parse($value);

        return $date->format('d M Y H:i');
    }
}
