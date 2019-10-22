<?php


namespace App\Http\Controllers\ZhaoXian\Comment;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Comment;
use App\Model\UserWork;
use App\Model\Works;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     *员工评论工作
     * @param Request $request
     * @return mixed
     */
    public function comment(Request $request)
    {
        $error = ReturnJson::parameter(['workid','workerid','wages','service'],$request);
        if($error) return $error;

        $work_id = $request -> workid;
        $worker_id = $request -> workerid;
        //查询用户是否完成工作，未完成不能评论
        $has = UserWork::where('work_id',$work_id) -> where('worker_id',$worker_id) -> where('status',2) -> first();
        if(!$has) return ReturnJson::json('err',11,'当前无法评论！');
//        //综合评分 = 工资评分 + 服务评分 + 工作满意度   除以  3
//        $synthesize = $request -> wages + $request -> service + $request -> satisfaction;
        $data = [
            'work_id'       => $work_id,                            //工作ID
            'worker_id'     => $worker_id,                          //员工ID
            'wages'         => $request -> wages,                   //工资评分
            'service'       => $request -> service,                 //服务评分
            //'satisfaction'  => $request -> satisfaction,            //工作满意度评分
            'synthesize'    => $request -> synthesize,              //综合评分
            'comment'       => $request -> comment,                 //内容
            'interview'     => $request -> interview,               //面试综合
            'offset'        => $request -> offset,                  //面试评价选项
            'ambient'       => $request -> ambient,                 //工作环境评分
            'content'       => $request -> content,                 //面试内容文字评价
            'created_at'    => date('Y-m-d H:i:s',time()),
        ];
        $res = Comment::insert($data);
        if($res) return ReturnJson::json('ok',0,'评论成功！');
        return ReturnJson::json('err',1,'评论失败！');
    }

    /**
     * 获取某工作的评论
     * @param Request $request
     * @return mixed
     *
     */
    public function getWorkComment(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $filed = ['id','work_id','worker_id','comment','created_at','wages','service','ambient','synthesize','interview','content'];
        $res = Works::with(['comment'=>function($query) use($filed){
            $query->with('workers:workers.id,username,header')->select($filed)->orderBy('id','desc')->get();
        }]) -> where('id',$request->workid) -> select('works.id') -> first()->toArray();
        foreach ($res['comment'] as $key => $value){
            $res['interviews'] = [0=>0,1=>0,2=>0];
            if($value['interview'] == 0){
                $res['interviews'][0] += 1;
            }elseif ($value['interview'] == 1){
                $res['interviews'][1] += 1;
            }elseif ($value['interview'] == 2){
                $res['interviews'][2] += 1;
            }
        }
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！');
    }

    /**
     * 获取某员工的评论
     * @param Request $request
     * @return mixed
     */
    public function getWorkerComment(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $filed = ['id','work_id','worker_id','comment','created_at'];
        $res = Comment::with('works:id,title,header')
            -> where('worker_id',$request -> workerid)
            -> where('status',0)
            -> select($filed)
            -> orderBy('id','desc')
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！');
    }

    /**
     * 删除某员工在某工作下的评论
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid','id'],$request);
        if($error) return $error;

        $res = Comment::where('worker_id',$request -> workerid)
            -> where('work_id',$request -> workid)
            -> where('id',$request -> id)
            -> update(['status'=>1]);
        if($res) return ReturnJson::json('ok',0,'删除成功！');
        return ReturnJson::json('err',1,'删除失败！');
    }

    /**
     * 获取b端用户的综合评分
     * @param Request $request
     * @return mixed
     */
    public function fraction(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $data = Works::with('comment')->where('recruiter_id',$request->id)->select('id')->get()->toArray();
        if(count($data)){
            $comment = [];
            foreach($data as $item){
                if(count($item['comment'])){
                    foreach ($item['comment'] as $v){
                        array_push($comment,$v);
                    }
                }
            }
            if(count($comment) == 0) return ReturnJson::json('ok',0,0);
            $fraction = 0;
            $wages = 0;
            $service = 0;
            $ambient = 0;
            foreach($comment as $value){
                $fraction += $value['wages'] + $value['service'] + $value['ambient'] + $value['synthesize'];
                $wages += $value['wages'];
                $service += $value['service'];
                $ambient += $value['ambient'];
            }
            $fraction = $fraction / 4 / count($comment);
            $wages = $wages / count($comment);
            $service = $service / count($comment);
            $ambient = $ambient / count($comment);
            return ReturnJson::json('ok',0,['fraction' => $fraction,'wages' => $wages,'service' => $service,'ambient' => $ambient]);
        }
        return ReturnJson::json('err',1,'服务器忙');
    }

    /**
     * 通过B端用户获取评论
     * @param Request $request
     * @return mixed
     */
    public function getCommentByRec(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = Works::with(['comment'=>function($query){
            $query->with('workers:id,username,header')->select('work_id','worker_id','comment','created_at')->get();
        }])->where('recruiter_id',$request->id)
            -> select('id')
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 获取评论分数
     * @param Request $request
     * @return mixed
     */
    public function getComOption(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = Works::with('comment:work_id,interview') -> where('recruiter_id',$request->id) -> select('id') -> get() -> toArray();
        if($res) {
            $comment = [
                'difference'    => 0,
                'commonly'      => 0,
                'good'          => 0
            ];
            $data = [];
            foreach ($res as $k=>$v){
                if($v['comment'] != []){
                    foreach ($v['comment'] as $item){
                        array_push($data,$item);
                    }
                }
            }
            foreach ($data as $value){
                if($value['interview'] == 0){
                    $comment['difference'] += 1;
                }elseif ($value['interview'] == 1){
                    $comment['commonly'] += 1;
                }elseif ($value['interview'] == 2){
                    $comment['good'] += 1;
                }
            }
            return ReturnJson::json('ok',0,$comment);
        }
        return ReturnJson::json('err',1,[]);
    }
}