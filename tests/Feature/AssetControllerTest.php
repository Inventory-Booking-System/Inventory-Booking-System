<?php
 
namespace Tests\Feature;
 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;
use App\Models\Loan;
use App\Models\Setup;
use App\Models\Location;
use Carbon\Carbon;
 
class AssetControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group asset-controller
     */
    public function requiresAuthentication(): void
    {
        $this->seed();

        $response = $this->get('/assets');
        $response->assertStatus(302);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function requiresStartDateTime(): void
    {
        $this->seed();

        $response = $this->actingAs(User::first())->get('/api/assets?endDateTime='.time());
        $response->assertStatus(400);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function requiresEndDateTime(): void
    {
        $this->seed();

        $response = $this->actingAs(User::first())->get('/api/assets?startDateTime='.time());
        $response->assertStatus(400);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function returnsListOfAssets(): void
    {
        $this->seed();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.time().'&endDateTime='.time()+60*100);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json->has(Asset::count())
                    ->first(fn (AssertableJson $json) =>
                        $json->has('id')
                            ->has('name')
                            ->has('available')
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenNoLoans(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenLoanBooked(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(0)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenLoanReserved(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(1)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenLoanOverdue(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(2)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenSetup(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $setupLoan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(3)
            ->create()
            ->first();
        $setupLoan->assets()->attach(Asset::first());
        Setup::factory()
            ->count(1)
            ->withLoan($setupLoan)
            ->withLocation(Location::first())
            ->create();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenCancelled(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(4)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenCompleted(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(5)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenLoanBookedAndAssetReturned(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(5)
            ->create()
            ->first();
        
        $loan->assets()->attach(Asset::first());
        
        // Mark the asset as returned
        $ids = [];
        $ids[Asset::first()->id] = ['returned' => 1];
        $loan->assets()->sync($ids);

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenSetupAndAssetReturned(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $setupLoan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(3)
            ->create()
            ->first();
        $setupLoan->assets()->attach(Asset::first());
        Setup::factory()
            ->count(1)
            ->withLoan($setupLoan)
            ->withLocation(Location::first())
            ->create();

        // Mark the asset as returned
        $ids = [];
        $ids[Asset::first()->id] = ['returned' => 1];
        $setupLoan->assets()->sync($ids);

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryStartBetweenLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(0)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->add(30, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(30, 'minute')->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryEndBetweenLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(0)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->subtract(30, 'minute')->timestamp.'&endDateTime='.$endDateTime->subtract(30, 'minute')->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryStartAndEndOutsideLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(0)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->subtract(30, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(30, 'minute')->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryStartAndEndInsideLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::now()->add(1, 'day');
        $endDateTime = Carbon::now()->add(1, 'day')->add(1, 'hour');

        $loan = Loan::factory()
            ->count(1)
            ->withUser(User::first())
            ->withCreator(User::first())
            ->withStartDateTime($startDateTime)
            ->withEndDateTime($endDateTime)
            ->withStatusId(0)
            ->create()
            ->first();
        $loan->assets()->attach(Asset::first());

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->add(10, 'minute')->timestamp.'&endDateTime='.$endDateTime->subtract(10, 'minute')->timestamp);
        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->has('0', fn (AssertableJson $json) =>
                        $json->where('available', false)
                            ->etc()
                    )
                    ->has('1', fn (AssertableJson $json) =>
                        $json->where('available', true)
                            ->etc()
                    )
            );
    }
}