<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobImage extends Model
{
    //
    protected $guarded = [];

    public function work()
    {
        return $this->belongsTo('App\Work','workid','id');
    }
}
