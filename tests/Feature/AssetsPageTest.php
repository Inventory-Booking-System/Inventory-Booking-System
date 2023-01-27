<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;
use App\Models\Asset;

class AssetsPageTest extends TestCase
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

        $this->get('/assets')->assertSeeLivewire('asset.assets');
    }

    /**
     * @test
     * @group assets-search
     */
    public function search_by_name()
    {
        $this->seed();

        Livewire::test('asset.assets')
            ->set('filters.search', Asset::first()->name)
            ->assertDontSee('No assets found')
            ->assertSeeHtml('"/assets/'.Asset::first()->id.'"');
    }

    /**
     * @test
     * @group assets-search
     */
    public function search_by_tag()
    {
        $this->seed();

        Livewire::test('asset.assets')
            ->set('filters.search', Asset::first()->tag)
            ->assertDontSee('No assets found')
            ->assertSeeHtml('"/assets/'.Asset::first()->id.'"');
    }

    /**
     * @test
     * @group assets-search
     */
    public function search_by_description()
    {
        $this->seed();

        Livewire::test('asset.assets')
            ->set('filters.search', Asset::first()->description)
            ->assertDontSee('No assets found')
            ->assertSeeHtml('"/assets/'.Asset::first()->id.'"')
            ->assertDontSeeHtml('"/assets/'.Asset::skip(1)->first()->id.'"');
    }
}
