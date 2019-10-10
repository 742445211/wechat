<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('banner',Banner\BannerController::class);

    $router->resource('census',Census\CensusController::class);

    $router->resource('works',Works\WorksController::class);

    $router->resource('workers',Workers\WorkersController::class);

    $router->resource('recruiter',Recruiter\RecruiterController::class);

    $router->resource('cate',Cate\CateController::class);

    $router->resource('feedback',Feedback\FeedbackController::class);
});
