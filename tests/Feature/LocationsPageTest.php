<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;
use App\Models\Location;

class LocationsPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function locations_page_contains_livewire_component()
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

        $this->get('/locations')->assertSeeLivewire('location.locations');
    }

    /**
     * @test
     * @group locations-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('location.locations')
            ->set('filters.search', Location::first()->name)
            ->assertDontSee('No locations found')
            ->assertSee(Location::first()->name)
            ->assertDontSee(Location::skip(1)->first()->name);
    }
}
