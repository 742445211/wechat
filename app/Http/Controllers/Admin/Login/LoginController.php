<?php
namespace App\Http\Controllers\Admin\Login;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    //加载登录页面
    public function index()
    {
    	return view('Admin.Login.login');
    }
	//处理登录
	public function dologin(Request $request)
	{
		$username = $request -> username;
		$password = $request -> password;
		//判断账号密码是否为空
		if($username && $password){
			//验证账号是否存在与数据库
			if($passwd = DB::table('adminuser') -> where('username','=',$username) -> value('password')){
				//如果存在继续验证密码
//				dd(Hash::make($password));
				if(Hash::check($password,$passwd)){
					//验证管理员的账号状态是否为禁用
					if(DB::table('adminuser') -> where('username','=',$username) -> value('status') == '0'){
						return redirect('/logins') -> with('err4','账号已被禁用，请联系超级管理员!');
					}
					//成功后直接跳转到后台首页
					//用户信息加入session顺便加入该管理员的id到sessiom
					$id = DB::table('adminuser') -> where('username','=',$username) -> value('id');
					session(['username'=>$username,'userid'=>$id]);
					
					//获取当前用户的权限信息
					$list = DB::table('adminuser') -> join('user_node','adminuser.level','=','user_node.rid') -> join('node','user_node.nid','=','node.id') -> where('adminuser.id','=',$id) -> get();
					//权限初始化 可以让所有管理员具有访问后台首页的权限
					$nodelist['IndexController'][]='index';
					//遍历  把list 权限列表写入到$nodelist
				foreach($list as $v){
					//控制器
					$nodelist[$v->kname][]=$v->fname;
					//如果权限列表里面有create方法  就把store方法加上
					if($v->fname == "create"){
						$nodelist[$v->kname][]='store';
					}
					//如果权限列表有edit方法  就添加update方法
					if($v->fname == 'edit'){
						$nodelist[$v->kname][]='update';
					}
					//用户相关控制器
					if($v -> fname == 'index'){   //查看用户
						$nodelist[$v->kname][]='deluser'; //查看删除的用户
						$nodelist[$v->kname][]='usershow';//查看用户详情
					}
					if($v -> fname == 'del'){  //删除用户
						$nodelist[$v->kname][]='userdelback';  //撤回删除
						$nodelist[$v->kname][]='userdeldel';  //彻底删除
					}
					//支付类型控制器
					if($v -> fname == 'update'){   //修改支付类型
						$nodelist[$v->kname][]='moneystatus';  //状态修改
					}
					if($v -> fname == 'typeadd'){   //添加
						$nodelist[$v->kname][]='dotypeadd';  //处理添加
					}
					//兼职
					if($v -> fname == 'create'){   //添加兼职
						$nodelist[$v->kname][]='workadd';  //处理兼职添加
						$nodelist[$v->kname][]='getadd';  //获取坐标信息
						$nodelist[$v->kname][]='getmap';  //获取坐标信息
                        $nodelist[$v->kname][]='jobImage';
                        $nodelist[$v->kname][]='delImg';
                        $nodelist[$v->kname][]='addJobImg';
                        $nodelist[$v->kname][]='select';
					}
					if($v -> fname == 'index'){   //查看
						$nodelist[$v->kname][]='show';  //查看兼职详情
					}
					//报名
					if($v -> fname == 'index'){   //查看
						$nodelist[$v->kname][]='cpltok';  //查看报名通过
						$nodelist[$v->kname][]='cpltno';  //查看报名拒绝
						$nodelist[$v->kname][]='cpltready';  //待处理
						$nodelist[$v->kname][]='cplts';  //查看报名人员详情
					}
					if($v -> fname == 'cpltstatus'){   //状态
						$nodelist[$v->kname][]='cpltms';  //面试状态
					}
					//标签
					if($v -> fname == 'times'){   //查看标签
						$nodelist[$v->kname][]='hots';  //热门
						$nodelist[$v->kname][]='types';  //类型
					}
					if($v -> fname == 'edit'){   //编辑标签
						$nodelist[$v->kname][]='hotsedit';  //热门
						$nodelist[$v->kname][]='typesedit';  //类型
						$nodelist[$v->kname][]='status';  //状态
						$nodelist[$v->kname][]='hotsstatus';  //类型
						$nodelist[$v->kname][]='typesstatus';  //类型
					}
					if($v -> fname == 'delete'){   //删除
						$nodelist[$v->kname][]='hotsdelete';  //热门
						$nodelist[$v->kname][]='typesdelete';  //类型
					}
					if($v -> fname == 'create'){   //添加
						$nodelist[$v->kname][]='docreate';  //处理添加
						$nodelist[$v->kname][]='hotscreate';  //热门添加
						$nodelist[$v->kname][]='dohotscreate';  //处理热门添加
						$nodelist[$v->kname][]='typescreate';  //类型添加
						$nodelist[$v->kname][]='dotypescreate';  //处理类型添加
					}
					//兼职分类
					if($v -> fname == 'index'){   //查看
						$nodelist[$v->kname][]='cate';  //查看分类详情
					}
					if($v -> fname == 'edit'){   //编辑
						$nodelist[$v->kname][]='sta';  //分类状态更改
					}
					if($v -> fname == 'add'){   //添加
						$nodelist[$v->kname][]='update';  //处理分类添加
					}
					//投诉、意见
					if($v -> fname == 'index'){   //查看投诉
						$nodelist[$v->kname][]='yijian';  //查看意见
						$nodelist[$v->kname][]='tousuok';  //已查看
					}
					//管理员
					if($v -> fname == 'create'){   //添加
						$nodelist[$v->kname][]='adduser';  //处理管理员添加
					}
					if($v -> fname == 'edit'){   //编辑
						$nodelist[$v->kname][]='adminuseredit';  //处理管理员编辑
						$nodelist[$v->kname][]='sta';  //管理员状态更改
					}
					//角色
					if($v -> fname == 'roleedit'){   //编辑
						$nodelist[$v->kname][]='sta';  //状态
					}
					if($v -> fname == 'create'){   //添加角色
						$nodelist[$v->kname][]='store';  //处理角色添加
					}
					if($v -> fname == 'edit'){   //分配权限
						$nodelist[$v->kname][]='update';  //处理分配权限
					}
					//节点
					if($v -> fname == 'create'){   //添加节点
						$nodelist[$v->kname][]='store';  //处理添加节点
					}
					//群发短信
					if($v -> fname == 'sendsms'){   //群发短信选择用户界面
						$nodelist[$v->kname][]='smsss';  //用户搜索
					}
					//修改兼职
					if($v -> fname == 'workedit'){
						$nodelist[$v->kname][]='workup';  //处理修改
						$nodelist[$v->kname][]='editmap';  //编辑时的地图
                        $nodelist[$v->kname][]='addRecImg';  //添加取消推荐
                        $nodelist[$v->kname][]='showRecImg';  //展示图片
					}
					//工作群
					if($v -> fname == 'index'){     //群聊列表
						$nodelist[$v->kname][]='bindId';  //绑定管理员id和群id
						$nodelist[$v->kname][]='workdetail';  //进入群聊
						$nodelist[$v->kname][]='godetail';  //进入群聊界面
						$nodelist[$v->kname][]='delwork';  //解散群
                        $nodelist[$v->kname][]='isSign';  //开启关闭签到签退
					}
					//工资管理
					if($v -> fname == 'index'){   //工资列表页
						$nodelist[$v->kname][]='workPrice';  //单个兼职详细工资
						$nodelist[$v->kname][]='putPrice';  //单个用户ajax发工资
						$nodelist[$v->kname][]='userPrice';  //单个用户的所有签到信息
						$nodelist[$v->kname][]='dayPrice';  //单个用户每天的薪资发放
					}
					//轮播图
					if($v -> fname == 'lunbo'){   
						$nodelist[$v->kname][]='addlunbo';  //添加轮播图
						$nodelist[$v->kname][]='getimgs';  //添加轮播图
						$nodelist[$v->kname][]='upimgres';  //添加轮播图
						$nodelist[$v->kname][]='setstatus';  //轮播图状态修改
						$nodelist[$v->kname][]='delajax';  //ajax删除轮播
                        $nodelist[$v->kname][]='addWorkImg';
                        $nodelist[$v->kname][]='getWorkImg';
                        $nodelist[$v->kname][]='showWorkImg';
                        $nodelist[$v->kname][]='uploadImg';
                        $nodelist[$v->kname][]='changImg';
                        $nodelist[$v->kname][]='recommend';
					}
					//关于页面
					if($v -> fname == 'getinfo'){   
						$nodelist[$v->kname][]='addinfo';  //添加关于
						$nodelist[$v->kname][]='infostatus';  //关于页面状态
						$nodelist[$v->kname][]='delinfo';  //删除关于
						$nodelist[$v->kname][]='addok';  //处理添加
					}
                    //前台兼职头像
                      if($v -> fname == 'header'){   
                          $nodelist[$v->kname][]='addheader';  //添加头像页面
                          $nodelist[$v->kname][]='upheader';  //处理头像添加
                      }
					//群公告
					if($v -> fname == 'gg'){   
						$nodelist[$v->kname][]='send_sms';  //发送短信
						$nodelist[$v->kname][]='addgg';  //添加群公告
						$nodelist[$v->kname][]='doaddgg';  //处理添加群公告
						$nodelist[$v->kname][]='editgg';  //ajax修改群公告
					}
					//导出Excel表
                    if($v -> fname == 'export'){
                        $nodelist[$v->kname][]='signExport';  //导出签到表
                    }
                    //审核企业用户
                    if($v -> fname == 'authentication'){
                        $nodelist[$v->kname][]='authentication';//查看用户状态
                        $nodelist[$v->kname][]='show';//查看用户详情
                        $nodelist[$v->kname][]='index';//查询用户
                        $nodelist[$v->kname][]='status';//审核
                        $nodelist[$v->kname][]='no';//未过审
                    }
				}
                    //dd($nodelist);exit;
				//把初始化的权限信息放在session里面
				session(['nodelist'=>$nodelist]);
					//登录次数和上次登录时间和ip
					//先查询，后设置
					//登录次数加一
					DB::table('adminuser') -> where('id','=',$id) -> increment('loginnum');
					$info = DB::table('adminuser') -> where('id','=',$id) -> first();
					session(['loginnum'=>$info->loginnum,'logintime'=>$info -> prevlogintime,'loginip'=>$info -> prevloginip]);
					//设置管理员的本次登录ip
					$data['prevloginip'] = LoginController::getIp();
					//设置管理员的本次登录时间
					$data['prevlogintime'] = time();
					DB::table('adminuser') -> where('id','=',$id) -> update($data);  //设置本次登录的信息
					return redirect('/admin') -> with('success','登录成功!');
				}else{
					//失败后提示密码错误
					return redirect('/logins') -> with('err3','账号或密码错误!') -> withInput();
				}
			}else{
				//不存在直接返回
				return redirect('logins') -> with('err','用户不存在!') -> withInput();
			}
		}else{
			return redirect('/logins') -> with('err2','账号或密码不能为空!');
		}
	}

    //获取用户IP地址
    public function getIp()
    {

        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        //var_dump($cip);
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);

        return $cip;
    }
	//退出登录
	public function outlogin(Request $request)
	{
		//退出登录后清除session信息
		$request->session()->forget('username');
		$request->session()->forget('userid');
		//清除权限信息
		$request -> session()->pull('nodelist');
		return redirect('/logins');
	}
}
