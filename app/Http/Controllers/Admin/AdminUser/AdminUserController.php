<?php

namespace App\Http\Controllers\Admin\AdminUser;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Hash;
class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //显示所有管理员
		$adminuser = DB::table('adminuser') -> get();
		$level = DB::table('role') -> get();   //权限
		$num = count($adminuser);
		return view('Admin.AdminUser.list',['adminuser' => $adminuser,'num'=>$num,'level'=>$level]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //加载添加管理员页面
		//遍历所有权限
		$level = DB::table('role') -> get();
		return view('Admin.AdminUser.add',['level'=>$level]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
		echo 'store';
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
		echo 'show';
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //通过id查出需要编辑的数据
		$info = DB::table('adminuser') -> where('id','=',$id) -> first();
		//查出权限信息
		$level = DB::table('role') -> get();
		return view('Admin.AdminUser.edit',['info'=>$info,'level' => $level]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
	//管理员状态更改
	public function sta(Request $request)
	{
		$data = DB::table('adminuser') -> where('id','=',$request -> id) -> value('status');
		if($data == 1){
			DB::table('adminuser')->where('id','=',$request->input('id'))->update(['status'=>0]);
			echo 0;
		}else{
			if(DB::table('adminuser')->where('id','=',$request->input('id'))->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//管理员删除
	public function del(Request $request)
	{
		//直接删除  然后返回状态码
		if(DB::table('adminuser')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//管理员修改
	public function adminuseredit(Request $request)
	{
		//首先判断密码是否被修改
		if($request -> password){
			//判断信息是否完整
			if($request -> phone && $request -> group && $request -> level && $request -> id){
				//密码被修改
				$data['password'] = Hash::make($request -> password);
				$data['phone'] = $request -> phone;
				$data['group'] = $request -> group;
				$data['level'] = $request -> level;
				if(DB::table('adminuser') -> where('id','=',$request -> id) -> update($data)){
					echo 1;
				}else{
					echo 0;
				}
			}else{
				echo 0;
			}
			
		}else{
			//不修改密码
			if($request -> phone && $request -> group && $request -> level &&$request -> id){
				//判断信息是否完整
				$data['phone'] = $request -> phone;
				$data['group'] = $request -> group;
				$data['level'] = $request -> level;
				if(DB::table('adminuser') -> where('id','=',$request -> id) -> update($data)){
					echo 1;
				}else{
					echo 0;
				}
			}else{
				echo 0;
			}
		}
	}
	//用户名有效性验证
	public function adduser(Request $request)
	{
		if($request -> password == $request -> repassword){
			if(DB::table('adminuser') -> where('username','=',$request -> username) -> first()){
				echo 3;
			}else{
				//两次密码正确
				$data['username'] = $request -> username;
				$data['password'] = Hash::make($request -> password);  //hash加密
				$data['phone'] = $request -> phone;
				$data['group'] = $request -> group;
				$data['level'] = $request -> level;
				$data['addtime'] = time();
				if(DB::table('adminuser') -> insert($data)){
					echo 1;
				}else{
					echo 0;
				}
			}
			
		}else{
			echo 2;
			//密码不正确两次
		}
	}
}
