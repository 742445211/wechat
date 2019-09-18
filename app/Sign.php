<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sign extends Model
{
    //
    protected $table = 'sign';

    protected $guarded = [];

    public function homeuser()
    {
        return $this->belongsTo('App\HomeUser','userid','id');
    }

    public function work()
    {
        return $this->belongsTo('App\Work','workid','id');
    }
}
