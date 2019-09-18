<?php

namespace App\Http\Controllers\Admin\Za;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class ZaController extends Controller
{
    //结算周期管理
	public function times()
	{
		//遍历所有的薪资时间结算表
		$times = DB::table('time') -> where('adminid','=',session('userid')) ->get();
		$num = count($times);
		return view('Admin.Za.times',['times'=>$times,'num'=>$num]);
	}
	//结算周期更改
	public function edit(Request $request)
	{
		//将更改写入数据库
		if(DB::table('time') -> where('id','=',$request -> id) -> update(['type'=>$request->v])){
			echo 1;
		}else{
			echo 0;
		}
	}
	//更改结算周期状态
	public function status(Request $request)
	{
		//查询现在的用户账号状态
		$data = DB::table('time')->where('id','=',$request->id)->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('time')->where('id','=',$request->id)->update(['status'=>0])){
				echo 0;
			}
		}else{
			if(DB::table('time')->where('id','=',$request->id)->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//删除结算周期标签
	public function delete(Request $request)
	{
		//直接删除  然后返回状态码
		if(DB::table('time')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//添加结算周期
	public function create()
	{
		//加载模板
		return view('Admin.Za.timesadd');
	}
	//处理结算周期
	public function docreate(Request $request)
	{
		$data['type'] = $request -> val;
		$data['adminid'] = session('userid');
		if(DB::table('time') -> insert($data)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	
	//热门标签管理
	public function hots()
	{
		$hots = DB::table('hots') -> where('adminid','=',session('userid')) ->get();
		$num = count($hots);
		return view('Admin.Za.hots',['hots'=>$hots,'num'=>$num]);
	}
		//热门标签更改
	public function hotsedit(Request $request)
	{
		//将更改写入数据库
		if(DB::table('hots') -> where('id','=',$request -> id) -> update(['type'=>$request->v])){
			echo 1;
		}else{
			echo 0;
		}
	}
	//更改热门标签状态
	public function hotsstatus(Request $request)
	{
		//查询现在的标签状态
		$data = DB::table('hots')->where('id','=',$request->id)->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('hots')->where('id','=',$request->id)->update(['status'=>0])){
				echo 0;
			}
		}else{
			if(DB::table('hots')->where('id','=',$request->id)->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//删除热门管理标签
	public function hotsdelete(Request $request)
	{
		//直接删除  然后返回状态码
		if(DB::table('hots')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//添加热门标签
	public function hotscreate()
	{
		//加载模板
		return view('Admin.Za.hotsadd');
	}
	//处理热门标签
	public function dohotscreate(Request $request)
	{
		$data['type'] = $request -> val;
		$data['adminid'] = session('userid');
		if(DB::table('hots') -> insert($data)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
	
	
	//兼职类型管理
	public function types()
	{
		$types = DB::table('types') -> where('adminid','=',session('userid')) ->get();
		$num = count($types);
		return view('Admin.Za.types',['types'=>$types,'num'=>$num]);
	}
		//兼职类型更改
	public function typesedit(Request $request)
	{
		//将更改写入数据库
		if(DB::table('types') -> where('id','=',$request -> id) -> update(['type'=>$request->v])){
			echo 1;
		}else{
			echo 0;
		}
	}
		//更改兼职类型状态
	public function typesstatus(Request $request)
	{
		//查询现在的标签状态
		$data = DB::table('types')->where('id','=',$request->id)->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('types')->where('id','=',$request->id)->update(['status'=>0])){
				echo 0;
			}
		}else{
			if(DB::table('types')->where('id','=',$request->id)->update(['status'=>1])){
				echo 1;
			}
		}
	}
		//删除兼职类型标签
	public function typesdelete(Request $request)
	{
		//直接删除  然后返回状态码
		if(DB::table('types')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
		//添加兼职类型
	public function typescreate()
	{
		//加载模板
		return view('Admin.Za.typesadd');
	}
		//处理兼职类型添加
	public function dotypescreate(Request $request)
	{
		$data['type'] = $request -> val;
		$data['adminid'] = session('userid');
		if(DB::table('types') -> insert($data)){
			echo 1;
		}else{
			echo 0;
		}
	}
	
}
