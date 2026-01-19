<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ConfigurationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureModels();
        $this->configureCommands();
        $this->configureDates();
        $this->configureAutoEagerLoading();
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict(! app()->isProduction());
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(app()->isProduction());
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureAutoEagerLoading(): void
    {
        Model::automaticallyEagerLoadRelationships();
    }
}
