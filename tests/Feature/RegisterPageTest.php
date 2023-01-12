<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;

class RegisterPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register_page_contains_livewire_component()
    {
        $user = User::factory()->count(1)->create()->first();
        Role::factory()->count(1)->withUser($user)->create();
        $this->actingAs($user);

        $this->get('/register')->assertSeeLivewire('auth.register');
    }

    /** @test */
    public function can_register()
    {
        $user = User::factory()->count(1)->withSuperAdmin()->create()->first();
        Role::factory()->count(1)->withUser($user)->create();
        $this->actingAs($user);

        Livewire::test('auth.register')
                ->set('password', '1234')
                ->set('passwordConfirmation', '1234')
                ->call('login')
                ->assertRedirect('/login');

        //Check database has value
        $this->assertTrue(User::where('password_set', '=', '1')->exists());

        //Check that we have authenticated
        $this->assertEquals('admin@admin123.com', auth()->user()->email);
    }

    /** @test */
    public function password_is_required()
    {
        Livewire::test('auth.register')
            ->set('password', '')
            ->set('passwordConfirmation', '1234')
            ->call('login')
            ->assertHasErrors(['password' => 'required']);
    }

    /** @test */
    public function passwordConfirmation_is_required()
    {
        Livewire::test('auth.register')
            ->set('password', '1234')
            ->set('passwordConfirmation', '')
            ->call('login')
            ->assertHasErrors(['passwordConfirmation' => 'required']);
    }

    /** @test */
    public function passwords_entered_do_not_match()
    {
        $user = User::factory()->count(1)->withSuperAdmin()->create()->first();
        Role::factory()->count(1)->withUser($user)->create();
        $this->actingAs($user);

        Livewire::test('auth.register')
                ->set('password', 'abc')
                ->set('passwordConfirmation', '123')
                ->call('login')
                ->assertHasErrors(['passwordConfirmation']);
    }
}
