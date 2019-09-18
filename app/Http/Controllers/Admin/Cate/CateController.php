<?php

namespace App\Http\Controllers\Admin\Cate;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class CateController extends Controller
{
    //便利分类数据，加载分类模板
	public function index()
	{
		//便利所有分类，加载模板
		$cate = DB::table('cate') -> where('adminid','=',session('userid')) -> get();
		$num = count($cate);
		return view('Admin.Cate.cate',['cate'=>$cate,'num'=>$num]);
	}
	//删除分类标签
	public function del(Request $request)
	{
		//直接删除  然后返回状态码
		if(DB::table('cate')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//分类标签状态更改
	public function sta(Request $request)
	{
		//查询现在的用户账号状态
		$data = DB::table('cate')->where('id','=',$request->input('id'))->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('cate')->where('id','=',$request->input('id'))->update(['status'=>0])){
				echo 0;
			}
		}else{
			if(DB::table('cate')->where('id','=',$request->input('id'))->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//分类标签编辑
	public function edit(Request $request)
	{
		//将更改写入数据库
		if(DB::table('cate') -> where('id','=',$request -> id) -> update(['cates'=>$request->v])){
			echo 1;
		}else{
			echo 0;
		}
		
	}
	//分类标签添加
	public function add()
	{
		//加载添加模板
		return view('Admin.Cate.add');
	}
	//处理添加分类标签
	public function update(Request $request)
	{
		$data['cates'] = $request -> val;
		$data['adminid'] = session('userid');
		if(DB::table('cate') -> insert($data)){
			echo 1;
		}else{
			echo 0;
		}
	}
	/*
	
	分类详细信息
	
	*/
	
	public function cate()
	{
		//获取所有分类的id 和分类名称
		$cate = DB::table('cate') -> where('adminid','=',session('userid')) -> get();
		//通过cateid查询所有下面的工作详情
		$num = [];
			foreach($cate as $key => $value){
				$cateid = $value->id;  //fen类id
				//每次拿到id到兼职表查询有多少该分累下面的兼职
				$num[$key] = DB::table('work') -> where('cate','=',$cateid) -> count();
			}
		$nums = count($cate);
		return view('Admin.Cate.cates',['cate'=>$cate,'nums'=>$num,'num'=>$nums]);
	}
}
