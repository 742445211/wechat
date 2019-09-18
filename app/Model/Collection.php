<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $table = 'collection';

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