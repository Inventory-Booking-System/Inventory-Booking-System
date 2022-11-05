<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class asset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tag', 'description', 'cost', 'bookable'];

    protected $with = ['loans'];

    /**
     * Get the loans for the asset.
     */
    public function loans()
    {
        return $this->belongsToMany(Loan::class)->withPivot('returned');
    }
}
