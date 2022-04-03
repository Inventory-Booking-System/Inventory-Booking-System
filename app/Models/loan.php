<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class loan extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status_id', 'start_date', 'end_date', 'start_time', 'end_time', 'details'];
}
