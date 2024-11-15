<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AssetGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    /**
     * Get the loans for the asset.
     */
    public function assets()
    {
        return $this->belongsToMany(Asset::class);
    }

    public function loans()
    {
        return $this->belongsToMany(Loan::class)->withPivot('quantity');
    }
}
