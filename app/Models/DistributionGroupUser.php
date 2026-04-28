<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionGroupUser extends Model
{
    use HasFactory;

    protected $table = 'distribution_group_user';
    public $timestamps = false;

    protected $fillable = [
        'distribution_group_id',
        'user_id',
    ];
}
