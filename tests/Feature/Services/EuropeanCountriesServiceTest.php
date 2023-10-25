<?php

namespace Tests\Feature\Services;

use App\Models\Statement;
use App\Services\EuropeanCountriesService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EuropeanCountriesServiceTest extends TestCase
{

    use RefreshDatabase;

    protected EuropeanCountriesService $european_countries_service;

    public function setUp(): void
    {
        $this->european_countries_service = app(EuropeanCountriesService::class);
        $this->assertNotNull($this->european_countries_service);
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    /**
     * @return void
     * @test
     */
    public function it_gets_all_the_options()
    {
        $options = $this->european_countries_service->getOptionsArray();
        $this->assertNotNull($options);
        $this->assertCount(30, $options);
    }

    /**
     * @return void
     * @test
     */
    public function it_condenses_european_union_countries()
    {
        $country_codes = EuropeanCountriesService::EUROPEAN_UNION_COUNTRY_CODES;
        $names = $this->european_countries_service->getCountryNames($country_codes);
        $this->assertEquals(['European Union'], $names);
    }

    /**
     * @return void
     * @test
     */
    public function it_condenses_european_economic_area_countries()
    {
        $country_codes = EuropeanCountriesService::EUROPEAN_ECONOMIC_AREA_COUNTRY_CODES;
        $names = $this->european_countries_service->getCountryNames($country_codes);
        $this->assertEquals(['European Economic Area'], $names);
    }

    /**
     * @return void
     * @test
     */
    public function it_does_not_condense_european_union_countries()
    {
        $country_codes = EuropeanCountriesService::EUROPEAN_UNION_COUNTRY_CODES;
        $names = $this->european_countries_service->getCountryNames($country_codes, false);
        $this->assertNotEquals(['European Union'], $names);
        $this->assertCount(27, $names);
    }

    /**
     * @return void
     * @test
     */
    public function it_filters_out_non_european_countries()
    {
        $country_codes = EuropeanCountriesService::EUROPEAN_UNION_COUNTRY_CODES;
        $country_codes[] = 'US';
        $country_codes[] = 'CA';
        $country_codes[] = 'XX';
        $country_codes[] = 'NOT EVEN CLOSE TO AN ISO';

        $result = $this->european_countries_service->filterSortEuropeanCountries($country_codes);
        $this->assertCount(27, $result);

        $this->assertNotContains('US', $result);
        $this->assertNotContains('CA', $result);
        $this->assertNotContains('XX', $result);
        $this->assertNotContains('NOT EVEN CLOSE TO AN ISO', $result);
    }

    /**
     * @return void
     * @test
     */
    public function it_does_not_break_on_empty()
    {
        $country_codes = [];
        $result = $this->european_countries_service->filterSortEuropeanCountries($country_codes);
        $this->assertCount(0, $result);
    }

    /**
     * @return void
     * @test
     */
    public function ensure_that_country_codes_arrays_are_in_alphabetical_order()
    {
        $codes = EuropeanCountriesService::EUROPEAN_COUNTRY_CODES;
        $result = $codes;
        sort($result);
        $this->assertEquals($result, $codes);

        $codes = EuropeanCountriesService::EUROPEAN_ECONOMIC_AREA_COUNTRY_CODES;
        $result = $codes;
        sort($result);
        $this->assertEquals($result, $codes);

        $codes = EuropeanCountriesService::EUROPEAN_UNION_COUNTRY_CODES;
        $result = $codes;
        sort($result);
        $this->assertEquals($result, $codes);
    }


}