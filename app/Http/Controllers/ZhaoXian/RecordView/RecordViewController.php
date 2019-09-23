<?php


namespace App\Http\Controllers\ZhaoXian\RecordView;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\ViewWork;
use App\Model\ViewWorker;
use App\Model\Works;
use Illuminate\Http\Request;

class RecordViewController extends Controller
{
    /**
     * 记录c端用户查看工作
     * @param Request $request
     * @return mixed
     */
    public function recordViewWork(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $data = [
            'worker_id'     => $request -> workerid,
            'work_id'       => $request -> workid,
            'created_at'    => time()
        ];
        $res = ViewWork::insert($data);
        if($res) return ReturnJson::json('ok',0,'记录成功');
        return ReturnJson::json('err',1,'记录失败');
    }

    /**
     * 获取产看过我的工作的c端用户
     * @param Request $request
     * @return mixed
     */
    public function getViewWork(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        //获取当前B端用户的在招工作
        $work_id = Works::where('recruiter_id',$request->id)
            -> where('status',0)
            -> select('id')
            -> get();
        $res = ViewWork::with(['work:id,title','worker'=>function($query){
            $query->with(['experiences:intention_work,intention_place,worker_id'])
                ->select('id','header','username','experience','education')->get();
        }]) -> whereIn('work_id',$work_id)
            -> select('work_id','worker_id','id')
            -> orderBy('id','desc')
            -> take(12)
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 记录b端用户查看c端用户
     * @param Request $request
     * @return mixed
     */
    public function recordViewWorker(Request $request)
    {
        $error = ReturnJson::parameter(['recruit_id','workerid'],$request);
        if($error) return $error;

        $data = [
            'recruit_id'        => $request -> recruit_id,
            'worker_id'         => $request -> workerid,
            'created_at'        => time()
        ];
        $res = ViewWorker::insert($data);
        if($res) return ReturnJson::json('ok',0,'记录成功');
        return ReturnJson::json('err',1,'记录失败');
    }

    /**
     * 获取查看过当前用户的B端用户
     * @param Request $request
     * @return mixed
     */
    public function getViewWorker(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = ViewWorker::with('recruiters:id,username,header,company')
            -> where('worker_id',$request->id)
            -> select('worker_id','recruit_id','id')
            -> orderBy('id','desc')
            -> take(12)
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }
}