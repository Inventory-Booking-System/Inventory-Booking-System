<?php

namespace App\Models;

class Student extends User
{
    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('type', fn ($q) => $q->where('users.type', 'student'));
        static::creating(fn ($model) => $model->type = 'student');
    }
}
