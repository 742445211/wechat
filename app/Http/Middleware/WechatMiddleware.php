<?php

namespace App\Http\Middleware;

use App\Recruiter;
use Closure;

/**
 * 企业端api中间件
 * Class WechatMiddleware
 * @package App\Http\Middleware
 */
class WechatMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(isset($request->token)){
            $id = Recruiter::where('token',$request->token) -> value('id');//查询当前用户ID
            if(!$id){
                return response()->json(['msg'=>'err','code'=>'51','result'=>'非法请求']);
            }
            $status = Recruiter::where('token',$request->token) -> value('status');//查询当前用户状态
            $mid_params = ['recruit_id'=>$id];
            $request->merge($mid_params);//合并参数

            if($status == 1){
                return $next($request);
            }elseif ($status == 0){
                return response()->json(['msg'=>'err','code'=>'11','result'=>'审核中，请耐心等待']);
            }elseif ($status == 3){
                return response()->json(['msg'=>'err','code'=>'21','result'=>'你已被禁用']);
            }
        }else{
                return response()->json(['msg'=>'err','code'=>'41','result'=>'请先登录']);
        }

    }
}
