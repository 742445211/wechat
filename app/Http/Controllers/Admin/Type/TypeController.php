<?php

namespace App\Http\Controllers\Admin\Type;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class TypeController extends Controller
{
    public function index()
	{
		//遍历所有付款方式
		$type = DB::table('moneytype') ->where('adminid','=',session('userid')) -> get();
		$num = count($type);
		return view('Admin.Type.moneytype',['type'=>$type,'num'=>$num]);
	}
	//收款方式状态
	public function moneystatus(Request $request)
	{
		//查询现在的用户账号状态
		$data = DB::table('moneytype')->where('id','=',$request->input('id'))->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('moneytype')->where('id','=',$request->input('id'))->update(['status'=>0])){
				echo 0;
			}
		}else{
			if(DB::table('moneytype')->where('id','=',$request->input('id'))->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//删除收款方式
	public function moneydel(Request $request)
	{
		//直接删除  然后返回状态码
		if(DB::table('moneytype')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//收款方式编辑
	public function update(Request $request)
	{
		//写入数据库
		if(DB::table('moneytype') -> where('id','=',$request -> id) -> update(['type'=>$request->v])){
			echo 1;
		}else{
			echo 0;
		}
	}
	//添加类型
	public function typeadd()
	{
		//加载添加页面
		return view('Admin.Type.add');
	}
	//处理添加
	public function dotypeadd(Request $request)
	{
		$type['type'] = $request -> val;
		$type['adminid'] = session('userid');
			if(DB::table('moneytype') -> insert($type)){
				echo 1;
			}else{
				echo 0;
			}
	}
}
