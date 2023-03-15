<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Carbon\Carbon;
use Livewire\Livewire;
use App\Models\User;
use App\Models\Role;
use App\Models\Incident;
use App\Models\DistributionGroup;
use App\Models\Location;

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

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_id_number()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_id_string()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', '#'.Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_date()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', (new Carbon(Incident::first()->start_date_time))->isoFormat('D MMM YYYY'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_time()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', (new Carbon(Incident::first()->start_date_time))->isoFormat('HH:mm'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_location()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', Incident::first()->location()->first()->name)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_distribution_group()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', DistributionGroup::find(Incident::first()->distribution_id)->name)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_equipment_issues_title()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', Incident::first()->issues()->first()->title)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_equipment_issues_quantity_string()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', 'x'.Incident::first()->issues()->first()->quantity)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_evidence()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', Incident::first()->evidence)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_evidence_windows_path()
    {
        $this->seed();

        $incident = Incident::factory()
            ->withLocation(Location::first())
            ->withDistributionGroup(DistributionGroup::first())
            ->withCreator(User::first())
            ->withEvidence('C:\\test_path\\evidence.mp4')
            ->create();

        Livewire::test('incident.incidents')
            ->set('filters.search', 'C:\\test_path\\evidence.mp4')
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.$incident->id.'"');
    }

    /**
     * @test
     * @group incidents-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.search', Incident::first()->details)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_id_number()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.id', Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_id_string()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.id', '#'.Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_date()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.start_date_time', (new Carbon(Incident::first()->start_date_time))->isoFormat('D MMM YYYY'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_time()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.start_date_time', (new Carbon(Incident::first()->start_date_time))->isoFormat('HH:mm'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_location()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.location_id', Incident::first()->location()->first()->name)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_distribution_group()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.distribution_id', DistributionGroup::find(Incident::first()->distribution_id)->name)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_equipment_issues_title()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.equipment_id', Incident::first()->issues()->first()->title)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_equipment_issues_quantity_string()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.equipment_id', 'x'.Incident::first()->issues()->first()->quantity)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_evidence()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.evidence', Incident::first()->evidence)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_evidence_windows_path()
    {
        $this->seed();

        $incident = Incident::factory()
            ->withLocation(Location::first())
            ->withDistributionGroup(DistributionGroup::first())
            ->withCreator(User::first())
            ->withEvidence('C:\\test_path\\evidence.mp4')
            ->create();

        Livewire::test('incident.incidents')
            ->set('filters.evidence', 'C:\\test_path\\evidence.mp4')
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.$incident->id.'"');
    }

    /**
     * @test
     * @group incidents-filter
     */
    public function filter_by_details()
    {
        $this->seed();

        Livewire::test('incident.incidents')
            ->set('filters.details', Incident::first()->details)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }
}
