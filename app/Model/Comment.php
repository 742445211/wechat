<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comment';

    public $timestamps = false;

    public function works()
    {
        return $this->belongsTo('App\Model\Works','work_id','id');
    }

    public function workers()
    {
        return $this->belongsTo('App\Model\Workers','worker_id','id');
    }
}