<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFromid extends Model
{
    //
    protected $table = 'user_fromid';

    protected $guarded = [];

    protected $dispatchesEvents = [
        'retrieved' => UserFromidRetrieved::class,
    ];

    public function getUserFromidAttribute()
    {
        $time = time() - 604800;
        UserFromid::where('created_at','<',$time) -> delete();
    }
}
