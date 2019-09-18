<?php

namespace App\Http\Controllers\Inter\About;

use BaseFile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Redis;
class AboutController extends Controller
{
    //个人中心我的收藏
	public function getLove(Request $request)
	{
      //查询当前用户的love字段里面的所有workid
    $time = time();
    if($time - $request -> time<10 && $request -> token){
      if($love = DB::table('homeuser') -> where('token','=',$request -> token) -> value('love')){
          $loveid = rtrim($love,',');
          //查询是否已经报名   work下面的cplt是否存在homeuser的id
          //拿到loveid查询work表下面的cplt
          $userId = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
          $loves = DB::select("select * from work where id in(".$loveid.")");
            foreach($loves as $key => $value){
                if($workCplt = $value -> cplt){  //如果报名不为空
                  $workCpltArr = explode(',',rtrim($workCplt,','));
                    if(in_array($userId,$workCpltArr)){
                      $data[$key]['is_cplt'] = true;
                    }else{
                      $data[$key]['is_cplt'] = false;
                    }
                }else{
                      $data[$key]['is_cplt'] = false;
                }
                $data[$key]['id'] = $value -> id;
                $data[$key]['header'] = $value -> header;
                $data[$key]['title'] = $value -> title;
                $data[$key]['addtime'] = date('Y/m/d',$value -> addtime);
                $data[$key]['price'] = $value -> price;
                $data[$key]['days'] = DB::table('time') -> where('id','=',$value -> days) -> value('type');
            }
          return $data;
      }else{
        //如果没有收藏就返回空
        return ['msg'=>'ok','code'=>'-1','result'=>'结果为空'];
      }
    }
      
    }
    //个人中心我的收藏里面的取消收藏
    public function qxLove(Request $request)
    {
      $time = time();
      if($time - $request->times < 10){
      	//通过用户的token查询出对应的收藏
          if($love = DB::table('homeuser') -> where('token','=',$request -> token) -> value('love')){
            if($love){
              $lovearr = explode(',',rtrim($love,','));
              $arr = array_merge(array_diff($lovearr, array($request->workid)));
              //删除后写回去
              if($arr){
              		$res['love'] = implode(',',$arr).',';
                    if(DB::table('homeuser') -> where('token','=',$request -> token) -> update($res)){
                      return ['msg'=>'ok','code'=>'0','result'=>'成功'];
                    }else{
                      return ['msg'=>'err','code'=>'4','result'=>'网络错误'];
                    }
              }else{
                	$res['love'] = '';
              		DB::table('homeuser') -> where('token','=',$request -> token) -> update($res);
                	return ['msg'=>'ok','code'=>'-1','result'=>'成功[null]'];
              }
              
              
            }else{
                return ['msg'=>'err','code'=>'1','result'=>'收藏为空'];
            }
          }else{
            return ['msg'=>'err','code'=>'2','result'=>'用户不存在'];
          }
      }else{
      	return ['msg'=>'err','code'=>'3','result'=>'非法请求'];
      }
    }
  	//已报名列表
  public function getCplt(Request $request)
  {
    //通过用户token查询到报名列表  分别查询各种状态的报名信息
    $times = time();
    if($times - $request -> times < 10 && $request -> token){
      $allWork = [];  //定义全部工作
      $cpltAll = [];  //定义cplt表
      $userId = DB::table('homeuser') -> where('token',$request -> token) -> value('id');  //用户id
      $cpltAll = DB::table('cplt') -> where('userid',$userId) -> get();   //全部
      if($cpltAll){   //如果全部为空  就没必要进行下去了
        //假设不为空
          $workId = [];
          for ($i=0; $i < count($cpltAll); $i++) { 
            $workId[$i] = $cpltAll[$i] -> workid;
          }
          if($workId){
            $allNum = implode(',',$workId);
            //到work表查询兼职信息
            $allWork = DB::select("select title,addresslite,id from work where id in(".$allNum.")");
          }else{
            $allWork = [];
          }
          

          //已报名
          $yiBaoMing = DB::table('cplt') -> where('userid',$userId) -> where('status','0') -> get();  //已报名 状态0待审核
          $yiBaoMingWork = [];  //提前定义已报名
          if($yiBaoMing){  //如果已报名不为空
              for ($i=0; $i < count($yiBaoMing); $i++) { 
                $yiBaoMingId[$i] = $yiBaoMing[$i] -> workid;
                $yiBaoMingStr = implode(',',$yiBaoMingId);
                $yiBaoMingWork = DB::select("select title,addresslite,id from work where id in(".$yiBaoMingStr.")");
              }
          }else{
            $yiBaoMingWork = [];
          }

          //待面试
          $daiMianShi = DB::table('cplt') -> where('userid',$userId) -> where('status','1') -> get(); //待面试
          $daiMianShiWork = [];  //提前定义待面试
          if($daiMianShi){  //如果待面试不为空
            for ($i=0; $i < count($daiMianShi); $i++) { 
                $daiMianShiId[$i] = $daiMianShi[$i] -> workid;
                $daiMianShiStr = implode(',',$daiMianShiId);
              $daiMianShiWork = DB::select("select title,addresslite,id from work where id in(".$daiMianShiStr.")");
              }
          }

          //工作中
          $gongZuoZhong = DB::table('cplt') -> where('userid',$userId) -> where('status','2') -> get(); //工作中
          $gongZuoZhongWork = [];   //提前定义工作中
          if($gongZuoZhong){  //如果待面试不为空
            for ($i=0; $i < count($gongZuoZhong); $i++) { 
                $gongZuoZhongId[$i] = $gongZuoZhong[$i] -> workid;
                $gongZuoZhongStr = implode(',',$gongZuoZhongId);
          $gongZuoZhongWork = DB::select("select title,addresslite,id from work where id in(".$gongZuoZhongStr.")");
              }
          }

          //已结束
          $yiJieShu = DB::table('cplt')    //已结束(包含已结束和未通过的兼职)
                      -> where('status','3') 
                      -> orwhere('status','4')
                      -> where('userid',$userId)
                      -> get();
          $yiJieShuWork = [];  //提前定义已结束
          if($yiJieShu){  //如果待面试不为空
            for ($i=0; $i < count($yiJieShu); $i++) { 
                $yiJieShuId[$i] = $yiJieShu[$i] -> workid;
                $yiJieShuStr = implode(',',$yiJieShuId);
          $yiJieShuWork = DB::select("select title,addresslite,id from work where id in(".$yiJieShuStr.")");
              }
          } 
      }  
      return ['msg'=>'ok','code'=>'0','result'=>['all'=>$allWork,'yibaoming'=>$yiBaoMingWork,'daiMianShi'=>$daiMianShiWork,'gongZuoZhong'=>$gongZuoZhongWork,'yiJieShu'=>$yiJieShuWork,'cpltAll'=>$cpltAll]];

    }else{
      return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
    }




    
  }
  //取消报名
  public function qxCplt(Request $request)
  {
    //通过传来的workid查询出cplt表里面的信息
    $cpltInfo = DB::table('cplt') -> where('workid','=',$request -> workid) -> first();
    //删除homeuser表里面的readystatus字段里面的cplt标的id
    $ready = DB::table('homeuser') -> where('token','=',$request -> token) -> value('readystatus');//拿到当前用户的表明表
    if($ready){
      $readyArr = explode(',',rtrim($ready,',')); // 组装成数组
      $arr = array_merge(array_diff($readyArr, array($cpltInfo -> id)));
        if($arr){
          $newReadyArr['readystatus'] = implode(',',$arr).',';
        }else{
          $newReadyArr['readystatus'] = '';
        }
        //写入数据库
        if(DB::table('homeuser') -> where('token','=',$request -> token) -> update($newReadyArr)){
        //把work标的cplt字段里面的当前用户id删除
          $workCplt = DB::table('work') -> where('id','=',$request -> workid) -> value('cplt');
          if($workCplt){
                $workCpltArr = explode(',',rtrim($workCplt,',')); // 组装成数组
                $newWorkArr = array_merge(array_diff($workCpltArr, array($cpltInfo->userid)));
              if($newWorkArr){
                $workArr['cplt'] = implode(',',$newWorkArr).',';
              }else{
                $workArr['cplt'] = '';
              }
              if(DB::table('work') -> where('id','=',$request -> workid) -> update($workArr) && DB::table('cplt') -> where('id','=',$cpltInfo -> id) -> delete()){
                return ['msg'=>'ok','code'=>'0','result'=>'成功'];
              }else{
                 return ['msg'=>'ok','code'=>'2','result'=>'失败192'];
              }
          }else{
            return ['msg'=>'err','code'=>'3','result'=>'暂无数据'];
          }

          
        }else{
          return ['msg'=>'ok','code'=>'2','result'=>'失败176'];
        }


    }else{
      return ['msg'=>'err','code'=>'1','result'=>'暂无数据'];
    }
  }
  
  //报名兼职
  public function goCplt(Request $request)
  {
  	//拿到工作id和token
    $time = time();
    if($time - $request -> times < 10 && $request -> workid && $request -> token){
      //首先查询用户的简历信息是否完整
      $userInfo = DB::table('homeuser') -> where('token',$request -> token) -> first();
      if($userInfo -> username && $userInfo -> idcard && $userInfo -> phone){
         // && $request -> oudate && $request -> height && $request -> width && $request -> school && $request -> in_school_date && $request -> spec && $reqeust -> like_work_type && $request -> like_work_address && $request -> like_work && $request -> myshow && $request -> syn && $request -> workdetail
        //简历齐全  可以报名
      }else{
        //简历信息不完整   前台提醒补充简历信息
        return ['msg'=>'err','code'=>'4','result'=>'简历信息不完整'];
      }
    	//通过token拿到用户的id
    	$id = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
    	//通过id查询cplt表看看是否已经报名
    	if($id){
    		$list = DB::table('cplt') -> where('userid','=',$id) -> get();
    		$res[] = '';
    		foreach($list as $key => $value){
    			$res[$key] = $value->workid;
    		}
    		if($res){  
    		 //如果他的收藏不为空
    			if(in_array($request -> workid,$res)){
    				return ['msg'=>'err','code'=>'1','result'=>'用户已报名'];
    			}else{
    				//否则增加一条报名记录
    				$data['userid'] = $id;
    				$data['cplttime'] = time();
    				$data['workid'] = $request -> workid;
    				$data['status'] = 0;
    				$data['adminid'] = DB::table('work') -> where('id','=',$request -> workid) -> value('adminid');
    				if($getId = DB::table('cplt') -> insertGetId($data)){
    					//把插入的id存入homeuser表的readystatus字段
    					//时候崔安获取该用户的readystatus字段里面的值
    					$ready = DB::table('homeuser') -> where('id','=',$id) -> value('readystatus');
    					$newready['readystatus'] = $ready.$getId.',';   //拼接新的报名兼职
    					if(DB::table('homeuser') -> where('id','=',$id) -> update($newready)){  //再写回去
    						//给该工作增加一个报名量
    						$cplt = DB::table('work') -> where('id','=',$request -> workid) -> value('cplt');
    						$newCplt['cplt'] = $cplt.$id.',';
    						DB::table('work') -> where('id','=',$request -> workid) -> update($newCplt);//写回去
    						return ['msg'=>'ok','code'=>'0','result'=>$getId];
    					}else{
    						return ['msg'=>'err','code'=>'2','result'=>'网络错误'];
    					}
    					
    				}else{
    					return ['msg'=>'err','code'=>'3','result'=>'报名失败'];
    				}
    			}
    			
    		}else{
    			//收藏为空
    			//增加一条报名记录
    				$data['userid'] = $id;
    				$data['cplttime'] = time();
    				$data['workid'] = $request -> workid;
    				$data['status'] = 0;
    				$data['adminid'] = DB::table('work') -> where('id','=',$request -> workid) -> value('adminid');
    				if($getId = DB::table('cplt') -> insertGetId($data)){
    					//把插入的id存入homeuser表的readystatus字段
    					//时候崔安获取该用户的readystatus字段里面的值
    					$ready = DB::table('homeuser') -> where('id','=',$id) -> value('readystatus');
    					$newready['readystatus'] = $ready.$getId.',';   //拼接新的报名兼职
    					if(DB::table('homeuser') -> where('id','=',$id) -> update($newready)){  //再写回去
    						//给该工作增加一个报名量
    						$cplt = DB::table('work') -> where('id','=',$request -> workid) -> value('cplt');
    						$newCplt['cplt'] = $cplt.$id.',';
    						DB::table('work') -> where('id','=',$request -> workid) -> update($newCplt);//写回去
    						return ['msg'=>'ok','code'=>'0','result'=>$getId];
    					}else{
    						return ['msg'=>'err','code'=>'2','result'=>'网络错误'];
    					}
    					
    				}else{
    					return ['msg'=>'err','code'=>'3','result'=>'报名失败'];
    				}
    		}
    	}
    }
  }
  //兼职轨迹图详情
  public function getGuiji(Request $request)
  {
  	if($request -> times && $request -> cpltid){
  		$times = $request -> times;
	  	$cpltid = $request -> cpltid;
	  	//通过id到cplt表查询当前报名信息
	  	$list = DB::table('cplt') -> where('id','=',$cpltid) -> first();
	  	if(!$list){
	  		return ['msg'=>'err','code'=>'-1','result'=>'信息不存在'];
	  	}
	  	//需要获取标题信息，拿到workid到work表里面查询
	  	$work = DB::table('work') -> where('id','=',$list->workid) -> first();
	  	$data['title'] = $work -> title;
	  	$data['addtime'] = date('Y-m-d',$work -> addtime);
	  	$data['price'] = $work -> price;
	  	$data['days'] = DB::table('time') -> where('id','=',$work -> days) -> value('type');
	  	//cplt表
	  	$data['status'] = $list -> status;  //报名状态   0待审核   1 待面试  2 直接通过的   3拒绝
	  	$data['prevtime'] = date('Y-m-d H:i:s',$list -> prevtime);   //管理员操作状态时间
	  	$data['cplttime'] = date('Y-m-d H:i:s',$list -> cplttime);  //cplt表的报名时间
	  	return $data;
	  }else{
	  	return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
	  }
  }
  //加载投诉的工作列表
  public function getTousuList(Request $request)
  {
    $time = time();
      if($time - $request -> times && $request -> token){
        //通过token查询用户id   然后拿着id在cplt表里面查询当前的所有兼职
        if($userId = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id')){
            $userCplt = DB::table('cplt') -> where('userid','=',$userId) -> get();
            if($userCplt){  //如果兼职不为空
                $data = [];
                foreach($userCplt as $key => $value){
                  $workId = $value -> workid;
                  //组装单条兼职信息
                  $workInfo =  DB::table('work') -> where('id','=',$workId) -> first();
                  $data[$key]['id'] = $workInfo -> id;
                  $data[$key]['title'] = $workInfo -> title;
                  $data[$key]['header'] = $workInfo -> header;
                  $data[$key]['addtime'] = date('Y-m-d',$workInfo -> addtime);
                  $data[$key]['price'] = $workInfo -> price;
                  $data[$key]['days'] = DB::table('time') -> where('id','=',$workInfo -> days) -> value('type');
                }
                return ['msg'=>'ok','code'=>'0','result'=>$data];
            }else{
                 return ['msg'=>'ok','code'=>'-1','result'=>'查询为空'];
            }
        }else{
          return ['msg'=>'err','code'=>'1','result'=>'用户不存在'];
        }
      }else{
        return ['msg'=>'err','code'=>'2','result'=>'缺少参数'];
      }
  }
  //提交投诉内容
  public function upTousu(Request $request)
  {
    $time = time();
    if($request -> token && $time - $request -> times < 10 && $request -> workid && $request -> detail){
        $userId = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
        //对比tousu   用户   兼职id  投诉列表存在就不让投诉
        if($tousuList = DB::table('tousu') -> where('userid','=',$userId) -> get()){   //当前用户的所有投诉
          foreach($tousuList as $k => $v){
              if($request -> workid == $v -> workid){
                  //如果当前用户的所有投诉里面有本次投诉的兼职id  就直接返回
                return ['msg'=>'ok','code'=>'-1','result'=>'已经投诉过了'];
              }
            }
        }
        //用户id   兼职id   投诉内容  投诉时间   兼职发布人id
        $data['userid'] = $userId;  //用户id
        $data['workid'] = $request -> workid;   //兼职id
        $data['content'] = $request -> detail;   //投诉内容
        $data['sendtime'] = time();    //投诉时间
        $data['adminid'] = DB::table('work') -> where('id','=',$request -> workid) -> value('adminid');  //兼职发布人id
        //写入数据库
        if(DB::table('tousu') -> insert($data)){
            return ['msg'=>'ok','code'=>'0','result'=>'成功'];
        }else{
            return ['msg'=>'err','code'=>'2','result'=>'失败'];
        }
    }else{
            return ['msg'=>'err','code'=>'1','result'=>'请填写后再提交'];
    }

  }
  //提交意见反馈的内容
  public function yiJian(Request $request)
  {
    $time = time();
    if($time - $request -> times && $request -> token && strlen($request -> detail)>0){
      //用户id   内容   反馈时间    
      $data['userid'] = DB::table('homeuser') -> where('token','=',$request -> token) -> value('id');
      $data['content'] = $request -> detail;
      $data['sendtime'] = time();
      if(DB::table('yijian') -> insert($data)){
        return ['msg'=>'ok','code'=>'0','result'=>'成功'];
      }else{
        return  ['msg'=>'err','code'=>'2','result'=>'失败'];
      }
    }else{
       return  ['msg'=>'err','code'=>'1','result'=>'请填写后再提交'];
    }
  }

  //获取用户的简历信息
  public function getUserInfo(Request $request)
  {
    //拿到token查询该用户的信息
    $time = time();
    if($request -> token && $time - $request -> times < 10){
      $userInfo = DB::table('homeuser') -> where('token','=',$request -> token) -> first();
      //$data['type'] = $userInfo -> type;   //身份类型  0学生  1非学生
      $data['username'] = $userInfo -> username;
      $data['idcard'] = $userInfo -> idcard;
      $data['phone'] = $userInfo -> phone;
      $data['sex'] = $userInfo -> sex;  //0女   1男
      $data['outdate'] = $userInfo -> outdate;  //出生年月
      $data['height'] = $userInfo -> height;   //身高
      $data['width'] = $userInfo -> width;  // 体重
      $data['school'] = $userInfo -> school;  //所在学校
      $data['in_school_date'] = $userInfo -> in_school_date; //入学时间
      $data['spec'] = $userInfo -> spec;  //专业
      $data['school_number'] = $userInfo -> school_number; // 学号
      $data['like_work_type'] = $userInfo -> like_work_type;  //期望工作类型
      // DB::table('types') -> where('id','=',$userInfo -> like_work_type) -> value('type');  //  期望工作类型   types表
      $data['like_work_address'] = $userInfo -> like_work_address;  //期望工作区域
      $data['like_work'] = $userInfo -> like_work;   //  工作意向
      $data['myshow'] = $userInfo -> myshow;   //自我介绍标签
      $data['syn'] = $userInfo -> syn;   // 自我介绍文字
      $data['workdetail'] = $userInfo -> workdetail;  //  工作经验
      return ['msg'=>'ok','code'=>'0','result'=> $data];
    }else{
      return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
    }
  }
  //用户编辑的表单信息的提交
  public function jianliup(Request $request)
  {
  	$time = time();
	  	if($time - $request -> times < 10 && $request -> token){
        //用token查询出用户的openid用来发送模板消息
        $openid = DB::table('homeuser') -> where('token',$request -> token) -> first();

        $is_muban = false;
        if(!$openid -> username || !$openid -> idcard){   //如果用户和姓名其中一个为空的话就认为是第一次完善简历，就发送模板消息
            $is_muban = true;
        }
		  		if(!$request -> myShow_data && !$request -> biaoqian_data){
		  			return ['msg'=>'err','code'=>'2','result'=>'参数不全'];
		  		}
			  		//拿到表单信息
			  		$userInfo = json_decode($request -> info,true);
			  		if(in_array('',$userInfo)){
			  			//如果为真就存在空值，报错
			  			return ['msg'=>'err','code'=>'2','result'=>'参数不全'];
			  		}else{
              //验证身份证账号姓名是否正确
              $name = $userInfo['username'];   //真实姓名
              $idcard = $userInfo['idcard'];   //身份证号码

              $res = file_get_contents("http://op.juhe.cn/idcard/query?key=99b7d4903977231daa777402742c6946&idcard=".$idcard."&realname=".$name."");
              $result = json_decode($res,true);
              if($result['error_code'] == '210304'){   //身份证或者姓名错误
                return ['msg'=>'err','code'=>'10','result'=>'身份证或姓名格式错误'];
              }

              if($result['result']['res'] == '2'){    //  res为1 匹配   2 不匹配   
                //不匹配
                return ['msg'=>'err','code'=>'9','result'=>'身份信息不匹配'];
              }
			  			//就写入数据库
              $is_like_work = DB::table('homeuser') -> where('token',$request -> token) -> first();
                //工作类型
                if($request -> myShow_data == $is_like_work -> like_work){
                  //说明工作意向没有更改
                }else{
                  //否则更改
                  $new_like_work = json_decode($request -> myShow_data,true);
                  //循环数组拿出name值进行拼接
                  for ($i=0; $i < count($new_like_work) ; $i++) { 
                    $data[$i] = $new_like_work[$i]['name'];
                  }
                  $userInfo['like_work'] = implode(',',$data) . ',';
                }

                //个人介绍
                if($request -> biaoqian_data == $is_like_work -> myshow){
                  //说明没有更改
                }else{
                   $new_my_show = json_decode($request -> biaoqian_data,true);
                   for ($i=0; $i < count($new_my_show) ; $i++) { 
                      $show[$i] = $new_my_show[$i]['name'];
                    }
                    $userInfo['myshow'] = implode(',',$show) . ',';
                }

                try {
                    $userid = DB::table('homeuser')->where('token',$request->token) -> value('id');
                    $a = $userInfo;
                    $a['user_id'] = $userid;
                    $a['update_time'] = time();
                    $record = DB::table('resume_record') -> where('user_id',$userid) -> groupBy('id') -> get();//查询简历修改记录
                    $count = count($record);
                    if($count != 0){
                        $last_time = $record[$count-1]-> update_time;
                    }else{
                        $last_time = 0;
                    }


                    if(($time - $last_time) > 604800){
                        //上次修改时间据这次有一周
                        if($count < 5){
                            $re = DB::table('resume_record') -> insert($a);
                            $res = DB::table('homeuser')->where('token','=',$request -> token) -> update($userInfo);
                        }else{
                            $delete = DB::table('resume_record') -> where('id',$record[0]->id) -> delete();
                            $re = DB::table('resume_record') -> insert($a);
                            $res = DB::table('homeuser')->where('token','=',$request -> token) -> update($userInfo);
                        }
                    }else{
                        //上次修改时间据这次没过一周
                        if($count < 5){
                            $re = '';
                            $res = DB::table('homeuser')->where('token','=',$request -> token) -> update($userInfo);
                            if($res){
                                $re = DB::table('resume_record') -> insert($a);
                            }
                        }else{
                            return ['msg'=>'err','code'=>'5','result'=>'近期修改次数过多!'];
                        }
                    }

                    if($re && $res){
                      if($is_muban){
                        //发送模板消息
                        $username = $name;
                        //$formid = $request -> formid;
                        $temid = 'Z98ZaPZgWSSTxj0dbrCMJf-VTPHQGqJv2VeKMZ-ekmA';
                        $page = '/pages/index/index';
                        $openid = $openid -> openid;
                        $key1 = date('Y-m-d',time());//发送的消息   时间
                        $key2 = '注册成功';
                        $key3 = $userInfo['phone'];  //电话
                        $key4 = $username;  //姓名
                        $access_token = AboutController::returnAssKey();
                        $all = json_decode($request->all,true);
                        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
                        $data = array(//这里一定要按照微信给的格式
                            "touser"=>$openid,
                            "template_id"=>$temid,
                            "page"=>$page,
                            "form_id"=>$all['formId'],
                            "data"=>array(
                                "keyword1"=>array(
                                    "value"=>$key4,
                                    "color"=>"#173177"
                                ),
                                "keyword2"=>array(
                                    "value"=>$key2,
                                    "color"=>"#173177"
                                ),
                                "keyword3"=>array(
                                    "value"=>$key3,
                                    "color"=>"#173177"
                                ),
                                "keyword4"=>array(
                                    "value"=>$key1,
                                    "color"=>"#173177"
                                )
                            ),
                            "emphasis_keyword"=>"keyword1.DATA",//需要进行加大的消息
                        );

                          $res = AboutController::postCurl($url,$data,'json');//将data数组转换为json数据
                          if($res){
                             return ['msg'=>'ok','code'=>'0','result'=> '修改成功!','temp' => $res];
                          }else{
                              echo json_encode(array('state'=>5,'msg'=>$res));
                          }
                      }
			  			return ['msg'=>'ok','code'=>'0','result'=> '修改成功'];
				  			}else{
                  return ['msg'=>'ok','code'=>'3','result'=> '未修改'];
                }
			  			} catch (Exception $e) {
			  				return ['msg'=>'err','code'=>'1','result'=>'用户不存在'];
			  			}

			  		}
	  	}else{
	  		return ['msg'=>'err','code'=>'1','result'=>'非法请求'];
	  	}
  }
    //获取当前用户的电话号码
      public function getPhone(Request $request)
      {
          $phone = DB::table('homeuser') -> where('nicename','=',$request->nickname) -> value('phone');
          if($phone){
              return ['msg' => 'ok','code' => 0,'result' => $phone];
          }else{
              return ['msg' => 'ok','code' => 0,'result' => $phone];
          }
      }

  //获取简历里面的所有标签模板
  public function getBiaoQian(Request $request)
  {
    $times = time();
    if($times - $request -> times < 10){
      //获取分类标签
    }
  }

  //获取二维码测试
    public function buffer()
    {
        $a = '69';
        $access_token = AboutController::returnAsskey();
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;
        $data = [
            'scene' => $a,
            'page' => 'pages/worklist/worklist',
            'auto_color' => false,
            'line_color' => ['r'=>224,'g'=>100,'b'=>0]
        ];
        $buffer = AboutController::postCurl($url,$data,'json');
        $path = uniqid() . '.png';
        file_put_contents($path,$buffer);
        return 'https://www.xiaoshetong.cn/' . $path;
    }

//获取access_key
public function returnAsskey()
{
    $ass_key = Redis::get('wxasskey');
    $ass_key = json_decode($ass_key);
    if(!$ass_key || time() - $ass_key->time > 7150){
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wxa32f710ad7d22b94&secret=656c551e33e60b4d351d1cafff77aade';
        $ass_key = AboutController::curl_get($url);
        $ass_key = collect($ass_key);
        $ass_key = $ass_key->prepend(time(),'time');
        Redis::set('wxasskey',$ass_key);
        $ass_key = Redis::get('wxasskey');
        $ass_key = json_decode($ass_key);
    }
    $a1 = $ass_key->access_token;
    return $a1;
}
 //post消息的方法
public function postCurl($url,$data,$type)
{
    if($type == 'json'){
        $data = json_encode($data,JSON_UNESCAPED_UNICODE);//对数组进行json编码
        $header= array("Content-type: application/json;charset=UTF-8","Accept: application/json","Cache-Control: no-cache", "Pragma: no-cache");
    }
    $curl = curl_init();
    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_POST,1);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);
    if(!empty($data)){
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
    //curl_setopt($curl,CURLOPT_ENCODING,"");
    $res = curl_exec($curl);

    if(curl_errno($curl)){
        echo 'Error+'.curl_error($curl);
    }
    curl_close($curl);
    return $res;
}
//get消息的方法
public function curl_get($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $data = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    return json_decode($data);//对数据进行json解码
}
  
  
}
