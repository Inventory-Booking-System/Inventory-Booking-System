<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;
use App\Models\EquipmentIssue;

class EquipmentIssuesPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function equipment_issues_page_contains_livewire_component()
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

        $this->get('/equipmentIssues')->assertSeeLivewire('equipment-issue.equipment-issues');
    }

    /**
     * @test
     * @group equipment-issues-search
     */
    public function search_by_title()
    {
        $this->seed();

        Livewire::test('equipment-issue.equipment-issues')
            ->set('filters.search', EquipmentIssue::first()->title)
            ->assertDontSee('No equipment issues found')
            ->assertSeeHtml('"/equipmentIssues/'.EquipmentIssue::first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issues-search
     */
    public function search_by_cost()
    {
        $this->seed();

        Livewire::test('equipment-issue.equipment-issues')
            ->set('filters.search', EquipmentIssue::first()->cost)
            ->assertDontSee('No equipment issues found')
            ->assertSeeHtml('"/equipmentIssues/'.EquipmentIssue::first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issues-filter
     */
    public function filter_by_title()
    {
        $this->seed();

        Livewire::test('equipment-issue.equipment-issues')
            ->set('filters.title', EquipmentIssue::first()->title)
            ->assertDontSee('No equipment issues found')
            ->assertSeeHtml('"/equipmentIssues/'.EquipmentIssue::first()->id.'"');
    }

    /**
     * @test
     * @group equipment-issues-filter
     */
    public function filter_by_cost()
    {
        $this->seed();

        Livewire::test('equipment-issue.equipment-issues')
            ->set('filters.cost', EquipmentIssue::first()->cost)
            ->assertDontSee('No equipment issues found')
            ->assertSeeHtml('"/equipmentIssues/'.EquipmentIssue::first()->id.'"');
    }
}
