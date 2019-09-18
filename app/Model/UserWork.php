<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class UserWork extends Model
{
    protected $table = 'user_work';

    public $timestamps = false;

    public function workers()
    {
        return $this->hasOne('App\Model\Workers','id','worker_id');
    }

    public function works()
    {
        return $this->hasOne('App\Model\Works','id','work_id');
    }
}