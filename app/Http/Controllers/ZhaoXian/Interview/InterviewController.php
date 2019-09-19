<?php


namespace App\Http\Controllers\ZhaoXian\Interview;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Interview;
use Illuminate\Http\Request;

class InterviewController extends Controller
{
    /**
     * 获取当前用户在当前工作的面试通知
     * @param Request $request
     * @return mixed
     */
    public function getInterviewDetail(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $res = Interview::where('worker_id',$request->workerid) -> where('work_id',$request->workid) -> where('status',0) -> select('time','address') -> first();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'暂无面试信息');
    }
}