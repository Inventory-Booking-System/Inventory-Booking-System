<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Livewire\Livewire;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;
use App\Models\Loan;
use App\Models\Asset;

class AssetPageTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function assets_page_contains_livewire_component()
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

        $this->get('/assets')->assertSeeLivewire('asset.show', ['asset' => Asset::first()->id]);
    }

    /**
     * @test
     * @group asset-search
     */
    public function search_by_id_number()
    {
        $this->seed();

        Livewire::test('asset.show', ['asset' => Asset::first()->id])
            ->set('filters.search', Asset::first()->loans()->first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Asset::first()->loans()->first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Asset::skip(1)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group asset-search
     */
    public function search_by_id_string()
    {
        $this->seed();

        Livewire::test('asset.show', ['asset' => Asset::first()->id])
            ->set('filters.search', '#'.Asset::first()->loans()->first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Asset::first()->loans()->first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Asset::skip(1)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group asset-search
     */
    public function search_by_status()
    {
        $this->seed();

        Livewire::test('asset.show', ['asset' => Asset::first()->id])
            ->set('filters.search', 'booked')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    /**
     * @test
     * @group asset-search
     */
    public function search_by_partial_status()
    {
        $this->seed();

        Livewire::test('asset.show', ['asset' => Asset::first()->id])
            ->set('filters.search', 'book')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    /**
     * @test
     * @group asset-search
     */
    public function search_by_date()
    {
        $this->seed();

        Livewire::test('asset.show', ['asset' => Asset::first()->id])
            ->set('filters.search', Carbon::now()->isoFormat('D MMM YYYY'))
            ->assertDontSee('No loans found')
            ->assertSee(Carbon::now()->isoFormat('D MMM YYYY'));
    }

    /**
     * @test
     * @group asset-search
     */
    public function search_by_time()
    {
        $this->seed();

        Livewire::test('asset.show', ['asset' => Asset::first()->id])
            ->set('filters.search', Carbon::now()->isoFormat('HH:mm'))
            ->assertDontSee('No loans found')
            ->assertSee(Carbon::now()->isoFormat('HH:mm'));
    }

    /**
     * @test
     * @group asset-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('asset.show', ['asset' => Asset::first()->id])
            ->set('filters.search', Loan::first()->details)
            ->assertDontSee('No loans found')
            ->assertSee(Loan::first()->details);
    }
}
