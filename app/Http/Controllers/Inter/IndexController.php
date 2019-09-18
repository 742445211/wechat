<?php

namespace App\Http\Controllers\Inter\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class IndexController extends Controller
{
	//首页数据
	public function index(Request $request)
	{
		$time = time();
		if($time - $request -> time < 60){
			if($request->has('offset') && $request->has('limit')){
				//上划加载   offset从第几条开始    limit取多少条数据
	$Res = DB::table('work') -> where('status','=',1) -> where('addresslite','like','%'.$request -> address.'%') -> offset($request -> offset) -> limit($request -> limit) ->orderBy('addtime','desc') -> get();
			}else{
				//正常
				$Res = DB::table('work') -> where('status','=',1)  -> where('addresslite','like','%'.$request -> address.'%') -> orderBy('addtime','desc') -> take(8) -> get();
			}
			$indexInfo = IndexController::indexInfo($Res);
			return ['msg'=>'ok','code'=>'0','result'=>$indexInfo];
		}else{
			echo json_encode(['msg'=>'err','code'=>'1','result'=>'超时']);
		}
		
	}
	//根据ip进行定位
	public function getMap(Request $request)
	{
		$map = "https://restapi.amap.com/v3/geocode/regeo?key=".$request -> key."&location=".$request -> location;
		$map = file_get_contents($map);
		return $map;
	}
	//定位选取四川省的地级市
	public function getAdd()
	{
		//获取所有的地址
		$city = DB::table('city') -> get();
        return json_decode($city);
		
	}
	//获取单条详情数据
	public function detail(Request $request)
	{
		if($request -> times && $request -> id && $request -> token){
			$time = time();
			if($time - $request -> times < 60){
				$detail = DB::table('work') -> where('id','=',$request -> id) -> first(); //查询当前兼职的id
				
				if($request -> token == 'null'){  //证明用户没有授权登录过
					$res['is_cplt'] = false;    //报名和收藏直接赋值为空
					$res['is_love'] = false;
				}else{   //已登录
					$userinfo = DB::table('homeuser') -> where('token','=',$request -> token) -> first();
						//判断报名
						if($workcplt = $detail -> cplt){
							//判断该用户id在不在当前兼职已报名里面
							if(in_array($userinfo->id,explode(',',rtrim($workcplt,',')))){
								$res['is_cplt'] = true;
							}else{
								$res['is_cplt'] = false;
							}
						}else{
							$res['is_cplt'] = false;
						}

						//判断收藏 homeuser表的love字段里面有没有work表的id
						if($userinfo -> love){   //判断该用户是否有收藏记录
							if(in_array($request -> id,explode(',',rtrim($userinfo->love,',')))){
								$res['is_love'] = true;
							}else{
								$res['is_love'] = false;
							}
						}else{
							$res['is_love'] = false;
						}
				}
				
				//第一部分   --大概信息
				$res['id'] = $detail->id;
				$res['title'] = $detail -> title;
				$res['addresslite'] = $detail -> addresslite;
				$res['addtime'] = date('Y/m/d H:i',$detail -> addtime);
				$res['price'] = $detail -> price;
				$res['cate'] = DB::table('cate') -> where('id','=',$detail -> cate) -> value('cates');
				$res['days'] = DB::table('time') -> where('id','=',$detail -> days) -> value('type');
				if($detail -> hots){     //处理标签
					$hots = explode(',',rtrim($detail -> hots,','));
					foreach($hots as $key => $value){
						$res['hots'][$key] = DB::table('hots') -> where('id','=',$value) -> value('type');
					}
				}else{
					$res['hots'][0] = '热门';
				}
				//第二部分    --联系人
				$res['contacts'] = $detail -> contacts;
				$res['phone'] = $detail -> phone;
				//第三部分  --正文介绍
				$res['content'] = $detail -> content;
				//第四部分  --地址
				$res['address'] = $detail -> address;
				$res['map'] = $detail -> map;
				//公司信息
				$res['groupinfo'] = $detail -> groupinfo;
				$res['grouplite'] = $detail -> grouplite;  //公司简称
				$res['startdate'] = $detail -> startdate;  //起始时间
				$res['enddate'] = $detail -> enddate;      //结束时间
				
				
				//推荐 -- 两条  依据地址推荐
				//把本条的字符串分割成区
				$substr = explode('/',$detail -> addresslite); //只获取本区的两条数据
				$tj = DB::table('work') -> orderBy('addtime','desc') -> where('addresslite','like','%'.$substr[1].'%') -> take(2) -> get();
				//处理推荐的数据
				foreach($tj as $k => $tui){
					$tuijian['id'] = $tui -> id;
						$tuijian['title'] = $tui -> title;
						$tuijian['addresslite'] = $tui -> addresslite;
						$tuijian['addtime'] = date('y/m/d',$tui -> addtime);
						$tuijian['price'] = $tui -> price;
						$tuijian['days'] = DB::table('time') -> where('id','=',$tui -> days) -> value('type');
		//				$tuijian['hots'] = 
							if($tui -> hots){
								$hotsid = rtrim($tui -> hots,',');
								$tuijian['hots'] = DB::select('select type from hots where id in('.$hotsid.')');
							}else{
								$tuijian['hots'] = '推荐';
							}
					$res['tuijian'][$k] = $tuijian;
				}
				//报名人数
				if($detail -> cplt){
					$res['cplt'] = count(explode(',',rtrim($detail -> cplt,',')));
				}else{
					$res['cplt'] = '0';
				}
				return json_encode($res,true);
			}else{
				echo json_encode(['msg'=>'err','code'=>'1','result'=>'请求超时']);
			}
		}else{
			echo json_encode(['msg'=>'err','code'=>'2','result'=>'缺少参数']);
		}
	}
	//详情页进行收藏
	public function goLove(Request $request)
	{
		$time = time();
		if($request -> token && $request -> workid && $time-$request -> times < 10){
			//收藏就是把work表的id加入到homeuser表的love字段里面   然后把work表linkenum字段自增1
			//首次按获取用户表的love字段
		  $userlove = DB::table('homeuser') -> where('token','=',$request -> token) -> value('love');
			if($userlove){  //如果用户的love有值
				$userlovearr = explode(',',rtrim($userlove,','));
				//判断用户是否已经收藏
				if(in_array($request -> workid,$userlovearr)){
					return ['msg'=>'err','code'=>'1','result'=>'用户已收藏,收藏失败'];
				}
			   //没有收藏的话直接把工作id拼接到用户的love字段后面
				$newUserLove['love'] = $userlove.$request -> workid.',';
				//拼接好了再写回去
				if(DB::table('homeuser') -> where('token','=',$request -> token) -> update($newUserLove)){
					//成功后进行work标的likenum字段的自增操作
					DB::table('work') -> where('id','=',$request -> workid) -> increment('likenum');
					return ['msg'=>'ok','code'=>'0','result'=>'成功'];
				}else{
					return ['msg'=>'err','code'=>'2','result'=>'错误'];
				}
			}else{
				//没有值直接收藏
				$newUserLove['love'] = $userlove.$request -> workid.',';
				//拼接好了再写回去
				if(DB::table('homeuser') -> where('token','=',$request -> token) -> update($newUserLove)){
					//成功后进行work标的likenum字段的自增操作
					DB::table('work') -> where('id','=',$request -> workid) -> increment('likenum');
					return ['msg'=>'ok','code'=>'0','result'=>'成功'];
				}else{
					return ['msg'=>'err','code'=>'2','result'=>'错误'];
				}
			}
		}else{
			return ['msg'=>'err','code'=>'3','result'=>'非法请求'];
		}
	}
	//首页搜索功能的实现
	public function search(Request $request)
	{
		$time = time();
		if($time - $request -> times < 10 && $request -> result){
			$result = DB::table('work') -> where('title','like','%'.$request -> result.'%') -> get();
			  $info = [];
				foreach($result as $key => $value){
					$info[$key]['id'] = $value -> id;   //id
					$info[$key]['title'] = $value -> title;    //标题
					$info[$key]['addresslite'] = $value -> addresslite;   //地址
					$info[$key]['addtime'] = date('y/m/d',$value->addtime);    //发布时间
					$info[$key]['price'] = $value -> price;    //薪资
					$info[$key]['days'] = DB::table('time') -> where('id','=',$value->days) -> value('type');  //薪资周期
					$info[$key]['header'] = $value -> header; //封面图
					//处理热门标签
					$hots = explode(',',rtrim($value->hots,','));
						if(count($hots)>3){
							$hots = explode(',',rtrim($value->hots,','));
							$re[0] = $hots[0];   //只显示三个
							$re[1] = $hots[1];
							$re[2] = $hots[2];
							$hots = implode(',',$re);
						}else{
							$hots = rtrim($value->hots,',');
						}
				if($hots){
						$info[$key]['hots'] = DB::select('select type from hots where id in('.$hots.')');
					}
				
				}
				return json_encode($info,true);

				
		}else{
			return ['msg'=>'err','code'=>'2','result'=>'输入为空'];
		}
	}
	//获取首页筛选的地理位置，分类，价格区间信息
	public function getSelectInfo(Request $request)
	{
		$times = time();
		if($times - $request -> times < 10 && $request -> address){
			$city = DB::table('sichuan_address') -> where('p_address',$request -> address) -> get();  //区域
			$cate = DB::table('cate') ->where('status','1') -> get();  //分类
			$price = [
				['id'=>'1','text' => '50以下'],
				['id'=>'2','text' => '50-100'],
				['id'=>'3','text' => '100-150'],
				['id'=>'4','text' => '150-200'],
				['id'=>'5','text' => '200-250'],
				['id'=>'6','text' => '250以上']
			];
			$data = [];
			foreach($city as $a => $b){   //城市处理
				$citys[$a]['id'] = $b -> id;
				$citys[$a]['text'] = $b -> address;
			}
			foreach ($cate as $c => $d) {   //分类处理
				$cates[$c]['id'] = $d -> id;
				$cates[$c]['text'] = $d -> cates;
			}
			$data['0'] = $citys;
			$data['1'] = $cates;
			$data['2'] = $price;
			return ['msg' => 'ok','code' => '0','result' => $data,'add'=>$request -> address];
		}else{
			return ['msg'=>'err','code'=>'1','result'=>'null'];
		}
	}
	

	//首页的筛选处理 + 数据返回
	public function getSelect(Request $request)
	{
		$times = time();
		if($times - $request -> times < 10 && $request -> address && $request -> keyword){
			$keywords = explode('/',$request -> keyword);  //区分筛选的哪些条件 0区域  1类型  2薪资区间
			switch($keywords['1']){  //索引1为搜索条件判断   0为搜索条件
				case '0':  //为区域
					//查询地区
					$city = DB::table('sichuan_address') -> where('id',$keywords['0']) -> value('address');
					$add = $request -> address .'/'.$city;  //市区拼接起来的   如 成都市/锦江区
					$Res = DB::table('work') -> where('status','1') -> where('addresslite','like','%'.$add.'%') -> get();
					//把查询到的数据进行处理
					$addWorkInfo = IndexController::indexInfo($Res);
					return $addWorkInfo;
					break;

				case '1':
					$Res = DB::table('work') -> where('status','1') -> where('cate',$keywords['0']) -> get();
					$addCateInfo = IndexController::indexInfo($Res);
					return ['msg' => 'ok','code' => '1','result' => $addCateInfo];
					break;

				case '2':
					$Res = []; //价格筛选
					if($keywords['0'] == '1'){
						$Res = DB::select("select * from work where status=1 and price<50");
					}elseif($keywords['0'] == '2'){
						$Res = DB::select("select * from work where status=1 and price>=50 and price<=100");
					}elseif($keywords['0'] == '3'){
						$Res = DB::select("select * from work where status=1 and price>=100 and price<=150");
					}elseif($keywords['0'] == '4'){
						$Res = DB::select("select * from work where status=1 and price>=150 and price<=200");
					}elseif($keywords['0'] == '5'){
						$Res = DB::select("select * from work where status=1 and price>=200 and price<=250");
					}elseif($keywords['0'] == '6'){
						$Res = DB::select("select * from work where status=1 and price>=250");
					}
					$addPriceInfo = IndexController::indexInfo($Res);
					return ['msg' => 'ok','code' => '2','result' => $addPriceInfo];
					break;
			}
			
		}
	}
	//首页四个banner分类的数据获取和处理
	public function getCates(Request $request)
	{
		$times = time();
		if($times - $request -> times < 10 && $request -> cateid){
			switch($request -> cateid){   //判断获取的是什么样的数据
				case '1'://精选兼职   随机获取数据
					$jingxuan = DB::select("SELECT * FROM work WHERE id >= ((SELECT MAX(id) FROM work)-(SELECT MIN(id) FROM work)) * RAND() + (SELECT MIN(id) FROM work) and status = '1' limit 10");
					$jxRes = IndexController::indexInfo($jingxuan);
					$data['img'] = '../../images/index/1.png';
					$data['title'] = '精选兼职';
					return ['msg' => 'ok','code' => '0','result' => $jxRes,'title' => $data];
					break;
				case '2'://周末兼职   搜索标题为周末的
					$jingxuan = DB::table('work') -> where('title','like','%周末%') -> orwhere('title','like','%周日%') -> orwhere('content','like','%周末%') -> orwhere('content','like','%周日%') -> where('status','1') -> get();
					$jxRes = IndexController::indexInfo($jingxuan);
					$data['img'] = '../../images/index/2.png';
					$data['title'] = '周末兼职';
					return ['msg' => 'ok','code' => '0','result' => $jxRes,'title' => $data];
					break;
				case '3'://日结兼职   搜索标题或者内容为日结的
					$jingxuan = DB::table('work') -> where('title','like','%日结%') -> orwhere('title','like','%日结%') -> orwhere('content','like','%日结%') -> orwhere('content','like','%日结%') -> where('status','1') -> get();
					$jxRes = IndexController::indexInfo($jingxuan);
					$data['img'] = '../../images/index/3.png';
					$data['title'] = '日结兼职';
					return ['msg' => 'ok','code' => '0','result' => $jxRes,'title' => $data];
					break;
				case '4'://实习兼职   搜索标题或者内容为实习的
					$jingxuan = DB::table('work') -> where('title','like','%实习%') -> orwhere('title','like','%实习%') -> orwhere('content','like','%实习%') -> orwhere('content','like','%实习%') -> where('status','1') -> get();
					$jxRes = IndexController::indexInfo($jingxuan);
					$data['img'] = '../../images/index/4.png';
					$data['title'] = '实习兼职';
					return ['msg' => 'ok','code' => '0','result' => $jxRes,'title' => $data];
					break;
			}
			
		}else{
			return 'err';
		}
	}
	//首页信息处理方法
	public function indexInfo($Res)
	{
		if(count($Res)){
			foreach($Res as $key => $value){
			$info[$key]['id'] = $value -> id;   //id
			$info[$key]['title'] = $value -> title;    //标题
			$info[$key]['addresslite'] = $value -> addresslite;   //地址
			$info[$key]['addtime'] = date('y/m/d',$value->addtime);    //发布时间
			$info[$key]['price'] = $value -> price;    //薪资
			$info[$key]['days'] = DB::table('time') -> where('id','=',$value->days) -> value('type');  //薪资周期
			$info[$key]['header'] = $value -> header; //封面图
			//处理热门标签
			$hots = explode(',',rtrim($value->hots,','));
				if(count($hots)>3){
					$hots = explode(',',rtrim($value->hots,','));
					$re[0] = $hots[0];   //只显示三个
					$re[1] = $hots[1];
					$re[2] = $hots[2];
					$hots = implode(',',$re);
				}else{
					$hots = rtrim($value->hots,',');
				}
			if($hots){
					$info[$key]['hots'] = DB::select('select type from hots where id in('.$hots.')');
				}
			}
			return $info;
		}else{
			return '';
		}
		
	}
}

