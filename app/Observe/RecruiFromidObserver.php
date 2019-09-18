<?php


namespace App\Observe;


use App\RecruitFromid;

class RecruiFromidObserver
{

    public function retrieved(RecruitFromid $recruitFromid)
    {
        $time = time() - 604800;
        RecruitFromid::where('created_at','<',$time) -> delete();
    }
}