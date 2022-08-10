<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use App\Http\Livewire\Asset\Create;
use App\Models\Asset;

class CreateAssetTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_see_livewire_component_on_page()
    {
        $this->get('/assets/create')
            ->assertSuccessful()
            ->assertSeeLivewire(Create::class);
    }

    /** @test */
    function can_create_asset()
    {
        $asset = Livewire::test(Create::class)
                ->set('name', 'Box of Headphones')
                ->set('tag', 1234)
                ->set('description', 'A Box of Headphones!')
                ->call('save');

        dd($asset->id());

        $this->assertDatabaseHas('assets', ['tag' => 1234, 'name' => 'Box of Headphones']);
    }
}
