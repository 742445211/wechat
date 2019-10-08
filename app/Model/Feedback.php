<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    public $timestamps = false;

    public function worker()
    {
        return $this->hasOne('App\Model\Workers','id','worker_id');
    }
}