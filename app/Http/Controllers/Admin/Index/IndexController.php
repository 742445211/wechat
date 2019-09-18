<?php

namespace App\Http\Controllers\Admin\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class IndexController extends Controller
{

    //加载后台首页
	public function index()
	{
		//查询兼职总数
		$work = DB::table('work') -> where('adminid','=',session('userid')) -> count();
		$cplt = DB::table('cplt') -> where('adminid','=',session('userid')) -> count();
		$tousu = DB::table('tousu') -> where('adminid','=',session('userid')) -> count();
		$yijian = DB::table('yijian') -> where('adminid','=',session('userid')) -> count();
		return view('Admin.Index.index',['work'=>$work,'cplt'=>$cplt,'tousu'=>$tousu,'yijian'=>$yijian]);
	}
}
