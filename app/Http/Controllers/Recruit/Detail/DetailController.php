<?php


namespace App\Http\Controllers\Recruit\Detail;


use App\Cplt;
use App\Http\Controllers\Controller;
use App\Recruiter;
use Illuminate\Http\Request;

/**
 * 报名详情
 * Class DetailController
 * @package App\Http\Controllers\Recruit\Detail
 */
class DetailController extends Controller
{

    /**
     * 查看所有报名
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $adminid = $request->recruit_id;
        if($adminid){
            $cplt = Cplt::with('work:id,title','homeuser:id,username,phone')
                -> where('rid',$adminid)
                -> where('status',$request->status) //[0=>待处理，1=>待面试，2=>已过审，3=>未通过]
                -> get();
            if($cplt){
                return ['msg'=>'ok','code'=>0,'result'=>$cplt];
            }else{
                return ['msg'=>'err','code'=>1,'result'=>'未知错误'];
            }
        }
    }


}