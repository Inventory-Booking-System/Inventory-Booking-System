<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class user extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ['forename', 'surname', 'email'];

    protected $hidden = ['password, remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    /**
     * A user can have many loans.
     */
    public function loans()
    {
        return $this->hasMany(Loans::class);
    }

    /**
     * A user can belongs to many distribution groups.
     */
    public function distributionGroups()
    {
        return $this->belongsToMany(DistributionGroup::class, 'distribution_groups_user', 'user_id', 'distribution_group_id');
    }
}
