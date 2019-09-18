<?php

namespace App\Http\Controllers\Inter\Logins;

use App\HomeUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use DB;
use Redis;
class LoginsController extends Controller
{

	//发送注册、登录短信接口
	public function getCode(Request $request)
	{
		//		preg_match(/^\d{11}$/,$request -> phone);
		if(strlen($request -> phone) == 11){
		//初始化必填
		//填写在开发者控制台首页上的Account Sid
		$options['accountsid']='9588784a067902d9eb30cf1ec4c96494';
		//填写在开发者控制台首页上的Auth Token
		$options['token']='545b83a19f6b95f37d512375f7c0b27d';

		//初始化 $options必填
		$ucpass = new \Ucpaas($options);


		$appid = "0143ae8f64744746949b26bf1adfea27";	//应用的ID，可在开发者控制台内的短信产品下查看
		$templateid = "371935";    //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
		$param = rand(100000,999999); //多个参数使用英文逗号隔开（如：param=“a,b,c”），如为参数则留空
		$mobile = '18126975907';
		$uid = "";

		//70字内（含70字）计一条，超过70字，按67字/条计费，超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。

		echo $ucpass->SendSms($appid,$templateid,$param,$mobile,$uid);
	}else{
			return json_encode(['msg'=>'err','code'=>'1','result'=>'电话号码格式不正确']);
		}
	}
	//获取用户登录的openid
	public function login(Request $request)
	{
		$res=$request->only(['appid','secret','code']);
        $api='https://api.weixin.qq.com/sns/jscode2session?appid='.$res['appid'].'&secret='.$res['secret'].'&js_code='.$res['code'].'&grant_type=authorization_code';
        $json =file_get_contents($api);
      	$arr = json_decode($json,true);

      	// dump($arr['openid']);exit;
      	
        //实现检查该openid是否已经注册为用户
      	if($id = DB::table('homeuser') -> where('openid','=',$arr['openid']) -> value('id')){
        	//如果进来了证明已经注册了，就把他的token下发到客户端当做用户标识
          	$token = Hash::make($arr['openid']);
            //$a = file_put_contents( $arr['openid'] . '.txt',$arr['session_key']);
          	//更新token
          	if(DB::table('homeuser') -> where('id','=',$id) -> update(['token'=>$token])){
            	return ['msg'=>'ok','code'=>'0','result'=>$token];
            }
        }else{
            //如果没有查询到就把新的openid和token插入导数据库，生成一个新的用户
          	$token = Hash::make($arr['openid']);
            $data['openid'] = $arr['openid'];
          	$data['token'] = $token;
          	$data['addtime'] = time();
      		if(DB::table('homeuser') -> insert($data)){
              //返回token到客户端
            	return ['msg'=>'ok','code'=>'00','result'=>$token];
            }
        }
	}
	//微信用户把头像和昵称写入数据库
	public function wechatlogin(Request $request)
	{
		$time = time();
      if($time - $request -> times < 10 && $request -> header && $request -> token){    //不判断用户名，防止部分用户用户名为空白
         	$data['header'] = $request -> header;
            $data['nicename'] = $request -> nicename;
	            if(DB::table('homeuser') -> where('token','=',$request -> token) -> update($data)){
	            	return ['msg'=>'ok','code'=>'0','result'=>'成功'];
	            }else{
	            	return ['msg'=>'err','code'=>'1','result'=>'失败'];
	            }
        }else{
        	return ['msg'=>'err','code'=>'2','result'=>'非法请求'];
        }
	}
 	//手机号码登录接口
    public function logins(Request $request)
	{
		$phone = $request -> phone;
		//首先判断该手机号在不在已注册用户
		if(DB::table('homeuser') -> where('phone','=',$phone) -> first()){
			//已注册   拿到手机号和验证码直接验证登录
			$list = DB::table('homeuser') -> where('phone','=',$phone) -> first();
			return json_encode($list);
		}else{
			//未注册  首先判断手机号和验证码按是否正确
			//正确 把手机号插入数据库 昵称随机  
			$data['nicename'] = '用户_'.str_random(12);
			$data['phone'] = $phone;
			return json_encode($data,true);
//			DB::table('homeuser') -> insert($data);
		}
	}

	public function getPhone(Request $request)
    {
        $openid = HomeUser::where('token',$request->token) -> value('openid');
        $session_key = file_get_contents($openid . '.txt');
        $appid = 'wxa32f710ad7d22b94';
        $pc = new WxBizDataCrypt($appid,$session_key);
        $errCode = $pc->decryptData($request->encryptedData, $request->iv, $data );
        if ($errCode == 0) {
            return ['msg'=>'ok','code'=>0,'result'=>$data];
        } else {
            return ['msg'=>'ok','code'=>0,'result'=>$errCode];
        }
    }
}
