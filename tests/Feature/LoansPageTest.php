<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Role;
use App\Models\Loan;
use App\Models\Asset;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class LoansPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function loans_page_contains_livewire_component()
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

        $this->get('/loans')->assertSeeLivewire('loan.loans');
    }

    /** @test */
    public function can_open_create_loan_model()
    {
        $this->actingAs(User::factory()->create());

        Livewire::test('loan.loans')
            ->call('showModal')
            ->assertEmitted('showModal');
    }

    /** @test */
    public function can_create_loan()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', $id)
            ->set('editing.status_id', 0)
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', "This is a test loan")
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasNoErrors();
    }

    /** @test */
    public function can_see_created_loan_in_table()
    {
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;
        Asset::factory()->create();

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', $id)
            ->set('editing.status_id', 0)
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', "This is a test loan")
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertSee(Loan::first()->id);
    }

    /** @test */
    public function user_id_is_required()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.status_id', 0)
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', "This is a test loan")
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasErrors(['editing.user_id' => 'required']);
    }

    /** @test */
    public function user_id_is_integer()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', 'Apples')
            ->set('editing.status_id', 0)
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', "This is a test loan")
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasErrors(['editing.user_id' => 'integer']);
    }

    /** @test */
    public function status_id_is_required()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', 0)
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', "This is a test loan")
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasErrors(['editing.status_id' => 'required']);
    }

    /** @test */
    public function status_id_is_integer()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', 0)
            ->set('editing.status_id', 'Apples')
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', "This is a test loan")
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasErrors(['editing.status_id' => 'integer']);
    }

    /** @test */
    public function status_id_is_0_or_1()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', 0)
            ->set('editing.status_id', '3')
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', "This is a test loan")
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasErrors(['editing.status_id' => 'in:0,1']);
    }

    /** @test */
    public function details_is_string()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', 0)
            ->set('editing.status_id', '3')
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', 0)
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasErrors(['editing.details' => 'string']);
    }

    /** @test */
    public function details_can_be_null()
    {
        Artisan::call('db:seed');
        $this->actingAs(User::factory()->create());
        $id = User::first()->id;

        //Create Loan
        Livewire::test('loan.loans')
            ->set('editing.user_id', 0)
            ->set('editing.status_id', '3')
            ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
            ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
            ->set('editing.details', null)
            ->set('equipment_id', Asset::first()->id)
            ->call('save')
            ->assertHasNoErrors(['editing.details' => 'nullable']);
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_id_number()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', Loan::first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_id_string()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', '#'.Loan::first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_user_forename()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', User::skip(1)->first()->forename)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.User::skip(1)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_user_surname()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', User::skip(1)->first()->surname)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.User::skip(1)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_user_full_name()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', User::skip(1)->first()->forename.' '.User::skip(1)->first()->surname)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.User::skip(1)->first()->loans()->first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.User::skip(2)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_status()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', 'booked')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_partial_status()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', 'book')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_date()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', (new Carbon(Loan::first()->start_date_time))->isoFormat('D MMM YYYY'))
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_time()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', (new Carbon(Loan::first()->start_date_time))->isoFormat('HH:mm'))
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_details()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', Loan::first()->details)
            ->assertDontSee('No loans found')
            ->assertSee(Loan::first()->details);
    }

    /**
     * @test
     * @group loans-searcha
     */
    public function search_by_asset_name()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', Loan::first()->assets()->first()->name)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_asset_tag()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', Loan::first()->assets()->first()->tag)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group loans-search
     */
    public function search_by_asset_name_and_tag()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.search', Loan::first()->assets()->first()->name.' ('.Loan::first()->assets()->first()->tag.')')
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group loans-filter
     */
    public function filter_by_id_number()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.id', Loan::first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group loans-filter
     */
    public function filter_by_id_string()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.id', '#'.Loan::first()->id)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.Loan::first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.Loan::skip(2)->first()->id.'"');
    }

    /**
     * @test
     * @group loans-filter
     */
    public function filter_by_user_forename()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.user_id', User::skip(1)->first()->forename)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.User::skip(1)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group loans-filter
     */
    public function filter_by_user_surname()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.user_id', User::skip(1)->first()->surname)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.User::skip(1)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group loans-filter
     */
    public function filter_by_user_full_name()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.user_id', User::skip(1)->first()->forename.' '.User::skip(1)->first()->surname)
            ->assertDontSee('No loans found')
            ->assertSeeHtml('"/loans/'.User::skip(1)->first()->loans()->first()->id.'"')
            ->assertDontSeeHtml('"/loans/'.User::skip(2)->first()->loans()->first()->id.'"');
    }

    /**
     * @test
     * @group loans-filter
     */
    public function filter_by_status()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.status_id', 'booked')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    /**
     * @test
     * @group loans-filter
     */
    public function filter_by_partial_status()
    {
        $this->seed();

        Livewire::test('loan.loans')
            ->set('filters.status_id', 'book')
            ->assertDontSee('No loans found')
            ->assertSee('Booked');
    }

    // /** @test */
    // public function equipment_id_exists_in_assets_table()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
    //         ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.details', 0)
    //         ->set('equipment_id', 0)
    //         ->call('save')
    //         ->assertHasErrors(['editing.equipment_id' => 'exists:assets,id']);
    // }

    // /** @test */
    // public function equipment_id_is_integer()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:14'))
    //         ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.details', 0)
    //         ->set('equipment_id', "Apples")
    //         ->call('save')
    //         ->assertHasErrors(['editing.equipment_id' => 'integer']);
    // }


    // /** @test */
    // public function end_date_time_is_required()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.end_date_time', '')
    //         ->set('editing.details', "This is a test loan")
    //         ->set('equipment_id', Asset::first()->id)
    //         ->call('save')
    //         ->assertHasErrors(['editing.end_date_time' => 'required']);
    // }

    // /** @test */
    // public function end_date_time_is_date()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.end_date_time', "Apples")
    //         ->set('editing.details', "This is a test loan")
    //         ->set('equipment_id', Asset::first()->id)
    //         ->call('save')
    //         ->assertHasErrors(['editing.end_date_time' => 'date']);
    // }

    // /** @test */
    // public function end_date_time_is_after_start_date_time()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', Carbon::parse('11 Jan 2023 13:14'))
    //         ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.details', "This is a test loan")
    //         ->set('equipment_id', Asset::first()->id)
    //         ->call('save')
    //         ->assertHasErrors(['editing.end_date_time' => 'after:editing.start_date_time']);
    // }

    // /** @test */
    // public function start_date_time_is_required()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', '')
    //         ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.details', "This is a test loan")
    //         ->set('equipment_id', Asset::first()->id)
    //         ->call('save')
    //         ->assertHasErrors(['editing.start_date_time' => 'required']);
    // }

    // /** @test */
    // public function start_date_time_is_date()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', "Apples")
    //         ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.details', "This is a test loan")
    //         ->set('equipment_id', Asset::first()->id)
    //         ->call('save')
    //         ->assertHasErrors(['editing.start_date_time' => 'date']);
    // }

    // /** @test */
    // public function start_date_time_is_before_end_date_time()
    // {
    //     Artisan::call('db:seed');
    //     $this->actingAs(User::factory()->create());
    //     $id = User::first()->id;

    //     //Create Loan
    //     Livewire::test('loan.loans')
    //         ->set('editing.user_id', 0)
    //         ->set('editing.status_id', '3')
    //         ->set('editing.start_date_time', Carbon::parse('11 Jan 2023 13:14'))
    //         ->set('editing.end_date_time', Carbon::parse('10 Jan 2023 13:15'))
    //         ->set('editing.details', "This is a test loan")
    //         ->set('equipment_id', Asset::first()->id)
    //         ->call('save')
    //         ->assertHasErrors(['editing.start_date_time' => 'before:editing.end_date_time']);
    // }
}
