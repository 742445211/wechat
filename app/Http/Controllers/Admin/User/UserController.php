<?php

namespace App\Http\Controllers\Admin\User;

use App\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class UserController extends Controller
{
    public function index(Request $request)
    {  
		$level = DB::table('adminuser') -> where('id','=',session('userid')) -> value('level');  //为1是超管
    	if($level == '1'){   //超级管理员
    		$handle = DB::table('homeuser');

			$endtime = $request -> endtime;   //结束时间
			$starttime = $request -> starttime;   // 开始时间
			$keyword = $request -> keyword;    // 关键词

			if($starttime){   // 开始时间
				$starttime=strtotime($starttime); //获取日期转换后的时间戳
			}
			if($endtime){   //结束时间
				$endtime=strtotime($endtime); //获取日期转换后的时间戳
			}

			$keyword && $handle -> where('username','like','%'.$keyword.'%');
			$starttime && $handle -> where('addtime','>',$starttime);
			$endtime && $handle -> where('addtime','<',$endtime);
			// 获取数据
			$list = $handle -> orderBy('addtime','desc') -> paginate(8);
	        //遍历所有兼职
			$num = DB::table('homeuser') -> count();
			return view('Admin.HomeUser.user',['list'=>$list,'num'=>$num,'endtime'=>$request -> endtime,'keyword'=>$request -> keyword,'request' => $request -> all()]);
    	}else{
    		return back();
    	}
    }
	//更改用户账号状态
	public function status(Request $request)
	{
		//查询现在的用户账号状态
		$data = DB::table('homeuser')->where('id','=',$request->input('id'))->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('homeuser')->where('id','=',$request->input('id'))->update(['status'=>0])){
				echo 0;
			}
		}else{
			if(DB::table('homeuser')->where('id','=',$request->input('id'))->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//删除用户
	public function del(Request $request)
	{
		//直接删除状态改为3  然后返回状态码
		if(DB::table('homeuser')->where('id','=',$request -> id)->update(['status'=>2])){
			echo 1;
		}else{
			echo 0;
		}
	}
	//查看删除的用户
	public function deluser()
	{
		$del = DB::table('homeuser') -> where('status','=',2) -> get();
		$num = count($del);
		return view('Admin.HomeUser.del',['del'=>$del,'num'=>$num]);
	}
	//撤回删除的用户
	public function userdelback(Request $request)
	{
		//状态改为1 撤回删除
		if(DB::table('homeuser')->where('id','=',$request -> id)->update(['status'=>1])){
			echo 1;
		}else{
			echo 0;
		}
	}
	//彻底删除
	public function userdeldel(Request $request)
	{
		//彻底删除
		if(DB::table('homeuser')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//查看用户详细信息
	public function usershow($id,$exus)
	{
		if($exus == 'cplt'){   //exus为cplt代表是报名列表里面的请求
			//接受到的id为cplt表的id   要通过cplt表的id查询到userid
			$userId = DB::table('cplt') -> where('id','=',$id) -> value('userid');
			//查询用户相关信息
			$data = DB::table('homeuser') -> where('id','=',$userId) -> first();
			$account = DB::table('account') -> where('userid',$userId) -> first();
			if($account){
				$type = DB::table('moneytype') -> where('id',$account -> type) -> value('type');
			}else{
				$account = '';
				$type = '';
			}
			
		}elseif($exus == 'price'){   //工资信息里面请求的    正常的用户id请求的
			$data = DB::table('homeuser') -> where('id','=',$id) -> first();
			$account = DB::table('account') -> where('userid',$id) -> first();
			if($account){
				$type = DB::table('moneytype') -> where('id',$account -> type) -> value('type');
			}else{
				$account = '';
				$type = '';
			}
			
		}else{  //否则为用户列表的查询
			//查询用户相关信息
			$data = DB::table('homeuser') -> where('id','=',$id) -> first();
			$account = DB::table('account') -> where('userid',$id) -> first();
			if($account){
				$type = DB::table('moneytype') -> where('id',$account -> type) -> value('type');
			}else{
				$account = '';
				$type = '';
			}
		}
		//处理性别
		$sex = ['0'=>'女','1'=>'男','2'=>'保密'];
			return view('Admin.HomeUser.show',['data'=>$data,'sex'=>$sex,'account'=>$account,'type'=>$type]);
	}


	//群发短信页面
	public function sendsms(Request $request)
	{
		//加载所有用户
		$user = DB::table('homeuser') -> get();
		$num = count($user);
		return view('Admin.HomeUser.sms',['user'=>$user,'num'=>$num]);
		
	}
	public function smsss(Request $request)
	{

	}

	//轮播图列表
	public function lunbo(Request $request)
	{
		//查询轮播图
		$lunbo = DB::table('lunbo') -> where('status','1') -> orwhere('status','0') -> get();
		$num = count($lunbo);
		return view('Admin.Lunbo.lunbo',['lunbo' => $lunbo,'num' => $num]);
	}
	//添加轮播图页面
	public function addlunbo(Request $request)
	{
		return view('Admin.Lunbo.addlunbo');
	}
	//轮播图添加到系统目录
	public function getimgs(Request $request)
	{
		$info = $request -> all();
		$data['level'] = 0;
		$data['addtime'] = time();  //判断最后一次添加的那条图片
		$data['adminid'] = session('userid');  //用于区别最后一位上传的管理员是谁  防止多个管理员上传
		$data['status'] = 3;   //状态  为3是没有确认提交的状态  前台确认后把状态改为1
		$file = $request -> file('file');
		$entension = $file -> getClientOriginalExtension(); //上传文件的后缀.
		$newName = str_random('19');
		$path = $newName.'.'.$entension;
		$info = $file -> move(base_path('public') . '/upload_xst/',$path);
		$data['url'] = 'https://www.xiaoshetong.cn/upload_xst/'.$path;   //路径
		DB::table('lunbo') -> insert($data);
	}
	//保存最终提交
	public function upimgres(Request $request)
	{
		if($request -> status){
			//点击的取消按钮
			$adminid = $request -> adminid;
			//按照时间查询数据库最后一条提交的数据和adminid
			$res = DB::table('lunbo') -> where('adminid',$adminid) -> orderBy('addtime','desc') -> first();
			DB::table('lunbo') -> where('id',$res -> id) -> delete();
			return 'close';
		}else{
			//确认保存按钮
			$adminid = $request -> adminid;
			$level = $request -> level;
			//按照时间查询数据库最后一条提交的数据和adminid
			$res = DB::table('lunbo') -> where('adminid',$adminid) -> orderBy('addtime','desc') -> first();
			$data['level'] =  $level;
			$data['status'] = 1;  //启用
			if(DB::table('lunbo') -> where('id',$res -> id) -> update($data)){
				echo 1;  //ok
			}else{
				DB::table('lunbo') -> where('id',$res -> id) -> delete();
				echo 2;  //err
			}
		}
	}
	//轮播图状态的修改
	public function setstatus(Request $request)
	{
		if($request -> id){
			$status = DB::table('lunbo') -> where('id',$request -> id) -> value('status');
			if($status == '1'){
				DB::table('lunbo') -> where('id',$request -> id) -> update(['status' => '0']);
				return '0';    //关闭
			}else{
				DB::table('lunbo') -> where('id',$request -> id) -> update(['status' => '1']);
				return '1';  //启用
			}
		}
	}
	//delajax删除轮播图
	public function delajax(Request $request)
	{
		if(DB::table('lunbo') -> where('id',$request -> id) -> delete()){
			return 'del';
		}
	}
	//关于信息展示
	public function getinfo()
	{
		$info = DB::table('info') -> get();
		$num = count($info);
		return view('Admin.Lunbo.info',['info'=>$info,'num' => $num]);
	}
	//关于信息添加页面
	public function addinfo()
	{
		return view('Admin.Lunbo.addinfo');
	}
	//保存添加
	public function addok(Request $request)
	{
		if($request -> content){
			$data['addtime'] = time();
			$data['adminid'] = session('userid');
			$data['status'] = 1;
			$data['content'] = $request -> content;
			if(DB::table('info') -> insert($data)){
				return '1';  //ok
			}else{
				return '2'; //err
			}
		}else{
			return '0';//没有内容
		}
	}
	//关于信息修改状态
	public function infostatus(Request $request)
	{
		if($request -> id){
			$status = DB::table('info') -> where('id',$request -> id) -> value('status');
			if($status == '1'){
				DB::table('info') -> where('id',$request -> id) -> update(['status' => '0']);
				return '0';    //关闭
			}else{
				DB::table('info') -> where('id',$request -> id) -> update(['status' => '1']);
				return '1';  //启用
			}
		}
	}
	//删除关于信息条目
	public function delinfo(Request $request)
	{
		if(DB::table('info') -> where('id',$request -> id) -> delete()){
			return 'del';
		}
	}
   //头像列表
  	public function header()
    {
      $header = DB::table('header') -> where('status','1') -> get();
      $num = count($header);
    	return view('Admin.Lunbo.header',['list' => $header,'num' => $num]);
    }
  	//添加头像
  	public function addheader()
    {
    	return view('Admin.Lunbo.addheader');
    }
  	//头像添加到系统目录
	public function upheader(Request $request)
	{
		$data['addtime'] = time();  //判断最后一次添加的那条图片
		$data['status'] = 1;   //状态
		$file = $request -> file('file');
		$entension = $file -> getClientOriginalExtension(); //上传文件的后缀.
		$newName = str_random('19');
		$path = $newName.'.'.$entension;
		$info = $file -> move('upload_header/',$path);
		$data['url'] = 'https://www.xiaoshetong.cn/upload_header/'.$path;   //路径
		DB::table('header') -> insert($data);
	}

    /**
     * 新增信息
     * @param Request $request
     * @return array
     */
	public function addWorkImg(Request $request)
    {
        $data['name']    = $request->input('name');//类型名
        $data['intro']   = $request->input('intro');//类型介绍
        $data['imgpath'] = $request->input('img');//图片路径
        $data['pid']     = $request->pid;
        $data['level']   = $request->level;
        $data['status']  = 1;
        if($request->type == 'insert'){
            $res = Position::create($data);
            if($res){
                return ['code'=>0];
            }
        }else{
            $res = Position::where('id',$request->id) -> update($data);
            if($res){
                return ['code'=>1];
            }
        }
    }

    /**
     * 上传修改类型图
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getWorkImg(Request $request)
    {
        if($request->pid == 0){
            $level = 0;
            $post = [];
        }else{
            $a     = Position::where('id',$request->pid) -> first();
            $level = $a->level;
            $pid   = $a->pid;
            $post  = Position::where('pid',$pid) -> get();
        }

        if(isset($request->id)){
            $data = Position::find($request->id);
            return view('Admin.Workimg.workimg',['type'=>'update','id'=>$request->id,'has'=>true,'data'=>$data,'post'=>$post,'level'=>$level,'pid'=>$request->pid]);
        }else{
            return view('Admin.Workimg.workimg',['has'=>false,'post'=>$post,'level'=>$level,'pid'=>$request->pid]);
        }
    }

    /**
     * 查看类型图
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showWorkImg(Request $request)
    {
        $status = isset($request->status) ? $request->status : 1;
        $pid = isset($request->pid) ? $request->pid : 0;
        if($pid){
            $name = Position::where('id',$pid) -> value('name');
        }else{
            $name = '';
        }
        $data = Position::where('status',$status) -> where('pid',$pid) -> get();
        $num = count($data);

        return view('Admin.Workimg.showworkimg',['data'=>$data,'num' => $num,'status' => $status,'pid'=>$pid,'name'=>$name]);
    }

    //添加推荐
    public function recommend(Request $request){
        $status = $request -> status;
        $id = $request -> id;
        $res = Position::where('id',$id) -> update(['is_rec'=>$status]);
        if($res){
            return ['data'=>1];
        }
    }

    /**
     * 上传图片
     * @param Request $request
     * @return array
     */
    public function uploadImg(Request $request)
    {
        $file = $request -> file('file');
        $entension = $file -> getClientOriginalExtension(); //上传文件的后缀.
        $newName = str_random('19');
        $path = $newName.'.'.$entension;
        $info = $file -> move(base_path('public') . '/upload_cate/',$path);
        $img = 'https://www.xiaoshetong.cn/upload_cate/'.$path;   //路径
        return ['msg'=>'ok','code'=>0,'img'=>$img,'token'=>csrf_token()];
    }

    /**
     * 修改图片状态
     * @param Request $request
     * @return int|string
     */
    public function changImg(Request $request)
    {
        if($request->type == 'delete'){
            $del = Position::where('id',$request->id) -> delete();
            if($del)return 'del';
        }elseif ($request->type == 'update'){
            $update = Position::where('id',$request->id) -> update(['status' => $request->status]);
            if($update)return 1;
        }
    }


}
