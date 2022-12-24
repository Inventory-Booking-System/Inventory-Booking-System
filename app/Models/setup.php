<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class setup extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'location_id'];

    protected $with = ['loan'];

    /**
     * A setup belongs to a loan
     */
    public function loan()
    {
        return $this->belongsTo(Loan::class)->withDefault([
            'details' => 'Test Description',
        ]);
    }

    /**
     * A setup has a location
     */
    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}
