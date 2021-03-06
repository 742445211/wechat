<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ViewWork extends Model
{
    protected $table = 'view_work';

    public $timestamps = false;

    public function work()
    {
        return $this->hasOne('App\Model\Works','id','work_id');
    }

    public function worker()
    {
        return $this->hasOne('App\Model\Workers','id','worker_id');
    }
}