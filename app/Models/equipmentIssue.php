<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class equipmentIssue extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'cost'];

    /**
     * Get the incidents for the equipment issues.
     */
    public function incidents()
    {
        return $this->belongsToMany(Incident::class)->withPivot('quantity');
    }
}
