<?php


namespace App\Observe;


use App\UserFromid;

class UserFromidObserver
{
    public function retrieved(UserFromid $userFromid)
    {
        $time = time() - 604800;
        UserFromid::where('created_at','<',$time) -> delete();
    }
}