<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Livewire\Livewire;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\User;

class UserPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group user-search
     */
    public function search_by_id_number()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_id_string()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', '#'.Loan::first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_user_forename()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->user()->first()->forename)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_user_surname()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->user()->first()->surname)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_user_full_name()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->user()->first()->forename.' '.Loan::first()->user()->first()->surname)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_status()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', 'booked')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_partial_status()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', 'book')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_date()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Carbon::now()->isoFormat('D MMM YYYY'))
            ->assertDontSee('No loans found')
            ->assertSee(Carbon::now()->isoFormat('D MMM YYYY'));
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_time()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Carbon::now()->isoFormat('HH:mm'))
            ->assertDontSee('No loans found')
            ->assertSee(Carbon::now()->isoFormat('HH:mm'));
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->details)
            ->assertDontSee('No loans found')
            ->assertSee(Loan::first()->details);
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_asset_name()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->assets()->first()->name)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_asset_tag()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->assets()->first()->tag)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group user-search
     */
    public function search_by_asset_name_and_tag()
    {
        $this->seed();

        Livewire::test('user.show', ['user' => Loan::first()->user()->first()->id])
            ->set('filters.search', Loan::first()->assets()->first()->name.' ('.Loan::first()->assets()->first()->tag.')')
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }
}
