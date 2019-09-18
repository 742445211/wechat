<?php

namespace App\Http\Controllers\Admin\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //加载所有角色列表
		$role = DB::table('role') -> get();
		$num = count($role);
		return view('Admin.Role.role',['role'=>$role,'num'=>$num]);
    }
	//角色状态更改
	public function sta(Request $request)
	{
		//查询现在的状态
		$data = DB::table('role')->where('id','=',$request->input('id'))->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('role')->where('id','=',$request->input('id'))->update(['status'=>0])){
				echo 0;
			}
		}else{
			if(DB::table('role')->where('id','=',$request->input('id'))->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//角色删除
	public function del(Request $request)
	{
		//查询当前权限是否有人使用
		if(DB::table('adminuser') -> where('level','=',$request -> id) -> first()){
			echo 2;
		}else{
			//直接删除  然后返回状态码
			if(DB::table('role')->where('id','=',$request -> id)->delete()){
				echo 1;
			}else{
				echo 0;
			}
		}
		
	}
	//角色表编辑
	public function roleedit(Request $request)
	{
		//将更改写入数据库
		if(DB::table('role') -> where('id','=',$request -> id) -> update(['level'=>$request->v])){
			echo 1;
		}else{
			echo 0;
		}
		
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
		//遍历出所有的node节点
		$node = DB::table('node') -> get();
        return view('Admin.Role.roleadd',['node'=>$node]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //首先把角色名字写入数据库
		if($level = $request -> level){
			//查询是否重名
			if(DB::table('role') -> where('level','=',$level) -> first()){
				echo '角色名重复';
			}else{
				$data['level'] = $request -> level;
				$id = DB::table('role') -> insertGetId($data);
				//获取新增权限
				for($i = 0; $i < count($request -> node); $i++){
					//循环写入   获取新增id数量
					$arr[] = DB::table('user_node') -> insertGetId(['rid'=>$id,'nid'=>$request -> node[$i]]);
				}
					if(count($arr) == count($request -> node)){
							echo '新增成功!';
						}else{
							echo 'gg';
					}
			}
		}
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
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	
	//分配权限
    public function edit($id)
    {
        
		//加载该角色信息   需要和角色节点表关联查询当前角色所拥有的权限，然后再选中
		$info = DB::table('user_node') -> where('rid','=',$id)-> get(); 
		$arr = [];
			foreach($info as $key => $value){
					$arr[]=$value->nid;
				}	
		$node = DB::table('node') -> get();
		//加载权限分配页面
		return view('Admin.Role.rolefp',['user_node'=>$arr,'node'=>$node,'id'=>$id]);
		
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
		//然后进行重新写入权限    遍历权限
		if($request -> node != ''){
		//首先删除该管理员的所有权限
		DB::table('user_node') -> where('rid','=',$id) -> delete();
			//循环
			for($i = 0;$i<count($request -> node);$i++){
				$data = $request -> node[$i];
				DB::table('user_node') -> insert(['rid'=>$id,'nid'=>$data]);
			}
			echo '修改成功!';
		}else{
			//删除该管理员的所有权限
			if(DB::table('user_node') -> where('rid','=',$id) -> delete()){
				echo '修改成功!';
			}
		}
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
}
