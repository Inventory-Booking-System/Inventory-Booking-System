<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class incident extends Model
{
    use HasFactory;

    protected $fillable = ['start_date_time', 'location_id', 'distribution_id', 'evidence', 'details'];

    /**
     * Get the issues for the incident.
     */
    public function issues()
    {
        return $this->belongsToMany(EquipmentIssue::class)->withPivot('quantity');
    }
}
