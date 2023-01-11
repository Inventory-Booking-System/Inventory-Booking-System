<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Models\User;
use App\Models\Role;

class SettingsPageTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function settings_page_contains_livewire_component()
    {
        //Make user
        $user = User::factory()->withPasswordSet()->create();
        Role::factory()->withUser($user)->create();

        //Perform Login
        Livewire::test('auth.login')
            ->set('email', 'admin@admin123.com')
            ->set('password', '1234')
            ->call('login')
            ->assertRedirect('/loans');

        $this->get('/settings')->assertSeeLivewire('app-settings.app-settings');
    }

    /** @test */
    public function mail_is_array()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail', 'Apples')
            ->call('save')
            ->assertHasErrors(['mail' => 'array']);
    }

    /** @test */
    public function mail_mailer_is_required()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.mailer', '')
            ->call('save')
            ->assertHasErrors(['mail.mailer' => 'required']);
    }

    /** @test */
    public function mail_mailer_is_string()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.mailer', 0)
            ->call('save')
            ->assertHasErrors(['mail.mailer' => 'string']);
    }

    /** @test */
    public function mail_mailer_is_in_set_values()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.mailer', 'Apples')
            ->call('save')
            ->assertHasErrors(['mail.mailer' => 'in:smtp,ses,mailgun,postmark,sendmail']);
    }

    /** @test */
    public function mail_host_is_required()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.host', '')
            ->call('save')
            ->assertHasErrors(['mail.host' => 'required']);
    }

    /** @test */
    public function mail_port_is_required()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.port', '')
            ->call('save')
            ->assertHasErrors(['mail.port' => 'required']);
    }

    /** @test */
    public function mail_port_is_integer()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.port', 'Apples')
            ->call('save')
            ->assertHasErrors(['mail.port' => 'integer']);
    }

    /** @test */
    public function mail_username_is_string()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.username', 0)
            ->call('save')
            ->assertHasErrors(['mail.username' => 'string']);
    }

    /** @test */
    public function mail_password_is_string()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.password', 0)
            ->call('save')
            ->assertHasErrors(['mail.password' => 'string']);
    }

    /** @test */
    public function mail_encryption_is_string()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.encryption', 0)
            ->call('save')
            ->assertHasErrors(['mail.encryption' => 'string']);
    }

    /** @test */
    public function mail_encryption_in_set_values()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.encryption', 0)
            ->call('save')
            ->assertHasErrors(['mail.encryption' => 'in:ssl,tls,starttls,null']);
    }

    /** @test */
    public function mail_from_address_is_required()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.from_address', '')
            ->call('save')
            ->assertHasErrors(['mail.from_address' => 'required']);
    }

    /** @test */
    public function mail_from_address_is_email()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.from_address', 'Apples')
            ->call('save')
            ->assertHasErrors(['mail.from_address' => 'email']);
    }

    /** @test */
    public function mail_cc_address_is_email()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('mail.cc_address', 'Apples')
            ->call('save')
            ->assertHasErrors(['mail.cc_address' => 'email']);
    }

    /** @test */
    public function notification_overdue_emails_is_boolean()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('notification.overdue_emails', 'Apples')
            ->call('save')
            ->assertHasErrors(['notification.overdue_emails' => 'boolean']);
    }

    /** @test */
    public function notification_setup_emails_is_boolean()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('app-settings.app-settings')
            ->set('notification.setup_emails', 'Apples')
            ->call('save')
            ->assertHasErrors(['notification.setup_emails' => 'boolean']);
    }
}
