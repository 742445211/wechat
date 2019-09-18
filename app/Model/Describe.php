<?php


namespace App\Model;



use Illuminate\Database\Eloquent\Model;

class Describe extends Model
{
    protected $table = 'describe';

    public $timestamps = false;

    public function works()
    {
        return $this->hasOne('App\Model\Works','work_id','id');
    }
}