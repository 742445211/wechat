<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Sign  extends Model
{
    protected $table = 'sign';

    public $timestamps = false;

    public function works()
    {
        return $this->hasOne('App\Model\Works','id','work_id');
    }

    public function workers()
    {
        return $this->hasOne('App\Model\Workers','id','worker_id');
    }
}