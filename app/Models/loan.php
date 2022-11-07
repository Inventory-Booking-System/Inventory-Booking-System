<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status_id', 'start_date_time', 'end_date_time', 'details'];

    protected $dates = ['start_date_time'];

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

    public function getStartDateForHumansAttribute()
    {
        return $this->start_date_time->format('d M Y h:ia');
    }

    public function getEndDateForHumansAttribute()
    {
        return $this->start_date_time->format('d M Y h:ia');
    }
}
