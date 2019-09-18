<?php

namespace App\Http\Controllers\Admin\TokeWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use GatewayClient\Gateway;
class TokeWorkController extends Controller
{
    //加载当前管理员所有工作群
	public function index()
	{
		//通过session里面suerid查询当前管理员下面的所有群
		$adminGroup = DB::table('workgroup') -> where('adminid',session('userid')) -> get();  //管理员的群
		
		$data = [];  //提前定义数组
		$num = '0';  //群数量
		if($adminGroup){
			//今日日期
			$date_now = date('Y-m-d',time());
			for($i=0;$i<count($adminGroup);$i++){
				$groupId = $adminGroup[$i] -> workid;
				$data[$i]['title']   = $adminGroup[$i] -> group_title;
				$data[$i]['num']     = $adminGroup[$i] -> group_num;
				 //查询今日的签到情况
				$data[$i]['sign']    = DB::table('sign') -> where('workid',$groupId) -> where('date',$date_now)-> count();
				$data[$i]['groupid'] = $adminGroup[$i] -> group_id;
				$data[$i]['workid']  = $adminGroup[$i] -> workid;
                $data[$i]['issign']  = $adminGroup[$i] -> sign_status;
			}
			$num = count($data);
			return view('Admin.TokeWork.tokework',['data'=>$data,'num'=>$num]);
		}else{
			//群为空
			return view('Admin.TokeWork.tokework',['data'=>$data,'num'=>$num]);
		}
	}
	//把管理员的id和所有的群id绑定
	public function bindId(Request $request)
	{
		$uid = session('userid');
		$client_id = $request -> client_id;
		Gateway::$registerAddress = '127.0.0.1:1238';
		Gateway::bindUid($client_id, $uid);  //把用户id和clientid绑定在一起
		$adminGroup = DB::table('workgroup') -> where('adminid',$uid) -> get();
		if($adminGroup){
			for ($i=0; $i < count($adminGroup); $i++) { 
			$groupid = $adminGroup[$i] -> group_id;
			//把用户的群和clientid循环绑定
			Gateway::joinGroup($client_id, $groupid);
		}
			return ['msg'=>'ok','code'=>'0','result'=>'绑定完成'];
		}	
	}
	//进入某个群并查询聊天记录和群成员以及群成员数量并且加载群公告
	public function workdetail(Request $request)
	{
		//查询群聊天记录  需要groupid
		$groupid =  $request -> groupid;
		$times = time();
		// $workMsg = DB::table('group_msg') -> where('groupid',$groupid) -> take(30) -> get();
		$workMsgs = file_get_contents("https://www.xiaoshetong.cn/wechat/getoldmsg?times=".$times."&groupid=".$request -> groupid."");
		$workMsg = json_decode($workMsgs,true);
		return view('Admin.TokeWork.workdetail',['workmsg'=>$workMsg,'groupid'=>$groupid]);
	}
	//进入群聊天界面
	public function godetail(Request $request)
	{
		$times = time();
		$msg = $request -> msg;
		$userid = session('userid');
		$groupid = $request -> groupid;
		//消息写入数据库
		$userInfo = DB::table('adminuser') -> where('id',$userid) -> first();
		$data['header'] = $userInfo -> header;
      	$data['username'] = $userInfo -> username;
  		$data['userid'] = $userid;
  		$data['groupid'] = $groupid;
  		$data['sendtime'] = date('Y-m-d H:i:s',$times);
  		$data['msg'] = $msg;
		DB::table('group_msg') -> insert($data);
		$res = file_get_contents("https://www.xiaoshetong.cn/wechat/sendmsg?times=".$times."&groupid=".$groupid."&userid=".$userid."&msg=".$msg."");
		$sendOk = json_decode($res,true);
		return $sendOk;
	}
	//解散群
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
                        $change = explode(',',$changes);
                        $change =array_diff($change,array($group->group_id));
                        $changes = implode(',',$change);
                        $users = DB::table('homeuser') -> where('id',$v) -> update(['mygroup'=>$changes]);
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
	//查看私信
	public function salary()
	{
		return '敬请期待。';
	}
	//群公告
	public function gg($groupid)
	{
			//get传值方法需要判断传入的值
			$groupid_arr = [];
			$adminGroup = DB::table('workgroup') -> where('adminid',session('userid')) -> get();  //管理员的群
			if(!$adminGroup){
				return '请不要越界操作,多次后将封禁账号,如非本人操作请联系超级管理员!';
			}
			for ($i=0; $i < count($adminGroup); $i++) { 
				$groupid_arr[$i] = $adminGroup[$i] -> group_id;
			}
			if(!in_array( $groupid,$groupid_arr)){  //判断传入的值是否合法
				return '请不要越界操作,多次后将封禁账号,如非本人操作请联系超级管理员!';
			}
		//加载房企只能管理员的群公告
		$notice = DB::table('notice') -> where('groupid',$groupid) -> get();
		$num = count($notice);
		$group_title = DB::table('workgroup') -> where('group_id',$groupid) -> first();
		return view('Admin.TokeWork.gglist',['data' => $notice,'num' => $num,'grouptitle' => $group_title]);
	}

	//添加群公告页面
	public function addgg($groupid)
	{
		return view('Admin.TokeWork.addgg',['groupid' => $groupid]);
	}
	//处理添加群公告
	public function doaddgg(Request $request)
	{
		$data['content'] = $request -> res;
		$data['groupid'] = $request -> gid;
		$data['adminid'] = session('userid');
		$data['addtime'] = time();
		$data['status'] = 0;
		if(DB::table('notice') -> insert($data)){
			return '1';  //ok
		}else{
			return '0';
		}
	}
	//修改群公告
	public function editgg(Request $request)
	{
		$id = $request -> id;
		$res = $request -> v;
		// return json_encode($request -> all(0));
		if(!$res){
			return '0';
		}
		if(DB::table('notice') -> where('id',$id) -> update(['content' => $res])){
			return '1';
		}else{
			return '0';
		}
	}
	//发送公告通知短信
	public function send_sms(Request $request)
	{
		$url = "http://v.juhe.cn/sms/send";
		$groupid = $request -> groupid;
		//拿到群id查找所有的群成员   然后拿到成员的电话号码，再发送短信
		$member = DB::table('workgroup') -> where('group_id',$groupid) -> value('group_member');
		$member_arr = explode(',',rtrim($member,','));
		//循环查询用户电话号码
		$phone_number = [];  //电话号码
		for ($i=0; $i < count($member_arr); $i++) { 
			$phone = DB::table('homeuser') -> where('id',$member_arr[$i]) -> value('phone');
			$phone_number[$i] = $phone;
		}
		//循环发送短信
		$err_num = 0;   //发送失败条数
		$send_ok = 0;   //发送成功条数
		$send_err = 0;   //发送失败人数
		for ($i=0; $i < count($phone_number); $i++) { 
			if($phone_number[$i]){  //如果手机号码不为空
				$params = array(
				    'key'   => '6b56f0bfe1af609232227466899c4776', //您申请的APPKEY
				    'mobile'    => "$phone_number[$i]", //接受短信的用户手机号码
				    'tpl_id'    => '111106', //您申请的短信模板ID，根据实际情况修改
				    'tpl_value' =>'' //您设置的模板变量，根据实际情况修改
				);
				$paramstring = http_build_query($params);
				$content = TokeWorkController::juheCurl($url, $paramstring);
				$result = json_decode($content, true);
				if($result['result']['fee'] == '1'){
					$send_ok += 1;   //成功条数
				}else{
					$send_err += 1;  //失败条数
				}
			}else{
				$err_num ++;   //电话号码为空用户 
			}
		}
		$err_sms = $send_err + $err_num;   //失败总条数
		//成功后更改状态为已发送短信通知
		if(DB::table('notice') -> where('groupid',$groupid) -> update(['status' => 1])){
			return json_encode(['msg' => 'ok','code' => '0', 'result' => $err_sms,'oksms' => $send_ok]);
		}else{
			return json_encode(['msg' => 'err','code' => '1', 'result' => $err_sms,'oksms' => $send_ok]);
		}
	}

	//开启关闭签到签退
	public function isSign(Request $request){
	    $status = $request->status == 4 ? 0 : $request->status;
        $is_status = DB::table('workgroup') -> where('group_id',$request->groupid) -> update(['sign_status' => $status]);
        if($is_status){
            return ['msg' => 'ok', 'code' => "{$status}", 'result' => '成功'];
        }
    }
/**
 * 请求接口返回内容
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function juheCurl($url, $params = false, $ispost = 0)
{
    $httpInfo = array();
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    if ($ispost) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_URL, $url);
    } else {
        if ($params) {
            curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
        }
    }
    $response = curl_exec($ch);
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
    curl_close($ch);
    return $response;
}

}
