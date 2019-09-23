<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class ViewWorker extends Model
{
    protected $table = 'view_worker';

    public $timestamps = false;

    public function recruiters()
    {
        return $this->hasOne('App\Model\Recruiters','recruit_id','id');
    }
}