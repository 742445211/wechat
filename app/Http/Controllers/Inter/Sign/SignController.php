<?php

namespace App\Http\Controllers\Inter\Sign;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SignController extends Controller
{
    //判断今天是否已经签到
    public function isSign(Request $request)
    {
    	$times = time();
    	if($times - $request -> times < 10 && $request -> token && $request -> groupid)
    	{
    		//查询是否已经签到
    		$userId = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
    		$workid = DB::table('workgroup') -> where('group_id',$request -> groupid) -> value('workid');
    		$sign = DB::table('workgroup') -> where('group_id',$request -> groupid) -> value('sign_status');
    		if($sign == 1){
                $date = date('Y-m-d',$times);
                //拿到用户id和工作id到签到表里面查询是否已经签到
                $signInTime = DB::table('sign')
                    -> where('userid',$userId)
                    -> where('workid',$workid)
                    -> where('date',$date)
                    -> value('signin_time');
                if($signInTime){
                    //不为空证明已经签到过了
                    return ['msg' => 'err','code' => '1','result' => '用户已签到'];
                }else{
                    return ['msg' => 'ok','code' => '0','result' => '未签到'];
                }
            }else{
                return ['msg' => 'err','code' => '3','result' => '当前无法进行签到操作!'];
            }
    	}else{
    		return ['msg' => 'err','code' => '2','result' => '非法操作'];
    	}
    }
    //进行签到
    public function goSign(Request $request)
    {
    	$times = time();
    	if($times - $request -> times < 10 && $request -> token && $request -> groupid && $request -> map)
    	{
    		$map = explode(',',$request -> map);//获取用户位置
    		$data['userid'] = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
    		$data['workid'] = DB::table('workgroup') -> where('group_id',$request -> groupid) -> value('workid');
    		$workInfo = DB::table('work') -> where('id',$data['workid']) -> first();
    		//给经纬度换个位置
    		$workMap = explode(',',$workInfo -> map);
    		$workMap = $workMap['1'].','.$workMap['0'];
    		$data['priceunit'] = $workInfo->price.'/'.$workInfo->days;
    		$data['date']   = date('Y-m-d',$times);
    		$data['signin_time'] = date('Y-m-d H:i:s',$times);
    		//签到写入信息
			if($signId = DB::table('sign') -> insertGetId($data)){
				//签到信息和工资信息写入price_detail表
				$price['date'] = $data['date'];    //当天签到日期
				$price['userid'] = $data['userid'];   //用户id
				$price['workid'] = $data['workid'];    //兼职id
				$price['signid'] = $signId;   //关联的签到表的id
				$price['signin_time'] = $data['signin_time'];   //签到时间
				$price['price_unit'] = $workInfo->days;    //薪资单位
                switch ($workInfo->days) {
                    case '1':   //为1按天算   不变
                        $price['price'] = $workInfo->price;    //基本薪资
                        $price['allprice'] = $workInfo->price;   //总计发放
                        break;
                    case '2':   //为2 按小时算  每天8小时
                        $dayprice = (int)$workInfo->price * 8;
                        $price['price'] = $dayprice;
                        $price['allprice'] = $dayprice;   //总计发放
                        break;
                    case '3':   //为3 按周算  每周5天工作日
                        $dayprice = (int)$workInfo->price / 5;
                        $price['price'] = $dayprice;
                        $price['allprice'] = $dayprice;   //总计发放
                        break;
                    case '4':   //为4 按半月算  每半月15天工作日
                        $dayprice = (int)$workInfo->price / 15;
                        $price['price'] = $dayprice;
                        $price['allprice'] = $dayprice;   //总计发放
                        break;
                    case '5':   //为5 按月算  每月30天工作日
                        $dayprice = (int)$workInfo->price / 30;
                        $price['price'] = $dayprice;
                        $price['allprice'] = $dayprice;   //总计发放
                        break;
                    case '6':   //为6 按季度算  每季度90天工作日
                        $dayprice = (int)$workInfo->price / 90;
                        $price['price'] = $dayprice;
                        $price['allprice'] = $dayprice;   //总计发放
                        break;
                    case '7':   //为7 按年算  每年360天工作日
                        $dayprice = (int)$workInfo->price / 360;
                        $price['price'] = $dayprice;
                        $price['allprice'] = $dayprice;   //总计发放
                        break;
                    default:    //其他就按天算
                        $price['price'] = $workInfo->price;    //基本薪资
                        $price['allprice'] = $workInfo->price;   //总计发放
                        break;
                }
				
				$price['price_status'] = '0';    //工资发放状态   0为未发放
				if($price_detail = DB::table('price_detail') -> insertGetId($price)){
					//吧详情表的id反写到签到表   让他们互相关联
					if(DB::table('sign') -> where('id',$signId) -> update(['price_detail' => $price_detail])){
						return ['msg'=>'ok','code'=>'0','result'=>'签到成功'];
					}
				}
			}else{
				return ['msg'=>'err','code'=>'1','result'=>'签到失败'];
			}
    	}else{
    		return ['msg' => 'err','code' => '2','result' => '非法操作'];
    	}
        
    }
    //判断是否签退
    public function isSignOut(Request $request)
    {
    	$times = time();
    	if($times - $request -> times < 10 && $request -> token && $request -> groupid){
    	//直接更新现在的时间到签退字段
    		$userId = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
    		$workId = DB::table('workgroup') -> where('group_id',$request -> groupid) -> value('workid');
    		$sign = DB::table('workgroup') -> where('group_id',$request -> groupid) -> value('sign_status');
    		//判断是否已经签过到
            if($sign == 3){
                //拿到用户id和工作id到签到表里面查询是否已经签到
                $signInTime = DB::table('sign')
                    -> where('userid',$userId)
                    -> where('workid',$workId)
                    -> where('date',date('Y-m-d',time()))
                    -> value('signin_time');
                if($signInTime){
                    //已签到，判断是否是重复签退
                    //判断是否已经签退
                    if(
                    DB::table('sign')
                        -> where('workid',$workId)
                        -> where('userid',$userId)
                        -> where('date',date('Y-m-d',time()))
                        -> value('signout_time')
                    ){
                        return ['msg' => 'err','code' => '2','result' => '已签退'];
                    }else{
                        return ['msg' => 'ok','code' => '0','result' => '未签退'];
                    }
                }else{

                    return ['msg' => 'err','code' => '1','result' => '未签到'];
                }
            }else{
                return ['msg' => 'err','code' => '4','result' => '当前无法进行签退操作！'];
            }
    	}else{
    		return ['msg' => 'err','code' => '3','result' => '非法请求'];
    	}
    }
    //进行签退
    public function signOut(Request $request)
    {
    	$times = time();
    	if($times - $request -> times < 10 && $request -> token && $request -> groupid){
    		//直接更新现在的时间到签退字段
    		$userId = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
    		$workId = DB::table('workgroup') -> where('group_id',$request -> groupid) -> value('workid');
    		//进行签到
    		$data['signout_time'] = date('Y-m-d H:i:s',$times);
	    		if(
	    			DB::table('sign') 
	    			-> where('workid',$workId) 
	    			-> where('userid',$userId)
	    			-> where('date',date('Y-m-d',time())) 
	    			-> update($data)
	    		){
	    			DB::table('price_detail') 
	    			-> where('workid',$workId) 
	    			-> where('userid',$userId)
	    			-> where('date',date('Y-m-d',time())) 
	    			-> update($data);
	    			return ['msg' => 'ok','code' => '0','result' => '签退成功'];
	    		}
    	}else{
    		return ['msg' => 'err','code' => '1','result' => '非法请求'];
    	}
    }
    //我的工资
    public function getPrice(Request $request)
    {
    	$times = time();
    	if($times - $request -> times < 10 && $request -> token){
    		//拿到token查询用户id
    		$userId = DB::table('homeuser') -> where('token',$request -> token) -> value('id');
    		//拿到用户id到cplt表查询status为2或者4的用户的工作id    2为正在工作   4位已经结束工作
    		$userWork = DB::table('cplt') -> where('userId',$userId) -> where('status','2') -> orwhere('status','4') -> get();
    		$price = [];
            $workInfo = [];
    		foreach($userWork as $key => $value){
    			$workInfo[]= $value -> workid;    //兼职id
    		}
    		$data = [];		//最终的数据盒子
    		$noPrice = [];  //未发放
    		$okPrice = [];   //已发放
            $info = [];
    		for($i=0;$i<count($workInfo);$i++){
    			//单个兼职的所有签到记录
    			$onlyWork = DB::table('price_detail') -> where('userid',$userId) -> where('workid',$workInfo[$i]) -> get();
    			//遍历出来
    			$onePrice = 0;
    			if($onlyWork){   //如果有签到记录
    				for ($m=0; $m < count($onlyWork); $m++) { 
    					if($onlyWork[$m]){
    						$onePrice += (int)$onlyWork[$m] -> price;   //单个兼职 单天的工资
    					}
    				}
    				$price[$i] = $onePrice;
    			}else{
    				//某个没有任何签到记录的工作
    				$price[$i] = "0";
    			}
    			//查询单条工作信息   把上面算出来的总工资推到数组后面
    			//查询薪资周期

    			$adminId = DB::table('work') -> where('id',$workInfo[$i]) -> value('adminid');
    			$data['workid'] = $workInfo[$i];
    			$data['title'] = DB::table('work') -> where('id',$workInfo[$i]) -> value('title');
    			$data['price'] = DB::table('work') -> where('id',$workInfo[$i]) -> value('price');
              	$data['header'] = DB::table('work') -> where('id',$workInfo[$i]) -> value('header'); 
    			$days = DB::table('work') -> where('id',$workInfo[$i]) -> value('days');
    			$data['days'] = DB::table('time') -> where('adminid',$adminId) -> where('id',$days) -> value('type');
    			$data['numPrice'] = $price[$i];
    			$info[$i] = $data;   //工资合计的最终整合
    			$data['status'] = DB::table('price_detail') -> where('userid',$userId) -> where('workid',$workInfo[$i]) -> value('price_status');
	    			if($data['status'] == '0'){
	    				unset($data['numPrice']);
	    				//未发放
	    				$noPrice[$i] = $data;
	    			}else if($data['status'] != ''){
	    				//已发放
	    				unset($data['numPrice']);
	    				$okPrice[$i] = $data;
	    			}
    			
    		}
    		return ['msg' => 'ok','code' => '0','result' => ['one'=>$info,'two'=>$noPrice,'three'=>$okPrice]];    //全部
    	}else{
    		return ['msg' => 'err','code' => '1','result' => '非法请求'];
    	}
    }
    //获取当前用户的账户信息
    public function getAccount(Request $request)
    {
        $times = time();
        $userAccount;  //提前定义
        if($times - $request -> times < 10 && $request -> token){
            $userId = DB::table('homeuser') -> where('token',$request -> token) -> value('id');
            $userAccount = DB::table('account') -> where('userid',$userId) -> first();
            //获取系统结算账户信息
            $moneyType = DB::table('moneytype') -> where('status','1') -> get();
            $data = [];
            for ($i=0; $i < count($moneyType); $i++) {
                $id = $moneyType[$i] -> id;
                $type = $moneyType[$i] -> type;
                array_push($data, $type);
            }
                return ['msg' => 'ok','code' => '0','result' => $userAccount,'data' => $data];
        }else{
            return ['msg' => 'err','code' => '1','result' => '非法请求'];
        }
    }
    //更新当前用户的账户信息
    public function upAccount(Request $request)
    {
        $times = time();
        if($times - $request -> times < 10 && $request -> token && $request -> type && $request -> name && $request -> num && $request -> a_in){
            //通过   写入数据库
            $userId = DB::table('homeuser') -> where('token',$request -> token) -> value('id');

            // $arr = ["请选择", "支付宝", "微信", "银行卡", "手机话费"];
            $data['userid'] = $userId;   //用户id
            $data['type'] = $request -> type;    //账户类型
            $data['name'] = $request -> name;   //姓名/账户名
            $data['num'] = $request -> num;     //账号
            $data['a_in'] = $request -> a_in;   //开户行/所属机构
            if(DB::table('account') -> where('userid',$userId) -> first()){
                //已经存在  则修改
                DB::table('account') -> where('userid',$userId) -> update($data);
                return ['msg' => 'ok','code' => '0','result' => '成功'];
            }
            if(DB::table('account') -> insert($data)){
                 return ['msg' => 'ok','code' => '0','result' => '成功'];
             }else{
                return ['msg' => 'ok','code' => '2','result' => '失败'];
             }
           
        }
        return ['msg' => 'err','code' => '1','result' => '非法请求'];
    }
    //获取单个兼职的详细工资
    public function getDetailPrice(Request $request)
    {
        $times = time();
        if($times - $request -> times < 10 && $request -> token && $request -> workid){
            //查询到用户的id
            $userId = DB::table('homeuser') -> where('token',$request -> token) -> value('id');
            //通过用户id和工作id查询到查询到用户的签到记录和工资
            $price_detail = DB::table('price_detail')
            -> where('userid',$userId)
            -> where('workid',$request -> workid)
            -> get();
            $work = DB::table('work') -> where('id',$request -> workid) -> first();
            //拿到该管理员下面的所有兼职周期单位
                    $time = DB::table('time') -> get();  //-> where('adminid',$work->adminid) 改了
                    $types = [];//兼职周期
                    foreach($time as $k => $v){
                        $types[$v->id] = $v -> type;
                    }
            if($price_detail){
                $data = [];
                $status = ['未发','已发'];
                foreach($price_detail as $key => $value){
                    $data[$key]['date'] = $value -> date;
                    $data[$key]['price'] = $value -> price;
                    $data[$key]['allprice'] = $value -> allprice;
                    $data[$key]['price_status'] = $status[$value -> price_status];
                    $data[$key]['id'] = $value -> id;
                }
                    //title栏的兼职标题和薪资
                    $works['title'] = $work -> title;
                    $works['price'] = $work -> price;
                    $works['types'] = $types[$work -> days];
              		$works['header'] = $work -> header;
                return ['msg' => 'ok','code' => '0','result' => $data,'work' => $works];
            }
        }else{
            return ['msg' => 'err','code' => '1','result' => '非法请求'];
        }
    }
    //获取每天的兼职详情工资
    public function getDayPrice(Request $request)
    {
        $times = time();
        if($times - $request -> times < 10 && $request -> priceid){
            $dayPrice = DB::table('price_detail') -> where('id',$request -> priceid) -> first();
            return ['msg' => 'ok','code' => '0','result' => $dayPrice];
        }else{
            return ['msg' => 'err','code' => '1','result' => '非法请求'];
        }
    }
}
