<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;
use App\Models\DistributionGroup;

class DistributionGroupsPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function distribution_groups_page_contains_livewire_component()
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

        $this->get('/distributionGroups')->assertSeeLivewire('distribution-group.distribution-groups');
    }

    /**
     * @test
     * @group distribution-groups-search
     */
    public function search_by_name()
    {
        $this->seed();

        Livewire::test('distribution-group.distribution-groups')
            ->set('filters.search', DistributionGroup::first()->name)
            ->assertDontSee('No distribution groups found')
            ->assertSeeHtml('"/distributionGroups/'.DistributionGroup::first()->id.'"')
            ->assertDontSeeHtml('"/distributionGroups/'.DistributionGroup::skip(1)->first()->id.'"');
    }

    /**
     * @test
     * @group distribution-groups-search
     */
    public function search_by_user_forename()
    {
        $this->seed();

        Livewire::test('distribution-group.distribution-groups')
            ->set('filters.search', DistributionGroup::first()->users()->first()->forename)
            ->assertDontSee('No distribution groups found')
            ->assertSeeHtml('"/distributionGroups/'.DistributionGroup::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-groups-search
     */
    public function search_by_user_surname()
    {
        $this->seed();

        Livewire::test('distribution-group.distribution-groups')
            ->set('filters.search', DistributionGroup::first()->users()->first()->surname)
            ->assertDontSee('No distribution groups found')
            ->assertSeeHtml('"/distributionGroups/'.DistributionGroup::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-groups-search
     */
    public function search_by_user_full_name()
    {
        $this->seed();

        Livewire::test('distribution-group.distribution-groups')
            ->set('filters.search', DistributionGroup::first()->users()->first()->forename.' '.DistributionGroup::first()->users()->first()->surname)
            ->assertDontSee('No distribution groups found')
            ->assertSeeHtml('"/distributionGroups/'.DistributionGroup::first()->id.'"');
    }
}
