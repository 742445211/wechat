<?php


namespace App\Http\Controllers\ZhaoXian\Msg;


use App\Facades\BaseFile;
use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\PrivateMsg;
use App\Model\Test;
use App\Model\UserWork;
use App\Model\Works;
use GatewayClient\Gateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MsgController extends Controller
{
    protected $redis;

    public function __construct()
    {
        $this->redis = Redis::connection('msg');
    }

    /**
     * 为用户绑定群ID
     * @param Request $request
     * $request->workerid           员工ID
     * $request->client_id          GetawayWorker客服端唯一标示
     * @return mixed
     */
    public function bindMyGroup(Request $request)
    {
        $error = ReturnJson::parameter(['id','client_id','is_rec'],$request);
        if($error) return $error;

        //设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

        //获取从前台传回来的数据
        $uid = $request -> id;                //获取员工ID
        $client_id = $request -> client_id;         //获取GetawayWorker发放的client_id

        $myGroupId = null;
        //通过uid获取当前用户的所有群ID
        if($request -> is_rec == 0){
            //当用户时员工时
            $myGroupId = UserWork::where('worker_id',$uid) -> where('status','=',1) -> select('work_id') -> get() -> toArray();
        }elseif($request -> is_rec == 1){
            //当用户时管理员时
            $myGroupId = Works::where('recruiter_id',$uid) -> where('status','!=',2) -> select('id') -> get() -> toArray();
        }

        //把client_id和uid绑定
        $uid = $request -> is_rec == 0 ? 'c'.$uid : 'b'.$uid;       //标示为c端用户
        go(function () use($client_id,$uid){
            \co::sleep(0.25);
            Gateway::bindUid($client_id,$uid);
        });

        //给用户绑定群ID
        go(function () use($client_id,$myGroupId,$request){
            \co::sleep(0.25);

            foreach($myGroupId as $v){
                go(function () use($client_id,$v,$request){
                    \co::sleep(0.25);
                    if($request->is_rec == 0){
                        Gateway::joinGroup($client_id,$v['work_id']);
                    }else{
                        Gateway::joinGroup($client_id,$v['id']);
                    }
                });
            }
        });
        if($myGroupId){
            $work = Works::whereIn('id',$myGroupId) -> select('id','header','title') -> get();
            if($work) return ReturnJson::json('ok',0,$work);
        }

        return ReturnJson::json('err',1,'群ID获取失败！');
    }

    /**
     * 获取消息记录，聊天记录下拉
     * @param Request $request
     * $request->workid         工作ID
     * $request->id             现有消息记录的最前一个ID
     * @return mixed
     */
    public function getGroupMsg(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id'],$request);
        if($error) return $error;

        //拼装table
        $table = 'group_msg' . $request -> workid;

        //获取最前历史消息的ID
        $id = $request -> id <= 20 ? 1 : $request -> id - 20;

        //通过最后一条消息的ID获取更后面的30条消息，按时间倒叙
        $msg = DB::table($table)
            -> select('id','username','header','content','created_at','member_id','is_rec')
            -> whereBetween('id',[$id, $request -> id])
            -> orderBy('id','desc')
            -> get();

        if($msg) return ReturnJson::json('ok',0,[$msg,$id]);
        return ReturnJson::json('err',1,'未知错误！稍后再试');
    }

    /**
     * 记录用户在某群浏览的最后一条消息
     * @param Request $request
     * $request->workid             群ID
     * $request->msg_id             最后一条消息ID
     * $request->id                 使用者ID
     * $request->is_rec             使用者是否为管理员
     * @return mixed
     */
    public function recordUnreadId(Request $request)
    {
        $error = ReturnJson::parameter(['workid','msg_id','id','is_rec'],$request);
        if($error) return $error;

        $msg_id = $request -> msg_id;
        //is_rec为0时为C端用户，为1时表示B端用户
        $id     = $request -> is_rec == 0 ? 'c'.$request -> id : 'b'.$request -> id;
        $work_id = $request -> workid;

        $redis = $this -> redis;
        //记录用户在某群浏览的最后一条消息的ID，hash表名为用户ID,群ID为filed，值为msg_id
        $res = $redis -> hset($id, $work_id, $msg_id);
        if($res) return ReturnJson::json('ok',0,'记录成功！');
        return ReturnJson::json('err',1,'记录失败！');
    }

    /**
     * 获取未读群消息
     * @param Request $request
     * $request->workid         群ID
     * $request->id             发送者ID
     * $request->is_rec         发送者是否为管理员
     * @return mixed
     */
    public function getUnreadMsg(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id','is_rec'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $id = $request -> is_rec == 0 ? 'c'.$request -> id : 'b'.$request -> id;
        //从redis中取出记录的消息ID
        $msg_id = $redis -> hget($id, $request -> workid);
        if($msg_id){
            //获取当前ID到最大ID的消息
            $res = DB::table('group_msg' . $request -> workid)
                -> select('id','member_id','content','created_at','is_rec','username','header')
                -> where('id','>=',$msg_id)
                -> get();
        }else{
            $res = DB::table('group_msg' . $request -> workid)
                -> select('id','member_id','content','created_at','is_rec','username','header')
                -> orderBy('id','desc')
                -> limit(30)
                -> get()
                -> toArray();
            $res = array_reverse($res);
        }
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！请手动下拉');
    }

    /**
     * 获取用户未读消息数
     * @param Request $request
     * $request->workid         工作群ID（数组）
     * $request->id             使用者ID
     * $request->is_rec         是否为管理员
     * @return mixed
     */
    public function getUnreadNumber(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id','is_rec'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $id = $request -> is_rec == 0 ? 'c'.$request -> id : 'b'.$request -> id;
        $workid = json_decode($request -> workid);
        $workid = explode(',',$workid);
        if($workid == []) return ReturnJson::json('err',1,[]);
        //从redis中取出记录消息的ID
        $msg_id = $redis -> hmget($id, $workid);
        return $msg_id;
        $res = [];
        //循环查询未读条数
        foreach ($msg_id as $key => $item) {
            if($item == null){
                $res[$workid[$key]] = 0;
            }else{
                //$max = DB::select("select max(id) as maxid from group_msg");
                $res[$workid[$key]] = DB::table('group_msg' . $workid[$key])
                    -> select('id')
                    -> where('id','>',$item)
                    -> get()
                    -> count();
            }
        }
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,[]);
    }

    /**
     * 获取群的最后一条信息
     * @param Request $request
     * @return mixed
     */
    public function getLastMsg(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $workid = json_decode($request->workid);
        $res = [];
        for($i=0;$i<count($workid);$i++){
            $res[$workid[$i]] = DB::table('group_msg' . $workid[$i])
                -> select('id','content','created_at','username')
                -> orderBy('id','desc')
                -> limit(1)
                -> first();
            //$res[$workid[$i]] -> created_at = date('Y-m-d',$res[$workid[$i]] -> created_at);
        }
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'稍后再试！');
    }

    /**
     * 发送消息
     * $request->workid             工作ID
     * $request->id                 发送者的ID
     * $request->msg                发送的消息
     * $request->created_at         发送时间
     * $request->is_rec             发送者是否为管理员
     * $request->username           发送者的姓名
     * $request->header             发送者的头像
     * @param Request $request
     * @return void
     */
    public function sendMsg(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id','msg','created_at','is_rec','username','header'],$request);
        if($error) return $error;

//        //判断上传的是否为图片
//        $massage = $request -> msg;
//        $massage['content']['content'] = BaseFile::processing($massage['content']);
        $massage = $request -> msg;
        //dd($massage);exit;
        //设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

        //使用通道接受协程中的数据
        $chan = new \co\Channel(1);

        //把消息及相关数据存入mysql
        go(function () use($request,$chan,$massage){
            //拼接要写入数据库的数据
            $data = [
                'member_id'         => $request -> id,
                'content'           => $massage,
                'created_at'        => $request -> created_at,
                'is_rec'            => $request -> is_rec,
                'username'          => $request -> username,
                'header'            => $request -> header
            ];
            $res = DB::table('group_msg' . $request->workid) -> insertGetId($data);
            if($res){
                $chan -> push($res);
            }
        });

        //发送消息
        go(function () use($request,$chan,$massage){
            $data = [
                'id'                => $chan -> pop(),
                'username'          => $request -> username,
                'header'            => $request -> header,
                'created_at'        => $request -> created_at,
                'is_rec'            => $request -> is_rec,
                'content'           => json_decode($massage)
            ];
            Gateway::sendToGroup($request -> workid, json_encode($data, JSON_UNESCAPED_UNICODE));
        });

    }

    /**
     * 发送图片
     * @param Request $request
     * @return mixed
     */
    public function sendImage(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id','msg','created_at','is_rec','username','header'],$request);
        if($error) return $error;

        //判断上传的是否为图片
        $massage = $request -> msg;
        $massage['content'] = BaseFile::processing(['content' => $massage['content'],'contentType' => $massage['contentType']]);
        //设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

        //使用通道接受协程中的数据
        $chan = new \co\Channel(1);

        //把消息及相关数据存入mysql
        go(function () use($request,$chan,$massage){
            \co::sleep(1);
            //拼接要写入数据库的数据
            $data = [
                'member_id'         => $request -> id,
                'content'           => $massage,
                'created_at'        => $request -> created_at,
                'is_rec'            => $request -> is_rec,
                'username'          => $request -> username,
                'header'            => $request -> header
            ];
            $res = DB::table('group_msg' . $request->workid) -> insertGetId($data);
            if($res){
                $chan -> push($res);
            }
        });

        //发送消息
        go(function () use($request,$chan,$massage){
            \co::sleep(1);
            $data = [
                'id'                => $chan -> pop(),
                'username'          => $request -> username,
                'header'            => $request -> header,
                'created_at'        => $request -> created_at,
                'is_rec'            => $request -> is_rec,
                'content'           => $massage
            ];
            Gateway::sendToGroup($request -> workid, json_encode($data, JSON_UNESCAPED_UNICODE));
        });
    }

    /**
     * 解散群
     * @param Request $request
     * $request -> workid       群ID
     * $request -> id           群主ID
     * @return mixed
     */
    public function unGroup(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id'],$request);
        if($error) return $error;

        //设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

        $num = UserWork::where('work_id',$request -> workid) -> where('status',1) -> select('id') -> get() -> count();
        $work = Works::where('id',$request -> workid) -> where('recruiter_id',$request -> id) -> update(['status'=>2]);
        $user_work = UserWork::where('work_id',$request -> workid) -> update(['status'=>2,'updated_at'=>date('Y-m-d',time())]);

        if($work && $user_work){
            go(function () use($request,$num){
                \co::sleep(0.25);
                Gateway::ungroup($request -> workid);
                $redis = Redis::connection('census');
                $redis -> decr('atwork');
                $redis -> decrby('onthejob',$num);
                $redis -> incr('finish');
            });

            return ReturnJson::json('ok',0,'已解散');
        }
        return ReturnJson::json('err',1,'操作失败');
    }

    /**
     * 踢人
     * @param Request $request
     * @return mixed
     */
    public function leaveGroup(Request $request)
    {
        $error = ReturnJson::parameter(['workid','workerid'],$request);
        if($error) return $error;

        //设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

        $user_work = UserWork::where('work_id',$request -> workid)
            -> where('worker_id',$request -> workerid)
            -> update(['status' => 2,'updated_at'=>date('Y-m-d',time())]);

        if($user_work){
            go(function () use($request){
                \co::sleep(1);
                Gateway::leaveGroup('c' . $request -> workerid,$request -> workid);
                $redis = Redis::connection('census');
                $redis -> decr('onthejob');
            });
        }

        if($user_work) return ReturnJson::json('ok',0,'已移除');
        return ReturnJson::json('err',1,'操作失败');
    }

    /**
     * 消息格式
     * $msg = [
            'type'      => 'say',
            'content'   => [
                'contentType'       => 'file',
                'content'           => '内容！'
            ]
        ];
     */

    /**
     * 加入分组
     * @param Request $request
     * $request -> workid           群ID
     * $request -> workerid         员工ID
     * $request -> grouping         分组ID
     * @return mixed
     */
    public function setGrouping( Request $request)
    {
        $error = ReturnJson::parameter(['workid','workerid','grouping_id'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        //设置群分组信息，表名为群ID，字段为员工ID，值为分组
        $group = $redis -> hset('group'.$request -> workid, $request -> workerid, $request -> grouping_id);
        if($group) return ReturnJson::json('ok',0,'已加入');
        return ReturnJson::json('err',1,'加入失败');
    }

    /**
     * 获取分组及人员
     * @param Request $request
     * $request -> workid           群ID
     * @return mixed
     */
    public function getGrouping(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $data = $redis -> hgetall('group'.$request -> workid);
        if($data) return ReturnJson::json('ok',0,$data);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 删除某员工的分组记录
     * @param Request $request
     * $request -> workid               群ID
     * $request -> workerid             员工ID
     * @return mixed
     */
    public function delGrouping(Request $request)
    {
        $error = ReturnJson::parameter(['workid','workerid'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $res = $redis -> hdel('group'.$request -> workid, $request -> workerid);
        if($res) return ReturnJson::json('ok',0,'已删除');
        return ReturnJson::json('err',1,'删除失败');
    }

    /**
     * 修改某员工的分组
     * @param Request $request
     * @return mixed
     */
    public function editGrouping(Request $request)
    {
        $error = ReturnJson::parameter(['workid','workerid','grouping_id'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $res = $redis -> hset('group'.$request -> workid, $request -> workerid, $request -> grouping_id);
        $data = $redis -> hget('group'.$request -> workid, $request -> workerid);
        if($request -> grouping_id == $data) return ReturnJson::json('ok',0,'修改成功');
        return ReturnJson::json('err',1,'修改失败');
    }

    /**
     * 添加分组名
     * @param Request $request
     * $request -> workid           群ID
     * $request -> grouping_name    分组名
     * @return mixed
     */
    public function addGroupingName(Request $request)
    {
        $error = ReturnJson::parameter(['workid','grouping_name'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $num = $redis -> zcard($request -> workid);
        $res = $redis -> zadd($request -> workid, ($num + 1), $request -> grouping_name);
        if($res) return ReturnJson::json('ok',0,'添加成功');
        return ReturnJson::json('err',1,'添加失败');
    }

    /**
     * 获取群的分组及名称
     * @param Request $request
     * @return mixed
     */
    public function getGroupingName(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $data = $redis -> zrange($request -> workid, 0, -1, true);
        $data = array_flip($data);
        if($data) return ReturnJson::json('ok',0,$data);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 修改分组名
     * @param Request $request
     * @return mixed
     */
    public function editGroupingName(Request $request)
    {
        $error = ReturnJson::parameter(['workid','grouping_id','old','now'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $del = $redis -> zrem($request -> workid, $request -> old);
        if($del){
            $res = $redis -> zadd($request -> workid, $request -> grouping_id, $request -> now);
            if($res) return ReturnJson::json('ok',0,'修改成功');
            return ReturnJson::json('err',1,'修改失败');
        }
        return ReturnJson::json('err',1,'修改失败');
    }

    /**
     * 删除分组
     * @param Request $request
     * $request -> workid           群ID
     * $request -> grouping_name    分组名
     * @return mixed
     */
    public function delGroupingName(Request $request)
    {
        $error = ReturnJson::parameter(['workid','grouping_name'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $del = $redis -> zrem($request -> workid, $request -> grouping_name);
        if($del) return ReturnJson::json('ok',0,'删除成功');
        return ReturnJson::json('err',1,'删除失败');
    }

    /**
     * 用户绑定私聊
     * b_id
     * c_id
     * is_rec
     * client_id
     * @param Request $request
     * @return mixed
     */
    public function bindPrivate(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','is_rec','client_id'],$request);
        if($error) return $error;

        //设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

        $client_id = $request->client_id;
        $uid = $request->is_rec == 0 ? 'C' . $request->c_id : 'B' . $request->b_id;

        Gateway::bindUid($client_id,$uid);
    }

    /**
     * 添加好友（点击咨询）
     * @param Request $request
     * @return mixed
     */
    public function addFriend(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','is_rec'],$request);
        if($error) return $error;

        $uid = $request->is_rec == 0 ? 'C' . $request->c_id : 'B' . $request->b_id;
        $fid = $request->is_rec == 0 ? $request->b_id : $request->c_id;
        $redis = $this->redis;
        $time = time();
        $res = $redis -> zadd('P' . $uid,$time,$fid);
        if($res == 1 || $res == 0) return ReturnJson::json('ok',0,'添加成功');
        return ReturnJson::json('err',1,'添加失败');
    }

    /**
     * 获取好友列表
     * @param Request $request
     * @return mixed
     */
    public function getFriend(Request $request)
    {
        $error = ReturnJson::parameter(['id','is_rec'],$request);
        if($error) return $error;

        $uid = $request->is_rec == 0 ? 'C' . $request->id : 'B' . $request->id;
        $redis = $this->redis;
        $res = $redis -> zRevRange('P' . $uid,0,-1);
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 私聊发送消息
     * @param Request $request
     * @return mixed
     */
    public function sendToPrivate(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','is_rec'],$request);
        if($error) return $error;

        //设置GatewayWorker服务的Register服务ip和端口
        Gateway::$registerAddress = '127.0.0.1:1238';

        //使用通道接受协程中的数据
        //$chan = new \co\Channel(1);

        //链接redis
        $redis = $this -> redis;

        //把消息及相关数据存入mysql
        //go(function () use($request,$chan){
            //拼接要写入数据库的数据
            $data = [
                'massage'           => $request -> massage,             //消息内容
                'type'              => $request -> type,                //消息类型
                'username'          => $request -> username,            //发送者姓名
                'header'            => $request -> header,              //发送者头像
                'b_id'              => $request -> b_id,                //发送者id
                'c_id'              => $request -> c_id,                //接受者id
                'created_at'        => $request -> created_at,          //发送时间
                'is_rec'            => $request -> is_rec
            ];
            $res = PrivateMsg::insertGetId($data);
            //if($res) {
            //    $chan -> push($res);
            //}
        //});
        //发送消息
        //go(function () use($request,$chan,$redis,$id){
            $data = [
                'id'                => $res,
                'username'          => $request -> username,
                'header'            => $request -> header,
                'created_at'        => $request -> created_at,
                'type'              => $request -> type,
                'massage'           => $request -> massage,
                'b_id'              => $request -> b_id,
                'c_id'              => $request -> c_id,
                'is_rec'            => $request -> is_rec
            ];

//            if($request->is_rec == 0){
//                $redis -> incr('b' . $request->b_id . 'c' . $request->c_id);
//            }elseif ($request->is_rec == 1){
//                $redis -> incr('c' . $request->c_id . 'b' . $request->b_id);
//            }
            $get_id = $request->is_rec == 0 ? 'b' . $request->b_id : 'c' . $request->c_id;
            Gateway::sendToUid($get_id, json_encode($data, JSON_UNESCAPED_UNICODE));
        //});
        go(function ()use($redis,$res){
            \co::sleep(1);
            $redis -> setbit('privateRead',$res,0);
        });
        return $res;
    }

    /**
     * 记录当前用户浏览的最后一条消息id
     * id       用户id
     * msg_id   消息id
     * @param Request $request
     * @return mixed
     */
    public function setLastMsgId(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','msg_id','is_rec'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $c_id = 'c' . $request->c_id;
        $b_id = 'b' . $request->b_id;
        //私聊由p_ 做前缀
        if($request -> is_rec == 0){
            $set = $redis -> set('p_' . $c_id . $b_id, $request->msg_id);
        }elseif ($request -> is_rec == 1){
            $set = $redis -> set('p_' . $b_id . $c_id, $request->msg_id);
        }

        if($set) return ReturnJson::json('ok',0,'记录成功');
        return ReturnJson::json('err',1,'记录失败');
    }

    /**
     * 获取用户未读消息
     * b_id         b端用户id
     * c_id         c端用户id
     * @param Request $request
     * @return mixed
     */
    public function getPrivateMsg(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','is_rec'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $c_id = 'c' . $request->c_id;
        $b_id = 'b' . $request->b_id;
        //获取当前用户浏览的最后一条消息id
        if($request->is_rec == 0){
            $id = $redis -> get('p_' . $c_id . $b_id);
        }elseif ($request->is_rec == 1){
            $id = $redis -> get('p_' . $b_id . $c_id);
        }

        //判断是否有消息记录
        if($id){
            $msg = PrivateMsg::where('b_id',$request->b_id)
                -> where('c_id',$request->c_id)
                -> where('id','>',$id)
                -> get();
            //获取消息是否已读
            $read = $redis -> pipeline(function ($pipe) use($msg){
                foreach ($msg as $value){
                    $pipe -> getbit('privateRead',$value->id);
                }
            });
            if($msg) return ReturnJson::json('ok',0,[$msg,$read]);
            return ReturnJson::json('err','1','服务器忙');
        }else{
            $msg = PrivateMsg::where('b_id',$request->b_id)
                -> where('c_id',$request->c_id)
                -> orderBy('id','desc')
                -> limit(20)
                -> get()
                -> toArray();
            $msg = array_reverse($msg);
            if($msg){
                //获取消息是否已读
                $read = $redis -> pipeline(function ($pipe) use($msg){
                    foreach ($msg as $value){
                        $pipe -> getbit('privateRead',$value['id']);
                    }
                });
            }
            if($msg) return ReturnJson::json('ok',0,[$msg,$read]);
            return ReturnJson::json('err',1,'服务器忙');
        }
    }

    /**
     * 检查是否有新消息
     * @param Request $request
     * @return mixed
     */
    public function getMsg(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','is_rec'],$request);
        if($error) return $error;

        $redis = $this->redis;
        $last = PrivateMsg::where('b_id',$request->b_id) -> where('c_id',$request->c_id) -> select('id') -> orderBy('id','desc') -> first();
        if($request -> is_rec == 0){
            $get = $redis -> get('p_' . 'c' . $request->c_id . 'b' . $request->b_id);
        }else{
            $get = $redis -> get('p_' . 'b' . $request->b_id . 'c' . $request->c_id);
        }

        if($last->id == $get){
            $res = PrivateMsg::where('b_id',$request->b_id) -> where('c_id',$request->c_id) ->orderBy('id','desc') -> take(20) -> get() -> toArray();
            $res = array_reverse($res);
            if($res) return ReturnJson::json('ok',0,$res);
            if($res == []) ReturnJson::json('err',15,[]);
            return ReturnJson::json('err',1,'服务器忙');
        }
        return ReturnJson::json('err',1,[]);
    }

    /**
     * 获取未读消息数
     * b_id         b端id
     * c_id         c端id
     * is_rec       用户所在客服端标示（0=>c,1=>b）
     * @param Request $request
     * @return mixed
     */
    public function getPrivateMsgNumber(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','is_rec'],$request);
        if($error) return $error;

        $redis = $this -> redis;
        $key = [];
        $number = [];
        if($request->is_rec == 0){
            $b_id = json_decode($request->b_id);
            foreach ($b_id as $value){
                array_push($key,'p_' . 'c' . $request->c_id . 'b' . $value);
            }
            //获取当前用户浏览的最后一条消息id
            $id = $redis -> mget($key);
            //循环获取到的id
            foreach ($id as $k => $val){
                $number[$b_id[$k]] = PrivateMsg::where('b_id',$b_id[$k])
                    -> where('c_id',$request->c_id)
                    -> where('id','>',$val)
                    -> select('id')
                    -> get()
                    -> count();
            }
        }elseif ($request->is_rec == 1){
            $c_id = json_decode($request->c_id);
            foreach ($c_id as $value){
                array_push($key,'p_' . 'b' . $request->b_id . 'c' . $value);
            }
            //获取当前用户浏览的最后一条消息id
            $id = $redis -> mget($key);
            //循环获取到的id
            foreach ($id as $k => $val){
                $number[$c_id[$k]] = PrivateMsg::where('b_id',$request->b_id)
                    -> where('c_id',$c_id[$k])
                    -> where('id','>',$val)
                    -> select('id')
                    -> get()
                    -> count();
            }
        }
        if(count($number) != 0) return ReturnJson::json('ok',0,$number);
        return ReturnJson::json('err',1,'无记录');
    }

    /**
     * 获取私聊中的最后一条信息
     * b_id         b端用户id
     * c_id         c端用户id
     * is_rec       客户端标示
     * @param Request $request
     * @return mixed
     */
    public function getLastPrivateMsg(Request $request)
    {
        $error = ReturnJson::parameter(['id','is_rec'],$request);
        if($error) return $error;

        //获取当前用户的好友列表
        $uid = $request->is_rec == 0 ? 'C' . $request->id : 'B' . $request->id;
        $redis = $this->redis;
        $friend = $redis -> zRevRange('P' . $uid,0,-1);

        //获取每个好友对应聊天的最后一条消息
        $res = [];
        if($request->is_rec == 0){
            $b_id = $friend;
            foreach ($b_id as $value){
                $res[$value] = PrivateMsg::where('b_id',$value)
                    -> where('c_id',$request->id)
                    -> select('id','massage','username','header','type','is_rec','created_at')
                    -> orderBy('id','desc')
                    -> first();
            }
        }elseif($request->is_rec == 1){
            $c_id = $friend;
            foreach ($c_id as $value){
                $res[$value] = PrivateMsg::where('b_id',$request->id)
                    -> where('c_id',$value)
                    -> select('id','massage','username','header','type','is_rec','created_at')
                    -> orderBy('id','desc')
                    -> first();
            }
        }
        if(count($res)) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'无记录');
    }

    /**
     * 标记消息为已读
     * @param Request $request
     * @return mixed
     */
    public function tabRead(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','is_rec'],$request);
        if($error) return $error;

        $is_rec = $request -> is_rec == '0' ? '1' : '0';
        $id = PrivateMsg::where('b_id',$request->b_id)
            -> where('c_id',$request->c_id)
            -> where('is_rec',$is_rec)
            -> selete('id')
            -> get();
        $redis = $this -> redis;
        $res = $redis -> pipeline(function ($pipe) use($id){
            foreach ($id as $value){
                $pipe -> setbit('privateRead',$value,1);
            }
        });
        if($res) return ReturnJson::json('ok',0,'yidu');
        return ReturnJson::json('err',1,'shibai');
    }

    /**
     * 标记一条为已读
     * @param Request $request
     * @return mixed
     */
    public function tabOneRead(Request $request)
    {
        $error = ReturnJson::parameter(['msg_id'],$request);
        if($error) return $error;

        $redis = $this->redis;
        $res = $redis -> setbit('privateRead',$request->msg_id,1);
        if($res == 0 || $res == 1) return ReturnJson::json('ok',0,'ok');
        return ReturnJson::json('err',1,'哦豁');
    }

    /**
     * 获取更多消息
     * b_id         b端用户id
     * c_id         c端用户id
     * first_id     最前一条消息的id
     * @param Request $request
     * @return mixed
     */
    public function getMorePrivateMsg(Request $request)
    {
        $error = ReturnJson::parameter(['b_id','c_id','first_id'],$request);
        if($error) return $error;

        $id = $request -> first_id <= 20 ? 1 : $request -> first_id - 20;
        $res = PrivateMsg::where('b_id',$request -> b_id)
            -> where('c_id',$request -> c_id)
            -> whereBetween('id',[$id,$request -> first_id])
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'服务器忙');
    }
}