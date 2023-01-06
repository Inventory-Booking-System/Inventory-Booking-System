<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_page_contains_livewire_component()
    {
        $this->get('/login')->assertSeeLivewire('auth.login');
    }

    /** @test */
    public function can_login()
    {
        //Make admin user
        $user = User::factory()->count(1)->withSuperAdmin()->create()->first();
        Role::factory()->count(1)->withUser($user)->create();

        //Perform Login
        Livewire::test('auth.login')
            ->set('email', 'admin@admin123.com')
            ->set('password', '1234')
            ->call('login')
            ->assertRedirect('/loans');

        //Check database has value
        $this->assertTrue(User::whereEmail('admin@admin123.com')->exists());

        //Check that we have authenticated
        $this->assertEquals('admin@admin123.com', auth()->user()->email);
    }

    /** @test */
    public function email_is_required()
    {
        Livewire::test('auth.login')
            ->set('email', '')
            ->set('password', '1234')
            ->call('login')
            ->assertHasErrors(['email' => 'required']);
    }

    /** @test */
    public function email_is_valid_email()
    {
        Livewire::test('auth.login')
            ->set('email', 'apples')
            ->set('password', '1234')
            ->call('login')
            ->assertHasErrors(['email' => 'email']);
    }

    /** @test */
    public function password_is_required()
    {
        Livewire::test('auth.login')
            ->set('email', 'admin@admin123.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function login_details_entered_incorrect()
    {
        //Make admin user
        $user = User::factory()->count(1)->withSuperAdmin()->create()->first();
        Role::factory()->count(1)->withUser($user)->create();

        Livewire::test('auth.login')
            ->set('email', 'admin@admin123.com')
            ->set('password', 'letmein')
            ->call('login')
            ->assertHasErrors(['email']);
    }
}
