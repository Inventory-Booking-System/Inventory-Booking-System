<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;

class UsersPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_page_contains_livewire_component()
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

        $this->get('/users')->assertSeeLivewire('user.users');
    }

    /**
     * @test
     * @group users-search
     */
    public function search_by_forename()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.search', User::first()->forename)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"');
    }

    /**
     * @test
     * @group users-search
     */
    public function search_by_surname()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.search', User::first()->surname)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"');
    }

    /**
     * @test
     * @group users-search
     */
    public function search_by_full_name()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.search', User::first()->forename.' '.User::first()->surname)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"');
    }

    /**
     * @test
     * @group users-search
     */
    public function search_by_email()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.search', User::first()->email)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"');
    }

    /**
     * @test
     * @group users-filter
     */
    public function filter_by_forename()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.user_id', User::first()->forename)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"')
            ->assertDontSeeHtml('"/users/'.User::skip(1)->first()->id.'"');
    }

    /**
     * @test
     * @group users-filter
     */
    public function filter_by_surname()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.user_id', User::first()->surname)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"')
            ->assertDontSeeHtml('"/users/'.User::skip(1)->first()->id.'"');
    }

    /**
     * @test
     * @group users-filter
     */
    public function filter_by_full_name()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.user_id', User::first()->forename.' '.User::first()->surname)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"')
            ->assertDontSeeHtml('"/users/'.User::skip(1)->first()->id.'"');
    }

    /**
     * @test
     * @group users-filter
     */
    public function filter_by_email()
    {
        $this->seed();

        Livewire::test('user.users')
            ->set('filters.email', User::first()->email)
            ->assertDontSee('No users found')
            ->assertSeeHtml('"/users/'.User::first()->id.'"')
            ->assertDontSeeHtml('"/users/'.User::skip(1)->first()->id.'"');
    }
}
