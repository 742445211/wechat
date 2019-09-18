<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $table = 'store';

    public $timestamps = false;

    public function storeImage()
    {
        return $this->hasMany('App\Model\storeImage','id','image_id');
    }
}