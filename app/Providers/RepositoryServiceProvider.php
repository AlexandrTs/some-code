<?php

namespace App\Providers;

use App\Repositories\CsCartOrderRepository;
use App\Contracts\CsCartOrderInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            CsCartOrderInterface::class,
            CsCartOrderRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
