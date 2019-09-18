<?php


namespace App\Http\Controllers\ZhaoXian\Notice;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Notice;
use App\Model\Workers;
use App\Model\Works;
use GatewayClient\Gateway;
use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;

class NoticeController extends Controller
{
    /**
     * 招聘端发布公告
     * @param Request $request
     * $request->workid         工作ID
     * $request->id             当前登陆者的ID
        $request -> title,      公告标题
        $request -> content,    公告内容
     * @return mixed
     */
    public function release(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id'],$request);
        if($error) return $error;

        //判断用户是否为该工作的管理员
        $id = Works::where('recruiter_id',$request->id) -> first();
        if($id){
            $data = [
                'work_id'       => $request -> workid,
                'title'         => $request -> title,
                'content'       => $request -> content,
                'version'       => 1,
                'created_at'    => date('Y-m-d H:i:s',time())
            ];
            //把公告信息存入数据库
            $res = Notice::insertGetId($data);
            if($res){
                //发送公告
                go(function () use ($data,$request,$res){
                    \co::sleep(0.25);
                    $data['id'] = $res;
                    $msg = [
                        'type'          => 'notice',
                        'content'       =>  [
                            'contentType'       => 'msg',
                            'content'           => $data
                        ]
                    ];
                    Gateway::sendToGroup($request -> workid, json_encode($msg, JSON_UNESCAPED_UNICODE));
                });
                return ReturnJson::json('ok',0,'发布成功！');
            }
            return ReturnJson::json('err',1,'发布失败！');
        }
        return ReturnJson::json('err',111,'非法请求');
    }

    /**
     * 删除公告
     * @param Request $request
     * $request->id         公告ID
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        //根据公告ID删除公告
        $res = Notice::where('id',$request -> id) -> update(['status'=>1]);
        if($res) return ReturnJson::json('ok',0,'已删除！');
        return ReturnJson::json('err',1,'删除失败！');
    }

    /**
     * 更新公告
     * @param Request $request
     * $request->id         公告ID
     * $request->workid     工作ID
     * @return mixed
     */
    public function edit(Request $request)
    {
        $error = ReturnJson::parameter(['id','workid'],$request);
        if($error) return $error;

        $data = [
            'title'         => $request -> title,
            'content'       => $request -> content,
            'version'       => $request -> version,
            'updated_at'    => date('Y-m-d H:i:s',time())
        ];
        //更新后发送
        go(function () use($data,$request){
            \co::sleep(0.25);
            $data['id'] = $request -> id;
            $msg = [
                'type'          => 'notice',
                'content'       =>  [
                    'contentType'       => 'msg',
                    'content'           => $data
                ]
            ];
            Gateway::sendToGroup($request -> workid, json_encode($msg, JSON_UNESCAPED_UNICODE));
        });
        $data['status'] = 0;
        //更新内容写入数据库
        $res = Notice::where('id',$request -> id) -> where('work_id',$request -> workid) -> update($data);
        if($res) return ReturnJson::json('ok',0,'更新成功！');
        return ReturnJson::json('err',1,'更新失败！');
    }

    /**
     * 获取某工作的公告
     * @param Request $request
     * $request->workid         工作ID
     * @return mixed
     */
    public function get(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $filed = ['id','title','content','version','created_at','updated_at'];
        //根据工作ID获取某工作未删除的公告，按发布时间倒叙排序
        $res = Notice::where('work_id',$request->workid)
            -> where('status',0)
            -> select($filed)
            -> orderBy('id','desc')
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！请稍后重试');
    }

    /**
     * 记录公告已读ID
     * @param Request $request
     * $request->workid             群ID
     * $request->notice_id          公告ID
     * $request->workerid           用户ID
     * @return mixed
     */
    public function recordsRead(Request $request)
    {
        $error = ReturnJson::parameter(['workid','notice_id','workerid'],$request);
        if($error) return $error;

        //从数据库里取出当前公告的已读记录
        $res = Notice::where('id',$request -> notice_id) -> where('work_id',$request -> workid) -> select('read') -> first();
        //有公告记录时
        if($res -> read){
            //将取出的记录转为数组
            $array = Hashids::decode($res -> read);
            //把当前用户ID push进数组中
            array_push($array,$request -> workerid);
            //把数组转为字符串
            $data = Hashids::encode($array);
        }else{
            //没有公告记录时，直接将当前用户ID存如数据库
            $data = Hashids::encode([$request -> workerid]);
        }
        $res = Notice::where('id',$request -> notice_id) -> where('work_id',$request -> workid) -> update(['read'=>$data]);
        if($res) return ReturnJson::json('ok',0,'记录成功');
        return ReturnJson::josn('err',1,'记录失败');
    }

    /**
     * 获取已读人名单
     * @param Request $request
     * $request -> workid           群ID
     * $request -> notice_id        公告ID
     * @return mixed
     */
    public function getReadRecord(Request $request)
    {
        $error = ReturnJson::parameter(['workid','notice_id'],$request);
        if($error) return $error;

        $worker = Notice::where('id',$request -> notice_id) -> where('work_id',$request -> workid) -> select('read') -> first();
        if($worker -> read){
            $read = Hashids::decode($worker -> read);
            $res = Workers::whereIn('id',$read) -> select('username','header') -> get();
            if($res) return ReturnJson::json('ok',0,$res);
            return ReturnJson::json('err',1,'查询失败');
        }else{
            return ReturnJson::json('err',0,'当前无人未读');
        }
    }
}