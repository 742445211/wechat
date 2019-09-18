<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cplt extends Model
{
    //
    protected $table = 'cplt';

    protected $guarded = [];

    public function homeuser()
    {
        return $this->belongsTo('App\HomeUser','userid','id');
    }

    public function recruiter()
    {
        return $this->belongsTo('App\Recruiter','rid','id');
    }

    public function work()
    {
        return $this->belongsTo('App\Work','workid','id');
    }
}
