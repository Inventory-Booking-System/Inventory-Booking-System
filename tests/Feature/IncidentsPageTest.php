<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;

class IncidentsPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function incidents_page_contains_livewire_component()
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

        $this->get('/incidents')->assertSeeLivewire('incident.incidents');
    }
}
