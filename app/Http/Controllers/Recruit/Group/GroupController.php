<?php


namespace App\Http\Controllers\Recruit\Group;


use App\Http\Controllers\Controller;
use App\Recruiter;
use FromId;
use GatewayClient\Gateway;
use Illuminate\Http\Request;
use DB;

/**
 * 群聊
 * Class GroupController
 * @package App\Http\Controllers\Recruit\Group
 */
class GroupController extends Controller
{

    /**
     * 获取当前用户的ID
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $times = time();
        if($request -> token && $times - $request -> times < 10){

            $userInfo = Recruiter::where('token','=',$request -> token) -> first();

            if($userInfo){
                $user['id']      = $userInfo -> id;
                $user['mygroup'] = $userInfo -> mygroup;
                return ['msg'=>'ok', 'code'=>'0','result'=>$user];
            }else{
                return ['msg'=>'err','code'=>'1','result'=>'用户不存在'];
            }
        }else{
            return ['msg'=>'err','code'=>'2','result'=>'非法请求'];
        }
    }

    /**
     * 获取当前用户所有群的最后一条消息
     * @param Request $request
     * @return array
     */
    public function getLastMsg(Request $request)
    {
        //拿着群id和用户id查询group_msg表里面的最后一条消息
        $times = time();
        if($request -> userid && $request -> groupid && $times - $request -> times < 10){
            //处理群id
            $group_id_arr = explode(',',rtrim($request -> groupid,','));

            for ($i=0; $i < count($group_id_arr); $i++) {
                $result[$i] = DB::select("select max(id) as maxid from group_msg where groupid = " . $group_id_arr[$i] . " ");  //拿到最近两个消息的id
            }

            for($i=0;$i<count($result);$i++){
                $id = $result[$i]['0'] -> maxid;
                $lastMsg[$i] = DB::table('group_msg') -> where('id','=',$id) -> first();
            }

            return ['msg'=>'ok', 'code'=>'0','result'=>$lastMsg];
        }else{
            return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
        }
    }

    /**
     * 把用户ID和client_id绑定
     * @param Request $request
     * @return array
     */
    public function idBind(Request $request)
    {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
        Gateway::$registerAddress = '127.0.0.1:1238';

        $uid       = $request -> userid;   //用户id
        $workGroup = $request -> mygroup;  //所有群
        $client_id = $request -> client_id;   //client_id
        // client_id与uid绑定
        Gateway::bindUid($client_id, $uid);   //现在就是把client_id换成用户的uid，发消息就用uid了
        // 加入某个群组（可调用多次加入多个群组
        if($workGroup){  //处理群id
            $myGroupArr = explode(',',rtrim($workGroup,','));
            for($i=0; $i < count($myGroupArr); $i++) {
                Gateway::joinGroup($client_id, $myGroupArr[$i]);//把用户的群和client_id循环进行绑定
            }
            //如果不为空的话就把所有的群id拿出来遍历出名字 然后放在当前用户的工作群列表里面
            $groupId       = rtrim($workGroup,',');
            $groupResult[] = DB::select("select * from workgroup where group_id in(".$groupId.")");
            $backClient    = Gateway::getClientIdByUid($uid);  //获取当前用户id绑定的所有client_id
            return ['msg'=>'ok','code'=>'0', 'result'=>$groupResult];
        }else{
            return ['msg'=>'ok','code'=>'-1','result'=>'数据为空'];
        }
    }

    /**
     * 把聊天记录写入数据库
     * @param Request $request
     * @return array
     */
    public function putMsg(Request $request)
    {
        $times = time();
        if($request -> userid && $request -> groupid && $times - $request -> times < 10 && $request -> msg)
        {
            $userInfo = DB::table('homeuser') -> where('id',$request -> userid) -> first();
            $data['header']   = $userInfo -> header;
            $data['username'] = $userInfo -> username;
            $data['userid']   = $request -> userid;
            $data['groupid']  = $request -> groupid;
            $data['sendtime'] = date('Y-m-d H:i:s',$times);
            $data['msg']      = $request -> msg;

            if(DB::table('group_msg') -> insert($data)){
                return ['msg'=>'ok', 'code'=>'0','result'=>'success!'];
            }else{
                return ['msg'=>'err','code'=>'1','result'=>'error!'];
            }

        }
    }

    /**
     * 获取群消息记录和群资料
     * @param Request $request
     * @return array
     */
    public function getOldMsg(Request $request)
    {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
        Gateway::$registerAddress = '127.0.0.1:1238';
        $times = time();
        if($times - $request -> times < 10 && $request -> groupid){
            //获取群消息记录30条
            $oldMsg = DB::table('group_msg') -> where('groupid',$request -> groupid) -> orderBy('sendtime','desc') -> take(30) -> get();
            //获取群资料
            $groupInfo = DB::table('workgroup') -> where('group_id',$request -> groupid) -> first();
            //获取在线人数
            $onlineNum = Gateway::getClientIdCountByGroup($request -> groupid);
            return ['msg'=>'ok', 'code'=>'0','oldMsg'=>$oldMsg,'groupInfo'=>$groupInfo,'onlineNum' => $onlineNum];
        }else{
            return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
        }
    }

    /**
     * 发送群聊消息
     * @param Request $request
     * @return array
     */
    public function sendMsg(Request $request)
    {
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
        Gateway::$registerAddress = '127.0.0.1:1238';
        $times = time();
        if($times - $request -> times < 10 && $request -> msg && $request -> userid && $request -> groupid){
            $resData = [
                'type' => 'say',               //该条消息的类型（自定义）
                'msg'  => $request -> msg       //发送的消息
            ];
            //发送群消息
            Gateway::sendToGroup($request -> groupid, json_encode($resData));
            //发送后需要生成一个消息列表数据
            //首先查询是否已经存在了
            $id = DB::table('msglist') -> where('userid',$request -> userid) -> where('groupid',$request -> groupid) -> value('id');
            if($id){
                //存在   则更新最后一次消息时间
                $time['toketime'] = date('m-d H:i',time());
                DB::table('msglist') -> where('id',$id) -> update($time);
            }else{
                //不存在则创建一个当前用户的消息表
                $data['userid']   = $request -> userid;  //用户id
                $data['is_group'] = '0';    // 0为群消息
                $data['groupid']  = $request -> groupid;//群id
                $data['toketime'] = date('m-d H:i',time());
                DB::table('msglist') -> insert($data);
            }
            return ['msg'=>'ok', 'code'=>'0','result'=>'发送成功'];
        }else{
            return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
        }
    }

    /**
     * 获取群详情数据
     * @param Request $request
     * @return array
     */
    public function getGroupInfo(Request $request)
    {
        $times = time();
        if($times - $request -> times < 10 && $request -> groupid){
            $groupInfo = DB::table('workgroup') -> where('group_id',$request -> groupid) -> first();
            //拿到群成员查询所有的头像和用户名
            $memberArr = explode(',',rtrim($groupInfo->group_member,','));
            $data['num'] = count($memberArr); //群成员数
            if($data['num']>10){   //只遍历十个用户显示详情
                $num = 10;
            }else{
                $num = $data['num'];
            }
            for ($i=0; $i < $num; $i++) {
                if($memberArr[$i] == $groupInfo->adminid){
                    //证明是管理员
                    $adminInfo = DB::table('recruiter') -> where('id',$groupInfo->adminid) -> first();
                }else{
                    $userInfo[$i] = DB::table('homeuser') -> where('id',$memberArr[$i]) -> first();
                }
            }
            return ['msg'=>'ok','code'=>'0','result'=>$userInfo,'admin' => $adminInfo,'num' => $data['num']];
        }else{
            return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
        }
    }

    /**
     * 获取群公告消息
     * @param Request $request
     * @return array
     */
    public function getGG(Request $request)
    {
        $times = time();
        if($times - $request -> times < 10 && $request -> groupid){
            //查询当前群id下面的多有公告，按照时间倒序排序
            $gg = DB::table('notice') -> where('groupid',$request -> groupid) -> orderBy('addtime','desc') -> get();
            $sendName = DB::table('recruiter') -> where('id',$gg[0] -> adminid) -> value('username');
            $data = [];
            foreach($gg as $key => $val){
                $data[$key]['sendname'] = $sendName;
                $data[$key]['senddate'] = date('Y-m-d H:i',$val -> addtime);
                $data[$key]['content']  = $val -> content;
            }
            return ['msg'=>'ok','code'=>'0','gginfo' => $data];
        }
    }

    /**
     * 获取最新公告
     * @param Request $request
     * @return array
     */
    public function newGG(Request $request)
    {
        $time = time();
        if(($time - $request->time) < 10){
            $gg = DB::table('notice')
                -> where('groupid',$request->groupid)
                -> orderByRaw('id DESC')
                -> first();
            if($gg){
                return ['msg'=>'ok','code'=>'1','result' => $gg];
            }else{
                return ['msg'=>'err','code'=>'0','result' => '无公告'];
            }
        }else{
            return ['msg'=>'err','code'=>'0','result' => '非法请求'];
        }
    }

    /**
     * 解散群
     * @param Request $request
     * @return array
     */
    public function delwork(Request $request)
    {
        $time = time();
        if(($time - $request->time) < 10 && $request->token){
            $group = DB::table('workgroup') -> where('group_id',$request->groupid) -> first();//查询工作ID
            $workid = $group->workid;
            $no_price = DB::table('price_detail')
                -> where('workid',$workid)
                -> where('price_status',0)
                -> get();//查询未发工资人数
            $num = count($no_price);
            if($num){//如果还有人未发工资,未发完返回数据
                return ['msg' => 'err', 'code' => 0, 'result' => "还有{$num}人未发放工资，不能删除该群"];
            }else{//若发完工资，则删除该群
                foreach($group as $k=>$v){
                    $del_group[$k] = $v;
                }

                //把相关用户的群所属删除
                $user = explode(',',$del_group['group_member']);
                foreach($user as $v){
                    if($v != $group->adminid){
                        $changes = DB::table('homeuser') -> where('id',$v) -> value('mygroup');
                        $change  = explode(',',$changes);
                        $change  = array_diff($change,array($group->group_id));
                        $changes = implode(',',$change);
                        $users   = DB::table('homeuser') -> where('id',$v) -> update(['mygroup'=>$changes]);
                    }
                }

                $res = DB::table('del_workgroup') -> insert($del_group);//先把要删除的群放入另一张表中
                $msg_list = DB::table('msglist') -> where('groupid',$group->group_id) -> delete();//删除消息列表记录
                $msg = DB::table('group_msg') -> where('groupid',$group->group_id) -> get()->map(function ($value) {
                    return (array)$value;
                })->toArray();//查询该群所有聊天记录
                $insert = DB::table('re_group_msg') -> insert($msg);//把该群所有聊天记录转存到记录表
                DB::table('group_msg') -> where('groupid',$group->group_id) -> delete();//删除该群所有聊天记录
                $del = DB::table('workgroup') -> where('group_id',$group->group_id) -> delete();//把群删除
                if($res && $del){

                    return ['msg' => 'ok', 'code' => 1, 'result' => "该群已删除"];
                }
            }
        }else{
            return ['msg' => 'err', 'code' => 3, 'result' => '非法操作'];
        }
    }

    /**
     * 添加公告
     * @param Request $request
     * @return array
     */
    public function addGG(Request $request)
    {
        $data['content'] = $request -> content;
        $data['groupid'] = $request -> groupid;
        $data['adminid'] = $request->recruit_id;
        $data['addtime'] = time();
        $data['status']  = 0;

        $a = FromId::recruitSave($request->fromid);

        if(DB::table('notice') -> insert($data)){
            return ['msg'=>'ok', 'code'=>0,'result'=>'添加成功'];  //ok
        }else{
            return ['msg'=>'err','code'=>1,'result'=>'添加失败'];
        }
    }

    /**
     * 修改公告
     * @param Request $request
     * @return array
     */
    public function editGG(Request $request)
    {
        $data['content'] = $request -> content;
        $id = $request->id;
        if(DB::table('notice') -> where('id',$id) -> update($data)){
            return ['msg'=>'ok', 'code'=>0,'result'=>'修改成功'];  //ok
        }else{
            return ['msg'=>'err','code'=>1,'result'=>'修改失败'];
        }
    }

    /**
     * 开启关闭签到签退
     * @param Request $request
     * @return array
     */
    public function isSign(Request $request){
        $status = $request->status == 4 ? 0 : $request->status;
        $is_status = DB::table('workgroup') -> where('group_id',$request->groupid) -> update(['sign_status' => $status]);
        if($is_status){
            return ['msg' => 'ok', 'code' => "{$status}", 'result' => '成功'];
        }
    }

}