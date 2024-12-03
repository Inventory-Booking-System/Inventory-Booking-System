<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'tag', 'description', 'cost', 'bookable', 'asset_group_id'];

    /**
     * Get the loans for the asset.
     */
    public function loans()
    {
        return $this->belongsToMany(Loan::class)->withPivot('returned');
    }

    public function assetGroup()
    {
        return $this->belongsTo(AssetGroup::class, 'asset_group_id');
    }
}
