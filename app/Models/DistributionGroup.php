<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\DistributionGroupUser;

class DistributionGroup extends Model
{
    use HasFactory, SoftDeletes;

    public const POS_ACCESS_DISABLED = -1;
    public const POS_ACCESS_ENABLED = 1;

    protected $casts = [
        'pos_student_screen_access' => 'integer',
    ];

    public static function posAccessLabel($value)
    {
        if ((int) $value === self::POS_ACCESS_ENABLED) {
            return 'Enabled';
        }

        if ((int) $value === self::POS_ACCESS_DISABLED) {
            return 'Disabled';
        }

        return '-';
    }

    /**
     * A distribution group belongs to many users.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'distribution_group_user', 'distribution_group_id', 'user_id');
    }
}
