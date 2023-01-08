<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tag', 'description', 'cost', 'bookable'];

    /**
     * Get the loans for the asset.
     */
    public function loans()
    {
        return $this->belongsToMany(Loan::class)->withPivot('returned');
    }
}
