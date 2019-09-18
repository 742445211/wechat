<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class ReturnJson extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'returnjson';
    }
}