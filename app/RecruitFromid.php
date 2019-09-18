<?php

namespace App;

use App\Events\ReadRecruiFromidEvent;
use Illuminate\Database\Eloquent\Model;

class RecruitFromid extends Model
{
    //
    protected $table = 'recruit_fromid';

    protected $guarded = [];

    protected $dispatchesEvents = [
        'retrieved' => RecruiFromidRetrieved::class,
    ];

    public function getRecruitFromidAttribute()
    {
        $time = time() - 604800;
        RecruitFromid::where('created_at','<',$time) -> delete();
    }
}
