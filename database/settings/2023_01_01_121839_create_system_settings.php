<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

class CreateSystemSettings extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('system.mailer', 'smtp');
        $this->migrator->add('system.host', 'smtp.mailtrap.io');
        $this->migrator->add('system.port', '2525');
        $this->migrator->add('system.username', 'd6d527bbf02a23');
        $this->migrator->add('system.password', '094b9677043c9b');
        $this->migrator->add('system.encryption', 'tls');
        $this->migrator->add('system.from_address', 'tinydeer@maildrop.cc');
    }
}
