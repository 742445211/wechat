<?php

namespace App\Http\Controllers\Admin\Tousu;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class TousuController extends Controller
{
	//加载投诉列表
    public function index()
	{
		//加载投诉列表   按照时间倒序排序
		$list = DB::table('tousu') ->where('adminid','=',session('userid')) -> orderBy('sendtime','desc') -> get();
		$num = count($list);
		return view('Admin.Tousu.tousu',['list'=>$list,'num'=>$num]);
	}
	//修改投诉内容已读   意见反馈同
	public function tousuok(Request $request)
	{
		if($request -> yj == 'yj'){
			DB::table('yijian')->where('id','=',$request->input('id'))->update(['status'=>1]);
			echo 1;
		}else{
			if(DB::table('tousu')->where('id','=',$request->input('id'))->update(['status'=>1])){
				echo 1;
			}else{
				echo 0;
			}
		}
			
	}
	//意见反馈 加载模板
	public function yijian()
	{
		//遍历所有意见   where('adminid','=',session('userid')) -> 
		$list = DB::table('yijian') -> orderBy('sendtime','desc') -> get();
		$num = count($list);
		return view('Admin.Tousu.yijian',['list'=>$list,'num'=>$num]);
	}
	//投诉和意见删除
	public function del(Request $request)
	{
		if($request -> yj == 'yj'){
			//说明删除的是意见反馈
			if(DB::table('yijian') -> where('id','=',$request -> id) -> delete()){
				echo 1;
			}else{
				echo 0;
			}
		}else{
			//删除的是投诉建议
			if(DB::table('tousu') -> where('id','=',$request -> id) -> delete()){
				echo 1;
			}else{
				echo 0;
			}
		}
	}
}
