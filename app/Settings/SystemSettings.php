<?php

namespace App\Settings;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelSettings\Settings;

class SystemSettings extends Settings
{
    public string $mailer;
    public string $host;
    public string $port;
    public string $username;
    public string $password;
    public string $encryption;
    public string $from_address;

    public static function group(): string
    {
        return 'system';
    }
}