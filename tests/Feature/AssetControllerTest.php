<?php
 
namespace Tests\Feature;
 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;
use App\Models\Loan;
use App\Models\Setup;
use App\Models\Location;
use Carbon\Carbon;
use Throwable;
 
class AssetControllerTest extends TestCase
{
    use RefreshDatabase;

    private $testLoanString;
    private $responseString;

    /**
     * @test
     * @group asset-controller
     */
    public function requiresAuthentication(): void
    {
        $this->seed();

        $response = $this->get('/assets');
        $this->responseString = $response->getContent();
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
        $this->responseString = $response->getContent();
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
        $this->responseString = $response->getContent();
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
        $this->responseString = $response->getContent();

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
     * @group failing
     */
    public function availableWhenNoLoans(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertTrue($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenLoanBooked(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenLoanReserved(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenLoanOverdue(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->add(1, 'day')->timestamp.'&endDateTime='.$endDateTime->add(1, 'day')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenSetup(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($setupLoan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenCancelled(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertTrue($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenCompleted(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertTrue($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenLoanBookedAndAssetReturned(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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

        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertTrue($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenSetupAndAssetReturned(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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

        $this->testLoanString = Loan::with('assets')->find($setupLoan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->timestamp.'&endDateTime='.$endDateTime->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertTrue($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryStartBetweenLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->add(30, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(30, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryEndBetweenLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->subtract(30, 'minute')->timestamp.'&endDateTime='.$endDateTime->subtract(30, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryStartAndEndOutsideLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->subtract(30, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(30, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function notAvailableWhenQueryStartAndEndInsideLoanStartAndEnd(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$startDateTime->add(10, 'minute')->timestamp.'&endDateTime='.$endDateTime->subtract(10, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     * 
     * If a reservation end time is before the current time, but it hasn't been 
     * cancelled/booked out, the assets should not be bookable
     */
    public function notAvailableWhenReservationExpiresButNotCancelled(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');
        $this->travelTo($endDateTime->copy()->add(1, 'hour'));

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
        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$endDateTime->add(10, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(20, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenReservationExpiresButNotCancelledAndAssetReturned(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');
        $this->travelTo($endDateTime->copy()->add(1, 'hour'));

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
        
        // Mark the asset as returned
        $ids = [];
        $ids[Asset::first()->id] = ['returned' => 1];
        $loan->assets()->sync($ids);

        $this->testLoanString = Loan::with('assets')->find($loan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$endDateTime->add(10, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(20, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertTrue($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     * 
     * If a setup end time is before the current time, but it hasn't been 
     * completed, the assets should not be bookable
     */
    public function notAvailableWhenSetupExpiresButNotCompleted(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');
        $this->travelTo($endDateTime->copy()->add(1, 'hour'));

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
        $this->testLoanString = Loan::with('assets')->find($setupLoan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$endDateTime->add(10, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(20, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertFalse($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }

    /**
     * @test
     * @group asset-controller
     */
    public function availableWhenSetupExpiresButNotCompletedAndAssetReturned(): void
    {
        $this->seed();

        $startDateTime = Carbon::create(2000, 1, 1, 0);
        $endDateTime = $startDateTime->copy()->add(1, 'hour');
        $this->travelTo($endDateTime->copy()->add(1, 'hour'));

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

        $this->testLoanString = Loan::with('assets')->find($setupLoan->id)->toJson();

        $response = $this
            ->actingAs(User::first())
            ->get('/api/assets?startDateTime='.$endDateTime->add(10, 'minute')->timestamp.'&endDateTime='.$endDateTime->add(20, 'minute')->timestamp);
        $this->responseString = $response->getContent();

        $response->assertOk();
        $jsonResponse = $response->json();

        $item1 = collect($jsonResponse)->firstWhere('id', Asset::first()->id);
        $this->assertTrue($item1['available']);

        $item2 = collect($jsonResponse)->firstWhere('id', Asset::skip(1)->first()->id);
        $this->assertTrue($item2['available']);
    }
    
    protected function onNotSuccessfulTest(Throwable $exception): void
    {
        // Log to console if the test fails
        dump('testLoanString: '.$this->testLoanString);
        dump('responseString: '.$this->responseString);

        parent::onNotSuccessfulTest($exception);
    }
}