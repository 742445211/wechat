<?php

namespace App\Http\Controllers\Admin\Work;

use App\JobImage;
use App\Position;
use App\Recommend;
use App\Work;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Queue\WorkerOptions;

class WorkController extends Controller
{
    public function index(Request $request)
    {
		$level = DB::table('adminuser') -> where('id','=',session('userid')) -> value('level');  //为1是超管
        $handle = DB::table('work');

        $endtime   = $request -> endtime;   //结束时间
        $starttime = $request -> starttime;   // 开始时间
        $keyword   = $request -> keyword;    // 关键词
        $status    = $request -> status;   // 发布状态
        $username  = $request -> username;   //发布人
        if($starttime){   // 开始时间
            $starttime=strtotime($starttime); //获取日期转换后的时间戳
        }
        if($endtime){   //结束时间
            $endtime=strtotime($endtime); //获取日期转换后的时间戳
        }

        $keyword && $handle   -> where('title','like','%'.$keyword.'%');
        $status && $handle    -> where('status','=',$status);
        $username && $handle  -> where('adminid','=',$username);
        $starttime && $handle -> where('addtime','>',$starttime);
        $endtime && $handle   -> where('addtime','<',$endtime);
        // 获取数据
        $list = $handle -> orderBy('addtime','desc') -> paginate(8);
        //遍历所有兼职
        $num = DB::table('work') -> count();
        return view('Admin.Work.work',['list'=>$list,'num'=>$num,'request' => $request -> all()]);
		
    }

    public function create(Request $request)
    {
		if($request -> address && $request -> map){
			$address = $request -> address;
			$map = $request -> map;
			$addresslite = $request -> addresslite;
		}else{
			$address = '';
			$map = '';
			$addresslite = '';
		}
        //职位添加
		//获取所有的可用结算周期
		$times = DB::table('time') -> where('status','=',1) ->get();
		//获取所有热门标签
		$hots = DB::table('hots') -> where('status','=',1) ->get();
		//获取兼职类型标签
		$types = DB::table('types') -> where('status','=',1) ->get();
		//获取分类信息
		//$cates = DB::table('cate') -> where('status','=',1)->get();     // -> where('adminid','=',session('userid'))
        $cates = Position::where('level',0) -> where('status',1) -> get() -> toArray();
        $one = ['id'=>100000,'name'=>'请选择'];
        array_unshift($cates,$one);
      	//获取可用头像 
      	$header = DB::table('header') -> where('status','=',1) -> get();
		return view('Admin.Work.add',['times'=>$times,'types'=>$types,'hots'=>$hots,'cates'=>$cates,'address'=>$address,'map'=>$map,'addresslite'=>$addresslite,'header'=>$header]);
    }

    public function select(Request $request)
    {
        $data = Position::where('pid',$request->id) -> get() -> toArray();
        $one = ['id'=>100000,'name'=>'请选择'];
        array_unshift($data,$one);
        return ['code'=>0,'result'=>$data];
    }

    public function show($id)
    {
        //加载详情查看列表  现获取本id信息
		$data = DB::table('work') -> where('id','=',$id) -> first();
        $path = JobImage::where('workid',$data->id) -> get();
		return view('Admin.Work.show',['data'=>$data,'path'=>$path]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }
	
    public function destroy($id)
    {
        //
    }
	//兼职状态管理
	public function workstatus(Request $request)
	{
		//查询现在的用户账号状态
		$data = DB::table('work')->where('id','=',$request->input('id'))->value('status');
		//直接更改
		if($data == 1){
			if(DB::table('work')->where('id','=',$request->input('id'))->update(['status'=>0])){
                $res = Recommend::where('workid',$request->id) -> update(['status'=>0]);
                $a = Work::where('id',$request->id) -> update(['is_rec'=>0]);
				echo 0;
			}
		}else{
			if(DB::table('work')->where('id','=',$request->input('id'))->update(['status'=>1])){
				echo 1;
			}
		}
	}
	//兼职删除
	public function workdel(Request $request)
	{
		//直接删除  然后返回状态码
		if(DB::table('work')->where('id','=',$request -> id)->delete()){
			echo 1;
		}else{
			echo 0;
		}
	}
	//发布兼职信息管理
	public function workadd(Request $request)
	{
		if($request->title && $request->content && $request->price && $request -> days && $request -> hots && $request -> types && $request -> post_id && $request -> address && $request -> map && $request -> contacts && $request -> phone && $request -> group && $request -> addresslite && $request -> grouplite && $request -> startdate && $request -> enddate)
		{
          	$data['header']      = $this -> header($request -> file('file'));
			$data['title']       = $request -> title;
			$data['content']     = $request -> content;
			$data['price']       = $request -> price;
			$data['days']        = $request -> days;
			$data['hots']        = implode(',',$request -> hots).',';  //标签   需要转换成字符串
			$data['types']       = implode(',',$request -> types).',';  //标签
			$data['post_id']     = $request -> post_id;
			$map = explode(',',$request -> map);
			$data['map']         = $map[1].','.$map[0];
			$data['address']     = $request -> address;
			$data['contacts']    = $request -> contacts;
			$data['phone']       = $request -> phone;
			$data['groupinfo']   = $request -> group;
			$data['grouplite']   = $request -> grouplite;   //公司简称
			$data['pid']         = session('userid');
			$data['addtime']     = time();
			$data['adminid']     = session('userid');
			$data['addresslite'] = $request -> addresslite;
			$data['number']      = $request -> number;
			$start = str_replace('-','',$request -> startdate);   //开始时间正则匹配
			$end = str_replace('-','',$request -> enddate);    //结束时间正则匹配
			if($start > $end){
				return back() -> with('times','开始时间不能大于结束时间!');
			}
			$data['startdate'] = $request -> startdate;   //起始时间
			$data['enddate']   = $request -> enddate;			//结束时间
			if(DB::table('work')->insert($data))
			{
				return redirect('/worklist') -> with('addok','添加成功!');
			}
		}else{
			return back() -> withInput() ->with('adderr','添加失败!');
		}
	}
	//上传图片
    public function header($files)
    {
        $file = $files;
        $entension = $file -> getClientOriginalExtension(); //上传文件的后缀.
        $newName = str_random('19');
        $path = $newName.'.'.$entension;
        $info = $file -> move(base_path('public') . '/upload_header/',$path);
        $img = 'https://www.xiaoshetong.cn/upload_header/'.$path;   //路径
        return $img;
    }
	//获取坐标
	public function getadd(Request $request)
	{
		//实时获取地址
        if($request->location == null){
            $d = file_get_contents("http://restapi.amap.com/v3/geocode/geo?key=d5e372073f391117a790cf5cdc16d6f4&s=rsv3&address=" . $request->address);
        }else{
            $d = file_get_contents("https://restapi.amap.com/v3/geocode/regeo?key=77ebcf13e1266f32784d631a4c3fb194&location=".$request -> location );
        }

		return json_decode($d,true);
	}
	
	//地图
	public function getmap()
	{
//		echo 1;
		return view('Admin.Work.map');
	}
	public function editmap($id)
	{
		return view('Admin.Work.editmap',['id'=>$id]);
	}
	//兼职编辑
	public function workedit(Request $request)
	{
		$id = $request -> id;
		if($request -> address && $request -> map){
			$address = $request -> address;
			$map = $request -> map;
			$addresslite = $request -> addresslite;

		}else{
			$address = '';
			$map = '';
			$addresslite = '';
		}
		$list = DB::table('work') -> where('id','=',$request -> id) -> first();
		// dd($list);
		return view('Admin.Work.edit',['id'=>$id,'list' => $list,'address'=>$address,'map'=>$map,'addresslite'=>$addresslite]);
	}
	//兼职编辑完成修改
	public function workup(Request $request)
	{
		if($request -> id && $request->title && $request->content && $request->price && $request -> days && $request -> hots && $request -> types && $request -> cate && $request -> address && $request -> map && $request -> contacts && $request -> phone && $request -> group && $request -> addresslite && $request -> grouplite && $request -> startdate && $request -> enddate)
		{
			$data['title']       = $request -> title;
			$data['content']     = $request -> content;
			$data['price']       = $request -> price;
			$data['days']        = $request -> days;
			$data['hots']        = implode(',',$request -> hots).',';  //标签   需要转换成字符串
			$data['types']       = implode(',',$request -> types).',';  //标签
			$data['post_id']     = $request -> cate;
			$map = explode(',',$request -> map);
			$data['map']         = $map[1].','.$map[0];
			$data['address']     = $request -> address;
			$data['contacts']    = $request -> contacts;
			$data['phone']       = $request -> phone;
			$data['groupinfo']   = $request -> group;
			$data['grouplite']   = $request -> grouplite;   //公司简称
			$data['pid']         = session('userid');
			$data['addtime']     = time();
			$data['adminid']     = session('userid');
			$data['addresslite'] = $request -> addresslite;
            $data['number']      = $request -> number;
			$start = str_replace('-','',$request -> startdate);   //开始时间正则匹配
			$end = str_replace('-','',$request -> enddate);    //结束时间正则匹配
			if($start > $end){
				return back() -> with('times','开始时间不能大于结束时间!');
			}
			$data['startdate']   = $request -> startdate;   //起始时间
			$data['enddate']     = $request -> enddate;			//结束时间
			if(DB::table('work')->where('id','=',$request -> id) -> update($data))
			{
				return redirect('/worklist') -> with('addok','保存成功!');
			}
		}else{
			return back() -> with('adderr','保存失败!');
		}
	}

    /**
     * 编辑图片
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function jobImage($id)
    {
        $data = JobImage::with('work:id,title') -> where('workid',$id) -> get();

        return view('Admin.Work.img',['data'=>$data,'workid'=>$id]);
    }

    //删除图片
    public function delImg(Request $request)
    {
        $id = $request->id;
        $res = JobImage::where('id',$id) -> delete();
        if($res){
            return ['msg'=>'ok','code'=>0,'result'=>'删除成功'];
        }
    }

    //把图片路径保存到数据库
    public function addJobImg(Request $request)
    {
        $img = $request->img;
        $workid = $request->workid;
        foreach ($img as $v){
            $data['workid']  = $workid;
            $data['imgpath'] = $v;
            $res = JobImage::create($data);
        }
        if($res){
            return ['msg'=>'ok','code'=>0,'result'=>'chenggong'];
        }
    }

    /**
     * 添加推荐工作的图片
     * @param Request $request
     * @return array
     */
    public function addRecImg(Request $request)
    {
        $data['name']    = $request->input('name');//工作标题
        $data['intro']   = $request->input('intro');//工作介绍
        $data['workid']  = $request->id; //工作ID
        $data['imgpath'] = $request->input('img');//图片路径
        $data['status']  = 1;
        if($request->type == 'insert'){
            $res = Recommend::create($data);
            if($res){
                $a = Db::table('work') -> where('id',$request->id) -> update(['is_rec'=>1]);
                return ['code'=>0];
            }
        }else{
            $res = Recommend::where('workid',$request->id) -> update($data);
            $a = Db::table('work') -> where('id',$request->id) -> update(['is_rec'=>1]);
            if($res){
                return ['code'=>1];
            }
        }

    }

    public function showRecImg(Request $request)
    {
        if($request->status == 0){
            $has = Recommend::where('workid',$request->id) -> first();
            if($has){
                return view('Admin.Work.rec',['type'=>'update','id'=>$request->id,'has'=>true,'data'=>$has]);
            }else{
                return view('Admin.Work.rec',['has'=>false,'id'=>$request->id]);
            }
        }else{
            $res = Recommend::where('workid',$request->id) -> update(['status'=>0]);
            $a = Work::where('id',$request->id) -> update(['is_rec'=>0]);
            if($res){
                return ['code'=>0];
            }
        }

    }
}
