<?php

namespace App\Http\Controllers\Admin\Node;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class NodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		if($request -> keyword){
			//只有关键字
			$num = DB::table('node') -> where('nodename', 'like', '%'.$request -> keyword.'%') -> count();
			$list = DB::table('node') -> where('nodename', 'like', '%'.$request -> keyword.'%') -> paginate(10);
			return view('Admin.Node.node',['node'=>$list,'num'=>$num,'keyword'=>$request -> keyword,'request' => $request -> all()]);
		}else{
			 //加载所有节点
			$node = DB::table('node') -> paginate(10);
			$num = DB::table('node') -> count();
			return view('Admin.Node.node',['node'=>$node,'num'=>$num,'request' => $request -> all()]);
		}
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //加载添加页面
		return view('Admin.Node.nodeadd');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request -> nodename && $request -> fname && $request -> kname){
			$data['nodename'] = $request -> nodename;
			$data['kname'] = $request -> kname;
			$data['fname'] = $request -> fname;
			//添加到数据库
			if(DB::table('node') -> insert($data)){
				echo '添加成功!';
			}else{
				echo '添加失败!';
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
        //
		echo $id;
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
	//权限节点删除
	public function nodedel(Request $request)
	{
		//删除节点
		if(DB::table('node') -> where('id','=',$request -> id) -> delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//权限节点编辑
	public function nodeedit(Request $request)
	{
		//将更改写入数据库
		if(DB::table('node') -> where('id','=',$request -> id) -> update(['nodename'=>$request->v])){
			echo 1;
		}else{
			echo 0;
		}
	}
}
