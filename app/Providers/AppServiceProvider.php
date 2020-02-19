<?php

namespace App\Providers;

use App\Http\Models\Devs;
use App\Http\Models\Pivot\DevTechs;
use App\Observers\DevObserver;
use App\Observers\DevTechsObserver;
use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Devs::observe(DevObserver::class);
        DevTechs::observe(DevTechsObserver::class);
    }
}
