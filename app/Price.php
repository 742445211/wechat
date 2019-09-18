<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    //
    protected $table = 'price_detail';

    protected $guarded = [];

    public function work()
    {
        return $this->belongsTo('App\Work','workid','id');
    }

    public function homeuser()
    {
        return $this->belongsTo('App\HomeUser','userid','id');
    }
}
