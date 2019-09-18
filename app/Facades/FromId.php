<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class FromId extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'fromid';
    }
}