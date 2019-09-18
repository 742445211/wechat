<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    //
    protected $table = 'work';

    protected $guarded = [];

    public $timestamps = false;

    public function recruiter()
    {
        return $this->belongsTo('App\Recruiter','rid','id');
    }
}
