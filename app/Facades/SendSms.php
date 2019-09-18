<?php


namespace App\Facades;


use Illuminate\Support\Facades\Facade;

class SendSms extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'sms';
    }
}