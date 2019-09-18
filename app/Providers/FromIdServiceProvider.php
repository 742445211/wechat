<?php

namespace App\Providers;

use App\Sms\FromId;
use Illuminate\Support\ServiceProvider;

class FromIdServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('fromid',function ($app){
            return new FromId();
        });
    }
}
