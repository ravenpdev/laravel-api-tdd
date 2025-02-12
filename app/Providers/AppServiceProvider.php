<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configCommands();
        $this->configModels();
        $this->configDates();
        // $this->configUrls();
        $this->configVite();
    }

    /**
     * Configure DB to prevent desctructive commands on prodution
     */
    private function configCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    /**
     * Configure model to be strict in developer
     */
    private function configModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
    }

    /**
     * Configure dates to be immutable
     */
    private function configDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    // private function configUrls(): void
    // {
    //     URL::forceScheme('https');
    // }

    /**
     * Configure vite to use aggressive prefetching
     */
    private function configVite(): void
    {
        Vite::useAggressivePrefetching();
    }
}
