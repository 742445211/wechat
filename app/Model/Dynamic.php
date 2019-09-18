<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Dynamic extends Model
{
    protected $table = 'dynamic';

    public $timestamps = false;

    public function recruiter()
    {
        return $this -> belongsTo('App\Model\Recruiters','recruiter_id','id');
    }

    public function dynamicImage()
    {
        return $this -> hasMany('App\Model\DynamicImage','dynamic_id','id');
    }
}