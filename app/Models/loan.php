<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status_id', 'start_date_time', 'end_date_time', 'details'];

    /**
     * An asset can belong to many loan
     */
    public function assets()
    {
        return $this->belongsToMany(Asset::class)->withPivot('returned');
    }

    /**
     * Get the user for the loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A loan can have a setup component
     */
    public function setup()
    {
        return $this->hasOne(Setup::class);
    }
}
