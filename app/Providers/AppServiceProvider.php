<?php

namespace App\Providers;

use App\RecruitFromid;
use App\UserFromid;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        RecruitFromid::observe(RecruitFromid::class);
        UserFromid::observe(UserFromid::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
