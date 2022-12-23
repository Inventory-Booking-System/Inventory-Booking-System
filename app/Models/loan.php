<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status_id', 'start_date_time', 'end_date_time', 'details'];

    protected $with = ['assets'];

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

    public function getStatusAttribute()
    {
        return [
            '0' => 'Booked',
            '1' => 'Reservation',
            '2' => 'Overdue',
            '3' => 'Setup',
            '4' => 'Cancelled',
            '5' => 'Completed',
            '6' => 'Modified',
        ][$this->status_id] ?? 'Error';
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

    public function getEndDateTimeAttribute($value2)
    {
        $date2 = Carbon::parse($value2);

        return $date2->format('d M Y H:i');
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
