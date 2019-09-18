<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Recruiters extends Model
{
    protected $table = 'recruiters';

    protected $guarded = [];

    public function works()
    {
        return $this -> hasMany('App\Model\Works','recruiter_id','id');
    }

    public function comment()
    {
        return $this -> hasMany('App\Model\Comment','recruiter_id','id');
    }
}