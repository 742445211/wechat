<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Redis;
use Closure;

class LoginMiddleware
{
    public function handle($request, Closure $next)
    {
		if($request->session()->has('username')){
			//判断如果有为username的session就继续下一个操作
			//判断用户的权限
			//获取访问的控制器和方法
           $action=$request->route()->getActionMethod();
           // echo $action;
           $actions=explode('\\', \Route::current()->getActionName());
           //或$actions=explode('\\', \Route::currentRouteAction());
           $modelName=$actions[count($actions)-2]=='Controllers'?null:$actions[count($actions)-2];
           $func=explode('@', $actions[count($actions)-1]);
           //获取的控制器名字
           $controller=$func[0];
           $actionName=$func[1];
			//获取当前登录用户的权限列表
			$nodelist = session('nodelist');
			//进行对比
			if(empty($nodelist[$controller]) || !in_array($action,$nodelist[$controller])){
				return redirect("/admin")->with('error','抱歉,你的权限不足,请联系超级管理员');
			}
			return $next($request);
		}else{
			//如果么有session就跳转回到登录界面
			return redirect('/logins');
		}
		
    }
}
