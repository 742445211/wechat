<?php

namespace App\Http\Controllers\Admin\Cplt;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use GatewayClient\Gateway;

class CpltController extends Controller
{
	public function index($status)
	{
		switch($status)
		{
			case 'all':
				//获取到全部报名表信息
				$cplt = DB::table('cplt') -> where('adminid','=',session('userid')) -> where('status','<>',4) -> get();
				break;
			case 'ready':
				//待处理  状态为0 
				$cplt = DB::table('cplt') -> where('status','=',0) -> where('adminid','=',session('userid')) ->get();
				break;
			case 'ms':
				//待面试  状态为1
				$cplt = DB::table('cplt') -> where('status','=',1) -> where('adminid','=',session('userid')) ->get();
				break;
			case 'ok':
				//审核通过（直接）  状态为2
				$cplt = DB::table('cplt') -> where('status','=',2) -> where('adminid','=',session('userid')) ->get();
				break;
			case 'no':
				//审核未通过  状态为3
				$cplt = DB::table('cplt') -> where('status','=',3) -> where('adminid','=',session('userid')) ->get();
				break;
			default :
				$cplt = DB::table('cplt') ->where('adminid','=',session('userid')) -> get();
				break;
		}
		
		$all = [];
		//拿到发布人和兼职标题
		if(!empty($cplt)){
			foreach($cplt as $key=>$val){
				//查询id
				$list['id'] = $cplt[$key] -> id;
				//兼职id
				$list['workid'] = $cplt[$key] -> workid;
				//当前报名的状态
				$list['status'] = $cplt[$key] -> status;
				//用pid查询发布者的姓名
				$list['adminname'] = DB::table('adminuser') -> where('id','=',$cplt[$key] -> id) -> value('username');
				//查询报名用户信息
				$list['username'] = DB::table('homeuser') -> where('id','=',$cplt[$key] -> userid) -> value('username');
				$list['phone'] = DB::table('homeuser') -> where('id','=',$cplt[$key] -> userid) -> value('phone');
				//通过兼职id查询兼职标题
				$list['worktitle'] = DB::table('work') -> where('id','=',$cplt[$key] -> workid) -> value('title');
				//报名时间
				$list['cplttime'] = $cplt[$key] -> cplttime;
				$all[$key] = $list;
			}
		}
		$num = count($all);
		return view('Admin.Cplt.cplt',['all'=>$all,'num'=>$num]);
	}
	//报名状态更改   为0时更改为1或2或3
	public function cpltstatus(Request $request)
	{
		$time = time();
		//查询现在的面试状态
		$data = DB::table('cplt')->where('id','=',$request->input('id'))->value('status');
		if($data == 0 && $request -> status == 'no'){
			//no时拒绝   状态改为3
			if(DB::table('cplt')->where('id','=',$request->input('id'))->update(['status'=>3])){
              $userId = DB::table('cplt') -> where('id',$request->input('id')) -> value('userid');
              $userPhone = DB::table('homeuser') -> where('id',$userId) -> value('phone');
				//写入现在的操作时间
              	//给用户发送审核失败短信
              $url = "http://v.juhe.cn/sms/send";
              $params = array(
                  'key'   => '6b56f0bfe1af609232227466899c4776',
                  'mobile'    => $userPhone, //接受短信的用户手机号码
                  'tpl_id'    => '112444', 
                  'tpl_value' =>'' //您设置的模板变量，根据实际情况修改
              );
              $paramstring = http_build_query($params);
              $content = CpltController::juheCurl($url, $paramstring);
              $result = json_decode($content, true);
              
			DB::table('cplt') -> where('id','=',$request->input('id')) -> update(['prevtime'=>$time]);
			echo 3;
			}
			//等于 ms改为面试状态  1
		}else if($data == 0 && $request -> status == 'ms'){
			if(DB::table('cplt')->where('id','=',$request->input('id'))->update(['status'=>1])){
              //发送待面试短信
              
              $userId = DB::table('cplt') -> where('id',$request->input('id')) -> value('userid');
              $userPhone = DB::table('homeuser') -> where('id',$userId) -> value('phone');
				//写入现在的操作时间
              	//给用户发送审核失败短信
              $url = "http://v.juhe.cn/sms/send";
              $params = array(
                  'key'   => '6b56f0bfe1af609232227466899c4776',
                  'mobile'    => $userPhone, //接受短信的用户手机号码
                  'tpl_id'    => '112442', 
                  'tpl_value' =>'' //您设置的模板变量，根据实际情况修改
              );
              $paramstring = http_build_query($params);
              $content = CpltController::juheCurl($url, $paramstring);
              $result = json_decode($content, true);
              
				DB::table('cplt') -> where('id','=',$request->input('id')) -> update(['prevtime'=>$time]);
				echo 1;
			}
		}else{
			//直接通过  状态改为 2
			if(DB::table('cplt')->where('id','=',$request->input('id'))->update(['status'=>2])){
				DB::table('cplt') -> where('id','=',$request->input('id')) -> update(['prevtime'=>$time]);
				//然后拿到workid把用户id添加进兼职信息的面试通过字段
				$siuser = DB::table('work') -> where('id','=',$request -> workid) -> value('siuser'); //拿到当前兼职的所有面试通过用户
				//判断用户是否在报名列表   待确定
				//把当前用户字段拼接到兼职列表
				$siuser = $siuser.$request -> id.',';
					$workid = $request -> workid;
					$worktitle = DB::table('work') -> where('id','=',$request -> workid) -> value('title');
					$userid = DB::table('cplt') -> where('id','=',$request -> id) -> value('userid');
					$adminid = DB::table('work') -> where('id','=',$request -> workid) -> value('adminid');
					DB::table('work') -> where('id','=',$request -> workid) -> update(['siuser'=>$siuser]);

					//通过workid查询adminid,并且把用户id和workid添加到workgroup工作群里面
					$group_id = DB::table('workgroup') -> where('workid','=',$workid) -> where('adminid','=',$adminid) -> value('group_id');
              
                        //发送成功短信
                        $userPhone = DB::table('homeuser') -> where('id',$userid) -> value('phone');
                          //写入现在的操作时间
                          //给用户发送审核失败短信
                        $url = "http://v.juhe.cn/sms/send";
                        $params = array(
                            'key'   => '6b56f0bfe1af609232227466899c4776',
                            'mobile'    => $userPhone, //接受短信的用户手机号码
                            'tpl_id'    => '112443', 
                            'tpl_value' =>'' //您设置的模板变量，根据实际情况修改
                        );
                        $paramstring = http_build_query($params);
                        $content = CpltController::juheCurl($url, $paramstring);
                        $result = json_decode($content, true);
              
						if($group_id){
							//证明群已存在   直接把当前用户加入当前工作群
							$member = DB::table('workgroup') -> where('group_id',$group_id) -> value('group_member');
							//如果里面已经有人了，就把成员添加进去
							$newMember = $member . $userid . ',';
							//群成员数量自增1
							DB::table('workgroup') -> where('group_id','=',$group_id) -> increment('group_num');
							$new = DB::table('workgroup') -> where('group_id',$group_id) -> update(['group_member' => $newMember]);  //写回去
							// //给用户表加上群id
							$myGroup = DB::table('homeuser') -> where('id','=',$userid) -> value('mygroup');
								$newGroup = $myGroup.$group_id.',';
								DB::table('homeuser') -> where('id','=',$userid) -> update(['mygroup' => $newGroup]);
								echo 2;
						}else{   //群还不存在  新建
							$list['group_member'] = $userid.','.$adminid.',';  //第一个群成员和管理员
							$list['group_num'] = "2";
							$list['workid'] = $workid;
							$list['adminid'] = $adminid;   //原来是lastId
							$list['group_title'] = $worktitle;	//工作群名字
                          	$list['group_header'] = DB::table('work') -> where('id',$request -> workid) -> value('header');
								//如果群不存在（新群），就新建一个群
								if($lastId = DB::table('workgroup') -> insertGetId($list)){
									//新建一条管理员发的消息
									$msg['groupid'] = $lastId;
									$msg['userid'] = $adminid;
									$adminUserInfo = DB::table('adminuser') -> where('id',$adminid) -> first();
									$msg['header'] = $adminUserInfo -> header;
									$msg['username'] = '【管理员】' . $adminUserInfo -> username;
									$msg['msg'] = '欢迎加入聊天群!';
									$msg['sendtime'] = date('Y-m-d H:i:s',time());
									
									//再把群id存入用户表的mygroup字段里面
									if($myGroup = DB::table('homeuser') -> where('id','=',$userid) -> value('mygroup')){
										$newGroup['mygroup'] = $myGroup.$lastId.',';
										DB::table('homeuser') -> where('id','=',$userid) -> update($newGroup);
									}else{
										$newGroup['mygroup'] = $lastId.',';
										DB::table('homeuser') -> where('id','=',$userid) -> update($newGroup);
									}
									if(DB::table('group_msg') -> insert($msg)){   //把管理员的出事消息插入聊天记录表
										echo 2;
									}
									
								}	
						}
			}
		}
	}
	
	//面试中状态更改
	public function cpltms(Request $request)
	{
		$time = time();
		$data = DB::table('cplt')->where('id','=',$request->input('id'))->value('status');
		if($data == 1 && $request -> status == 'no'){
			//no时拒绝   状态改为3
			if(DB::table('cplt')->where('id','=',$request->input('id'))->update(['status'=>3])){
              $userId = DB::table('cplt') -> where('id',$request->input('id')) -> value('userid');
              $userPhone = DB::table('homeuser') -> where('id',$userId) -> value('phone');
				//写入现在的操作时间
              	//给用户发送审核失败短信
              $url = "http://v.juhe.cn/sms/send";
              $params = array(
                  'key'   => '6b56f0bfe1af609232227466899c4776',
                  'mobile'    => $userPhone, //接受短信的用户手机号码
                  'tpl_id'    => '112444', 
                  'tpl_value' =>'' //您设置的模板变量，根据实际情况修改
              );
              $paramstring = http_build_query($params);
              $content = CpltController::juheCurl($url, $paramstring);
              $result = json_decode($content, true);
              
				DB::table('cplt') -> where('id','=',$request->input('id')) -> update(['prevtime'=>$time]);
				echo 3;
			}
			//等于 ok改为已通过面试 2
		}else if($data == 1 && $request -> status == 'ok'){
			if(DB::table('cplt')->where('id','=',$request->input('id'))->update(['status'=>2])){
				DB::table('cplt') -> where('id','=',$request->input('id')) -> update(['prevtime'=>$time]);

					$workid = $request -> workid;
					$userid = DB::table('cplt') -> where('id','=',$request -> id) -> value('userid');
					$adminid = DB::table('work') -> where('id','=',$request -> workid) -> value('adminid');
					$worktitle = DB::table('work') -> where('id','=',$request -> workid) -> value('title');
              
              			//发送成功短信
                        $userPhone = DB::table('homeuser') -> where('id',$userid) -> value('phone');
                          //写入现在的操作时间
                          //给用户发送审核失败短信
                        $url = "http://v.juhe.cn/sms/send";
                        $params = array(
                            'key'   => '6b56f0bfe1af609232227466899c4776',
                            'mobile'    => $userPhone, //接受短信的用户手机号码
                            'tpl_id'    => '112443', 
                            'tpl_value' =>'' //您设置的模板变量，根据实际情况修改
                        );
                        $paramstring = http_build_query($params);
                        $content = CpltController::juheCurl($url, $paramstring);
                        $result = json_decode($content, true);
              
					//通过workid查询adminid,并且把用户id和workid添加到workgroup工作群里面
						if($group_id = DB::table('workgroup') -> where('workid','=',$workid) -> where('adminid','=',$adminid) -> value('group_id')){
							//证明群已存在   直接把当前用户加入当前工作群
							$member = DB::table('workgroup') -> where('group_id','=',$group_id) -> value('group_member');
							//如果里面已经有人了，就把成员添加进去
							$newMember = $member . $userid.',';
							//群成员数量自增1
							DB::table('workgroup') -> where('group_id','=',$group_id) -> increment('group_num');
								if(DB::table('workgroup') -> where('group_id','=',$group_id) -> update(['group_member' => $newMember])){ 
								//写回去
								//给用户表加上群id
								$myGroup = DB::table('homeuser') -> where('id','=',$userid) -> value('mygroup');
								$newGroup['mygroup'] = $myGroup.$group_id.',';
								DB::table('homeuser') -> where('id','=',$userid) -> update(['mygroup' => $newGroup]);
								echo 2;
								} 
						}else{   //群还不存在  新建
							$list['group_member'] = $userid.','.$adminid.',';  //第一个群成员和管理员
							$list['group_num'] = "2";
							$list['workid'] = $workid;
							$list['adminid'] = $adminid;
							$list['group_title'] = $worktitle;	//工作群名字
                          	$list['group_header'] = DB::table('work') -> where('id',$workid) -> value('header');
								//如果群不存在（新群），就新建一个群
								if($lastId = DB::table('workgroup') -> insertGetId($list)){
									//新建一条管理员发的消息
									$msg['groupid'] = $lastId;
									$msg['userid'] = $adminid;
									$adminUserInfo = DB::table('adminuser') -> where('id',$adminid) -> first();
									$msg['header'] = $adminUserInfo -> header;
									$msg['username'] = '【管理员】' . $adminUserInfo -> username;
									$msg['msg'] = '欢迎加入聊天群!';
									$msg['sendtime'] = date('Y-m-d H:i:s',time());

									//再把群id存入用户表的mygroup字段里面
									if($myGroup = DB::table('homeuser') -> where('id','=',$userid) -> value('mygroup')){
										$newGroup['mygroup'] = $myGroup.$lastId.',';
										DB::table('homeuser') -> where('id','=',$userid) -> update($newGroup);
									}else{
										$newGroup['mygroup'] = $lastId.',';
										DB::table('homeuser') -> where('id','=',$userid) -> update($newGroup);
									}
									if(DB::table('group_msg') -> insert($msg)){   //把管理员的出事消息插入聊天记录表
										echo 2;
									}
								}	
						}
              //发送成功短信
              
			}
		}
	}
	//报名信息删除
	public function cpltdel(Request $request)
	{
		if(DB::table('cplt')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
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