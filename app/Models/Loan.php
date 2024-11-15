<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status_id', 'start_date_time', 'end_date_time', 'details', 'created_by'];

    protected $with = ['assets', 'user', 'user_created_by'];

    /**
     * An asset can belong to many loan
     */
    public function assets()
    {
        return $this->belongsToMany(Asset::class)->withPivot('returned');
    }

    public function assetGroups()
    {
        return $this->belongsToMany(AssetGroup::class)->withPivot('quantity');
    }

    /**
     * Get the user who requested the loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the loan.
     */
    public function user_created_by()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * A loan can have a setup component
     */
    public function setup()
    {
        return $this->hasOne(Setup::class);
    }

    public static function getStatusIds()
    {
        return [
            '0' => 'Booked',
            '1' => 'Reservation',
            '2' => 'Overdue',
            '3' => 'Setup',
            '4' => 'Cancelled',
            '5' => 'Completed',
            '6' => 'Modified',
        ];
    }

    public function getStatusAttribute()
    {
        return $this->getStatusIds()[$this->status_id] ?? 'Error';
    }

    public function getStatusTypeAttribute()
    {
        return [
            '0' => 'success',
            '1' => 'warning',
            '2' => 'danger',
            '3' => 'primary',
        ][$this->status_id] ?? 'secondary';
    }

    public function getStartDateTimeAttribute($value)
    {
        $date = Carbon::parse($value);

        return $date->format('d M Y H:i');
    }

    public function getEndDateTimeAttribute($value)
    {
        $date = Carbon::parse($value);

        return $date->format('d M Y H:i');
    }

    // public function getFormatDateForDatabaseAttribute()
    // {
    //     $date = Carbon::parse($this->startDateTime);
    //     return $date->format('Y-m-d H:i');
    // }

    // public function setStartDateTimeAttribute($value)
    // {
    //     $date = Carbon::parse($value);
    //     return $date->format('Y-m-d H:i:s');
    // }

    // public function setEndDateTimeAttribute($value)
    // {
    //     $date = Carbon::parse($value);
    //     return $date->format('Y-m-d H:i:s');
    // }
}
