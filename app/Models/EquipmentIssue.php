<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class EquipmentIssue extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'cost'];

    /**
     * Get the incidents for the equipment issues.
     */
    public function incidents()
    {
        return $this->belongsToMany(Incident::class)->withPivot('quantity');
    }
}
