<?php


namespace App\Http\Controllers\ZhaoXian\Collection;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Collection;
use App\Model\Recruiters;
use App\Model\Works;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    /**
     * 员工收藏工作
     * @param Request $request
     * $request->workerid           员工ID
     * $request->workid             工作ID
     * @return mixed
     */
    public function collection(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $data = [
            'worker_id'        => $request -> workerid,
            'work_id'          => $request -> workid,
        ];

        $has = Collection::where('worker_id',$request->workerid) -> where('work_id',$request->workid) -> first();  //判断用户是否收藏过
        if($has){
            $next = Collection::where('worker_id',$request->workerid) -> where('work_id',$request->workid) -> update(['status'=>0]);
            if($next) return ReturnJson::json('ok',0,'收藏成功！');
            return ReturnJson::json('err',1,'收藏失败！');
        }

        $res = Collection::insert($data);
        if($res) return ReturnJson::json('ok',0,'收藏成功！');
        return ReturnJson::json('err',1,'收藏失败！');
    }

    /**
     * 取消收藏
     * @param Request $request
     * $request->workerid           员工ID
     * $request->workid             工作ID
     * @return mixed
     */
    public function cancel(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $res = Collection::where('worker_id',$request->workerid) -> where('work_id',$request->workid) -> update(['status'=>1]);
        if($res) return ReturnJson::json('ok',0,'已取消收藏！');
        return ReturnJson::json('err',1,'取消失败！');
    }

    /**
     * 员工获取自己收藏的工作
     * @param Request $request
     * $request->workerid       员工ID
     * @return mixed
     */
    public function getCollectionWork(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $res = Collection::with(['works','workers']) -> where('worker_id',$request->workerid) -> where('status',0) -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！');
    }

    /**
     * 招聘端通过工作ID获取收藏者
     * @param Request $request
     * $request->workid         工作ID
     * @return mixed
     */
    public function getCollectionWorker(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $workid = Works::where('recruiter_id',$request->id) -> select('id') -> get() -> toArray();
        $work_id = [];
        foreach ($workid as $value){
            array_push($work_id,$value['id']);
        }
        $res = Collection::with(['works:id,title','workers'=>function($query){
            $query->with(['experiences:intention_work,intention_place,worker_id'])
                ->select('id','header','username','experience','education')->get();
        }]) -> whereIn('work_id',$work_id) -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！');
    }
}