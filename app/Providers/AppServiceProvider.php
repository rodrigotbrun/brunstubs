<?php

namespace App\Providers;

use App\Console\Commands\MakeActionCommand;
use App\Console\Commands\MakeDataCommand;
use App\Console\Commands\MakeHttpResourceCommand;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->commands([
            MakeDataCommand::class,
            MakeActionCommand::class,
            MakeHttpResourceCommand::class,
        ]);
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
