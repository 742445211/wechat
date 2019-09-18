<?php


namespace App\Providers;


use App\Sms\ReturnJson;
use Illuminate\Support\ServiceProvider;

class ReturnJsonServiceProvider extends ServiceProvider
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
        $this->app->singleton('returnjson',function ($app){
            return new ReturnJson();
        });
    }
}