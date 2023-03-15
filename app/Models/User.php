<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = ['forename', 'surname', 'email', 'has_account', 'password_set'];

    protected $attributes = ['has_account' => false];

    protected $hidden = ['password,', 'remember_token'];

    protected $casts = ['email_verified_at' => 'datetime'];

    /**
     * A user can have many loans.
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * A user can belongs to many distribution groups.
     */
    public function distributionGroups()
    {
        return $this->belongsToMany(DistributionGroup::class, 'distribution_groups_user', 'user_id', 'distribution_group_id');
    }
}
