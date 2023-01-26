<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use Carbon\Carbon;
use App\Models\EquipmentIssue;
use App\Models\Incident;
use App\Models\DistributionGroup;

class EquipmentIssuePageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_id_number()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', EquipmentIssue::first()->incidents()->first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_id_string()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', '#'.EquipmentIssue::first()->incidents()->first()->id)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_distribution_group()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', DistributionGroup::find(EquipmentIssue::first()->incidents()->first()->distribution_id)->name)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_status()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', 'outstanding')
            ->assertDontSee('No loans found')
            ->assertSee('Outstanding');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_partial_status()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', 'outstand')
            ->assertDontSee('No loans found')
            ->assertSee('Outstanding');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_date()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', (new Carbon(EquipmentIssue::first()->incidents()->first()->start_date_time))->isoFormat('D MMM YYYY'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_time()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', (new Carbon(EquipmentIssue::first()->incidents()->first()->start_date_time))->isoFormat('HH:mm'))
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', Incident::first()->details)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.Incident::first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_equipment_issues_title()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', EquipmentIssue::first()->incidents()->first()->issues()->first()->title)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_equipment_issues_quantity_string()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', 'x'.EquipmentIssue::first()->incidents()->first()->issues()->first()->pivot->quantity)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_equipment_issues_cost()
    {
        $this->seed();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', EquipmentIssue::first()->incidents()->first()->issues()->first()->cost)
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issue-search
     */
    public function search_by_equipment_issues_full_string()
    {
        $this->seed();

        $issue = EquipmentIssue::first()->incidents()->first()->issues()->first();

        Livewire::test('equipment-issue.show', ['equipmentIssue' => EquipmentIssue::first()->id])
            ->set('filters.search', 'x'.$issue->pivot->quantity.' '.$issue->title.' (£'.$issue->cost.')')
            ->assertDontSee('No incidents found')
            ->assertSeeHtml('"/incidents/'.EquipmentIssue::first()->incidents()->first()->id.'"');
    }
}
