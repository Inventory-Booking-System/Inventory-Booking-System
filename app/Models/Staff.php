<?php

namespace App\Models;

class Staff extends User
{
    protected $table = 'users';

    protected static function booted(): void
    {
        static::addGlobalScope('type', fn ($q) => $q->where('users.type', 'staff'));
        static::creating(fn ($model) => $model->type = 'staff');
    }
}
