<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banner';

    public $timestamps = false;

    protected $fillable = ['status','level'];

    protected $visible = ['id','image_path','level','status','created_at'];
}