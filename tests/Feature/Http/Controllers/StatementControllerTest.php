<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Statement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;

use Tests\TestCase;

/**
 * @see \App\Http\Controllers\StatementController
 */
class StatementControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function index_displays_view()
    {
        $statements = Statement::factory()->count(3)->create();
        $response = $this->get(route('statement.index'));
        $response->assertOk();
        $response->assertViewIs('statement.index');
        $response->assertViewHas('statements');
    }

    /**
     * @test
     */
    public function index_does_not_auth()
    {
        // The cas is set to masquerade in testing mode.
        // So when we make a call to a cas middleware route we get logged in.
        // If we make a call to a non cas route nothing should happen.
        $u = auth()->user();
        $this->assertNull($u);
        $response = $this->get(route('statement.index'));
        $u = auth()->user();
        $this->assertNull($u);
    }


    /**
     * @test
     */
    public function create_displays_view()
    {
        $user = $this->signIn();
        $response = $this->get(route('statement.create'));
        $response->assertOk();
        $response->assertViewIs('statement.create');
    }

    /**
     * @test
     */
    public function create_must_be_authenticated()
    {
        // The cas is set to masquerade in testing mode.
        // So when we make a call to a cas middleware route we get logged in.
        // Thus before we make this call we are nobody
        $u = auth()->user();
        $this->assertNull($u);
        $response = $this->get(route('statement.create'));

        // After we made this call we are somebody
        $u = auth()->user();
        $this->assertNotNull($u);
    }


    /**
     * @test
     */
    public function show_displays_view()
    {
        $this->seed();
        $statement = Statement::factory()->create();
        $response = $this->get(route('statement.show', $statement));

        $response->assertOk();
        $response->assertViewIs('statement.show');
        $response->assertViewHas('statement');
    }


    /**
     * @test
     */
    public function store_uses_form_request_validation()
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\StatementController::class,
            'store',
            \App\Http\Requests\StatementStoreRequest::class
        );
    }

    /**
     * @test
     * @see StatementAPIControllerTest
     */
    public function store_saves_and_redirects()
    {
        // This is a basic test that the normal controller is working.
        // For more advanced testing on the request and such, see the API testing.
        $title = $this->faker->sentence(4);
        $language = 'en';

        $user = $this->signIn();

        $this->assertCount(0, Statement::all());

        // When making statements via the FORM
        // The dates come in as d-m-Y from the ECL datepicker.
        $response = $this->post(route('statement.store'), [
            'title' => $title,
            'language' => $language,
            'date_sent' => '01-01-2023',
            'date_enacted' => '02-01-2023',
            'date_abolished' => '03-01-2023',
            'source' => Statement::SOURCE_ARTICLE_16
        ]);


        $this->assertCount(1, Statement::all());
        $statement = Statement::latest()->first();
        $this->assertNotNull($statement);
        $this->assertEquals('FORM', $statement->method);
        $this->assertEquals($user->id, $statement->user->id);
        $this->assertEquals('2023-01-03 00:00:00', $statement->date_abolished);
        $this->assertInstanceOf(Carbon::class, $statement->date_abolished);

        $response->assertRedirect(route('statement.index'));
    }
}
