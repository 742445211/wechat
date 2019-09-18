<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class GroupMsg extends Model
{
    protected $table = 'group_msg';

    public $timestamps = false;

    public function workers()
    {
        return $this -> hasOne('App\Model\Workers','id','workers_id');
    }
}