<?php

namespace App\Providers;

use App\Services\EuropeanLanguagesService;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;

class EuropeanLanguagesServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(EuropeanLanguagesService::class, fn(Application $app) => new EuropeanLanguagesService());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [EuropeanLanguagesService::class];
    }
}
