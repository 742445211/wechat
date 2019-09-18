<?php

namespace App\Http\Controllers\Admin\Price;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class PriceController extends Controller
{
    //查看工资列表
    public function index()
    {
    	//先查询当钱管理员所创建的群  通过群才能证明这个工作有人报名并且产生工资信息
    	$adminid = session('userid');
    	$adminGroup = DB::table('workgroup') -> where('adminid',$adminid) -> get();//管理员的群
        $num = count($adminGroup);
        $All = [];
        //查询总工资
        if($adminGroup){
            //通过workid查询sign表里面的签到信息，拿到详情工资表
            for ($i=0; $i < count($adminGroup); $i++) {
                $allprice = 0;  ///初始值
                $workId = $adminGroup[$i] -> workid;
                $price_detail_workid = DB::table('sign') -> where('workid',$workId) -> value('workid');   //每个工作的所有签到信息
                $prices = DB::table('price_detail') -> where('workid',$price_detail_workid) -> get();
                if(count($prices) > 0){
                    foreach($prices as $k => $v){
                       $allprice += (float)$v -> allprice - (float)$v -> pun + (float)$v -> reward;
                       $workid = $v -> workid;
                    }
                    $data['workid']   = $workid;
                    $data['allprice'] = $allprice;
                    $All[$i] = $data;
                }else{
                    $All[$i] = '';
                }
                
            }
            return view('Admin.Price.price',['all'=>$All,'adminGroup'=>$adminGroup,'nums'=>$num]);
            //dump($All,$adminGroup);
        }
    }
    //单个兼职的详细工资列表
    public function workPrice(Request $request)
    {
    		$workName = DB::table('work') -> where('id',$request -> workid) -> value('title');  //标题的工作title //
    		$member = DB::table('workgroup') -> where('workid',$request -> workid) -> first();   //该工作群的信息
    		$group_member = explode(',',rtrim($member -> group_member,','));   //该群的所有用户成员
    		$member_arr = array_merge(array_diff($group_member, array($member -> adminid)));  //去除管理员
    		//查询每个用户的信息
    		$data = [];
    		for ($i=0; $i < count($member_arr); $i++) { 
    			$userId = $member_arr[$i]; //单个用户的id
    			//需要查询用户姓名，工资合计，工资状态
    			$username = DB::table('homeuser') -> where('id',$userId) -> value('username');  //用户名    //
    			$userPrice = DB::table('price_detail') -> where('userid',$userId) -> where('workid',$request -> workid) -> get();   //用户在该兼职的签到和工资信息
    				$priceNum = 0;  //单用户总工资合计    //
    				$status_text = '无薪资';   //
    				if($userPrice){  //如果用户有签到信息
    					for ($p=0; $p < count($userPrice); $p++) { 
	    					$priceNum += (float)$userPrice[$p] -> allprice - (float)$userPrice[$p] -> pun + (float)$userPrice[$p] -> reward;   //
	    					$status[$p] = $userPrice[$p] -> price_status;   //用户的薪资状态
	    					if(in_array('1', $status) && !in_array('0', $status)){
			                     	//工资全部发完
			                     	$status_text = '已发完';
			                     }elseif(in_array('0', $status) && !in_array('1', $status)){
			                     	//一分未发
			                     	$status_text = '薪资未发';
			                     }elseif(in_array('0', $status) && in_array('1', $status)){
			                     	//发了一部分
			                     	$status_text = '未发完';
			                }
	    				}
    				}
	    			$data[$i]['username']    = $username;  //姓名
	    			$data[$i]['pricenum']    = $priceNum;	//工资合计
	    			$data[$i]['status_text'] = $status_text;	//工资状态
	    			$data[$i]['userid']      = $userId;	//用户id
    			}   
    		return view('Admin.Price.useprice',['username'=>$data,'workid'=>$request -> workid,'num'=>count($member_arr),'workname'=>$workName]);
    }
    //ajax单个用户发工资
    public function putPrice(Request $request)
    {
    	$res = DB::table('price_detail') -> where('userid',$request -> userid) -> where('workid',$request -> workid) -> update(['price_status' => '1']);
    	if($res){
    		return '0';
    	}else{
    		return '1';
    	}
    }
    //单个用户的所有签到和工资薪资
    public function userPrice(Request $request)
    {
    	$user_sign = DB::table('price_detail') -> where('userid',$request -> userid) -> where('workid',$request -> workid) -> get();
    	$num = count($user_sign);
    	$data = [];
    	$price = 0;
    	if($user_sign){
    		//如果该用户的签到信息不为空
    		foreach ($user_sign as $key => $value) {
    			if($value -> reward == null){  //奖
    				$reward = 0;
    			}else{
    				$reward = (float)$value -> reward;
    			}
	    			if($value -> pun == null){  //罚
	    				$pun = 0;
	    			}else{
	    				$pun = (float)$value -> pun;
	    			}
    			$data[$key]['date']            = $value -> date;      //签到日期
    			$data[$key]['price_detail_id'] = $value -> id;      //详情表id
    			$data[$key]['signin_time']     = explode(' ',$value -> signin_time)[1];  //签到时间 只取时间

    			if($value -> signout_time){   //签退时间不为空
    				$data[$key]['signout_time'] = explode(' ',$value -> signout_time)[1];  //签退时间
    			}else{
    				$data[$key]['signout_time'] = '未签退';
    			}

    			$data[$key]['reward']   = $reward;  //奖励
    			$data[$key]['pun']      = $pun;  //罚
    			$data[$key]['allprice'] = (float)$value -> price - $pun + $reward;  //最终工资
    			$data[$key]['status']   = $value -> price_status;  //工资发放状态
                $price += $data[$key]['allprice'];   //工资合计
    		}
    	}
    	//查询当前用户姓名
    	$userName = DB::table('homeuser') -> where('id',$request -> userid) -> value('username');
    	return view('Admin.Price.signlist',['usersign' => $data,'num' => $num,'username' => $userName,'price' => $price]);
    }
    //每天给单个用户发工资
    public function dayPrice(Request $request)
    {
    	$res = DB::table('price_detail') -> where('id',$request -> id) -> update(['price_status' => '1']);
    	if($res){
    		return 1;  //ok
    	}else{
    		return 0;  //err
    	}
    }
    //用户的账户管理
    public function account(Request $request)
    {
    	//获取该管理员发布的兼职信息，通过加群成功的数据查询所有的用户
    	$adminId = session('userid');
    	$adminGroup = DB::table('workgroup') -> where('adminid',$adminId) -> get();
    	//var_dump($adminGroup);exit;
    	if($adminGroup){
    	    $member = [];
    	    $data = [];
    	    $num = 0;//默认输出数据为空
    		//通过循环拿出所有群成员
    		for ($i=0; $i < count($adminGroup); $i++) { 
    			$member[$i] = rtrim($adminGroup[$i] -> group_member,',');
    		}
            $newMember  = implode(',',$member);   //把拿到的每个群的群成员拼起来
            $arr_member = explode(',',$newMember);   //再拆成每个用户
            $arr        = array_unique($arr_member);    //去除重复的用户
            $member_arr = array_merge(array_diff($arr, array($adminId)));  //再去掉管理员
            $newUserId  = implode(',',$member_arr);   //每位用户再拼回去
            //判断该用户组是否含有除了管理员之外的其他成员
            if($newUserId){
                $accountInfo = DB::select("select * from account where userid in(".$newUserId.")"); //查询他们的银行卡信息
                foreach ($accountInfo as $key => $value) {
                    $data[$key]['username'] = DB::table('homeuser') -> where('id',$value -> userid) -> value('username');
                    $data[$key]['type']     = DB::table('moneytype') -> where('id',$value -> type) -> value('type');
                    $data[$key]['num']      = $value -> num;
                    $data[$key]['a_in']     = $value -> a_in;
                    $data[$key]['name']     = $value -> name;
                }
                $num = count($accountInfo);
            }
    		return view('Admin.Price.account',['data' => $data,'num' => $num]);
    	}
    }

    //结算完成，工作完成
    public function workOver()
    {

    }
}
