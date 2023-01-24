<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Setup;

class SetupsPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function setups_page_contains_livewire_component()
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

        $this->get('/setups')->assertSeeLivewire('setup.setups');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_id_number()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Setup::skip(1)->first()->id)
            ->assertDontSee('No setups found')
            ->assertSeeHtml('"/setups/'.Setup::skip(1)->first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.Setup::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_id_string()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', '#'.Setup::skip(1)->first()->id.' '.Setup::skip(1)->first()->title)
            ->assertDontSee('No setups found')
            ->assertSeeHtml('"/setups/'.Setup::skip(1)->first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.Setup::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_forename()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', User::skip(1)->first()->forename)
            ->assertDontSee('No setups found')
            /**
             * Skip the Super Admin user
             * The user's first loan is a real loan, second loan is a setup
             * Get the ID from the setup, not it's associated loan
             */
            ->assertSeeHtml('"/setups/'.User::skip(1)->first()->loans()->skip(1)->first()->setup()->first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.User::skip(2)->first()->loans()->skip(1)->first()->setup()->first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_surname()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', User::skip(1)->first()->surname)
            ->assertDontSee('No setups found')
            /**
             * Skip the Super Admin user
             * The user's first loan is a real loan, second loan is a setup
             * Get the ID from the setup, not it's associated loan
             */
            ->assertSeeHtml('"/setups/'.User::skip(1)->first()->loans()->skip(1)->first()->setup()->first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.User::skip(2)->first()->loans()->skip(1)->first()->setup()->first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_full_name()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', User::skip(1)->first()->forename.' '.User::skip(1)->first()->surname)
            ->assertDontSee('No setups found')
            /**
             * Skip the Super Admin user
             * The user's first loan is a real loan, second loan is a setup
             * Get the ID from the setup, not it's associated loan
             */
            ->assertSeeHtml('"/setups/'.User::skip(1)->first()->loans()->skip(1)->first()->setup()->first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.User::skip(2)->first()->loans()->skip(1)->first()->setup()->first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_location()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Setup::first()->location()->first()->name)
            ->assertDontSee('No setups found')
            ->assertSeeHtml('"/setups/'.Setup::first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_date()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Carbon::now()->isoFormat('D MMM YYYY'))
            ->assertDontSee('No setups found')
            ->assertSee(Carbon::now()->isoFormat('D MMM YYYY'));
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_time()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Carbon::now()->isoFormat('HH:mm'))
            ->assertDontSee('No setups found')
            ->assertSee(Carbon::now()->isoFormat('HH:mm'));
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Setup::first()->loan()->first()->details)
            ->assertDontSee('No setups found')
            ->assertSee(Setup::first()->loan()->first()->details);
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_asset_name()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Setup::first()->loan()->first()->assets()->first()->name)
            ->assertDontSee('No setups found')
            ->assertSeeHtml('"/setups/'.Setup::first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.Setup::skip(1)->first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_asset_tag()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Setup::first()->loan()->first()->assets()->first()->tag)
            ->assertDontSee('No setups found')
            ->assertSeeHtml('"/setups/'.Setup::first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.Setup::skip(1)->first()->id.'"');
    }

    /**
     * @test
     * @group setups-search
     */
    public function search_by_asset_name_and_tag()
    {
        $this->seed();

        Livewire::test('setup.setups')
            ->set('filters.search', Setup::first()->loan()->first()->assets()->first()->name.' ('.Setup::first()->loan()->first()->assets()->first()->tag.')')
            ->assertDontSee('No setups found')
            ->assertSeeHtml('"/setups/'.Setup::first()->id.'"')
            ->assertDontSeeHtml('"/setups/'.Setup::skip(1)->first()->id.'"');
    }
}
