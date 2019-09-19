<?php


namespace App\Model;


use Illuminate\Database\Eloquent\Model;

class Workers extends Model
{
    protected $table = 'workers';

    /**
     * 待审核
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function toBeAudited()
    {
        return $this->belongsToMany('App\Model\Works','user_work','worker_id','work_id','id','id') -> wherePivot('status',0) -> withTimestamps();
    }

    /**
     * 工作中
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function works()
    {
        return $this->belongsToMany('App\Model\Works','user_work','worker_id','work_id','id','id') -> wherePivot('status',1) -> withTimestamps();
    }

    /**
     * 已离职
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function quit()
    {
        return $this->belongsToMany('App\Model\Works','user_work','worker_id','work_id','id','id') -> wherePivot('status',2) -> withTimestamps();
    }

    public function educational()
    {
        return $this->hasMany('App\Model\WorkEducational','worker_id','id');
    }

    public function experiences()
    {
        return $this->hasMany('App\Model\WorkExperience','worker_id','id');
    }
}