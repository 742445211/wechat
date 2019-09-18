<?php


namespace App\Http\Controllers\Recruit\Login;


use App\Http\Controllers\Controller;
use App\Recruiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    /**
     * 企业端用户登陆
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $res=$request->only(['appid','secret','code']);
        $api='https://api.weixin.qq.com/sns/jscode2session?appid='.$res['appid'].'&secret='.$res['secret'].'&js_code='.$res['code'].'&grant_type=authorization_code';
        $json =file_get_contents($api);
        $arr = json_decode($json,true);
        // dump($arr['openid']);exit;

        $id = Recruiter::where('openid',$arr['openid']) -> value('id');
        //实现检查该openid是否已经注册为用户
        if($id){
            //如果进来了证明已经注册了，就把他的token下发到客户端当做用户标识
            $token = Hash::make($arr['openid']);
            //更新token
            if(Recruiter::where('id',$id) -> update(['token'=>$token])){
                return ['msg'=>'ok','code'=>'0','result'=>$token];
            }
        }else{
            //如果没有查询到就把新的openid和token插入导数据库，生成一个新的用户
            $token = Hash::make($arr['openid']);
            $data['openid'] = $arr['openid'];
            $data['token'] = $token;
            $data['created_at'] = time();
            if(Recruiter::create($data)){
                //返回token到客户端
                return ['msg'=>'ok','code'=>'00','result'=>$token];
            }
        }
    }

    /**
     * 把用户信息写入数据库
     * @param Request $request
     * @return array
     */
    public function wechatlogin(Request $request)
    {
        $time = time();
        if($time - $request -> times < 10 && $request -> header && $request -> token){    //不判断用户名，防止部分用户用户名为空白
            $data['header']   = $request -> header;
            $data['nicename'] = $request -> nicename;
            if(Recruiter::where('token',$request -> token) -> update($data)){
                return ['msg'=>'ok', 'code'=>'0','result'=>'成功'];
            }else{
                return ['msg'=>'err','code'=>'1','result'=>'失败'];
            }
        }else{
            return ['msg'=>'err','code'=>'2','result'=>'非法请求'];
        }
    }

}