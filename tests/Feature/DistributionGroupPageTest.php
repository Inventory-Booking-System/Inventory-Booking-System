<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use Carbon\Carbon;
use App\Models\DistributionGroup;
use App\Models\Incident;
use App\Models\Location;
use App\Models\User;

class DistributionGroupPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_id_number()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_id_string()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', '#'.Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_date()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', (new Carbon(Incident::first()->start_date_time))->isoFormat('D MMM YYYY'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_time()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', (new Carbon(Incident::first()->start_date_time))->isoFormat('HH:mm'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_location()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', Incident::first()->location()->first()->name)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_equipment_issues_title()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', Incident::first()->issues()->first()->title)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_equipment_issues_quantity_string()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', 'x'.Incident::first()->issues()->first()->quantity)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_evidence()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', Incident::first()->evidence)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
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

            Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', 'C:\\test_path\\evidence.mp4')
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.$incident->id.'"');
    }

    /**
     * @test
     * @group distribution-group-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.search', Incident::first()->details)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_id_number()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.id', Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_id_string()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.id', '#'.Incident::first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_date()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.start_date_time', (new Carbon(Incident::first()->start_date_time))->isoFormat('D MMM YYYY'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_time()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.start_date_time', (new Carbon(Incident::first()->start_date_time))->isoFormat('HH:mm'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_location()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.location_id', Incident::first()->location()->first()->name)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_equipment_issues_title()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.equipment_id', Incident::first()->issues()->first()->title)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_equipment_issues_quantity_string()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.equipment_id', 'x'.Incident::first()->issues()->first()->quantity)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_evidence()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.evidence', Incident::first()->evidence)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
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

            Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.evidence', 'C:\\test_path\\evidence.mp4')
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.$incident->id.'"');
    }

    /**
     * @test
     * @group distribution-group-filter
     */
    public function filter_by_details()
    {
        $this->seed();

        Livewire::test('distribution-group.show', ['distributionGroup' => Incident::first()->group()->first()->id])
            ->set('filters.details', Incident::first()->details)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }
}
