<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Works extends Model
{
    protected $table = 'works';

    protected $guarded = [];

    public function recruiters()
    {
        return $this->hasOne('App\Model\Recruiters','id','recruiter_id');
    }

    public function cate()
    {
        return $this->hasOne('App\Model\Cate','cate_id','id');
    }

    public function describe()
    {
        return $this->hasOne('App\Model\Describe','work_id','id');
    }

    public function workImage()
    {
        return $this->hasMany('App\Model\WorkImage','work_id','id');
    }

    public function toBeAudited()
    {
        return $this->belongsToMany('App\Model\Workers','user_work','work_id','worker_id','id','id') -> wherePivot('status',0) -> withTimestamps();
    }

    public function workers()
    {
        return $this->belongsToMany('App\Model\Workers','user_work','work_id','worker_id','id','id') -> wherePivot('status',1) -> withTimestamps();
    }

    public function quit()
    {
        return $this->belongsToMany('App\Model\Workers','user_work','work_id','worker_id','id','id') -> wherePivot('status',2) -> withTimestamps();
    }

    public function enrolment()
    {
        return $this->belongsToMany('App\Model\Workers','user_work','work_id','worker_id','id','id') -> wherePivot('status',4) -> withTimestamps();
    }

    public function comment()
    {
        return $this->hasMany('App\Model\Comment','work_id','id');
    }

    public function userWork()
    {
        return $this->hasMany('App\Model\UserWork','work_id','id');
    }
}