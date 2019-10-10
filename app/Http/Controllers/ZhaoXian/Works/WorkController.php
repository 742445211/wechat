<?php


namespace App\Http\Controllers\ZhaoXian\Works;


use App\Facades\BaseFile;
use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Describe;
use App\Model\Works;
use App\Model\WorksGeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class WorkController extends Controller
{
    /**
     * 下架工作
     * @param Request $request
     * $request->workid             工作ID
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['workid','id'],$request);
        if($error) return $error;

        $res = Works::where('id',$request->workid) -> where('recruiter_id',$request -> id) -> update(['status'=>1]);

        if($res){
            go(function () use($request){
                \co::sleep(0.25);
                $street = WorksGeo::where('work_id',$request -> workid) -> select('street_pin','district') -> first();
                $redis = Redis::connection('geo');
                $redis -> decr($street -> street_pin);
                $redis -> decr($street -> district);
            });
            go(function (){
                \co::sleep(0.25);
                $redis = Redis::connection('census');
                $redis -> incr('atwork');
                $redis -> decr('recruitment');
            });
        }
        if($res) return ReturnJson::json('ok',0,'下架成功！');
        return ReturnJson::json('err',1,'下架失败！');
    }

    /**
     * 修改工作
     * @param Request $request
     *  $request->workid             工作ID
     *  $request -> title,        //工作标题
        $request -> address,      //工作地址
        $deduct,                  //应扣除时间
        $request -> cycle,        //结算周期
        $request -> wages * 100,  //工资*100
        $request -> describe,     //工作描述
        $request -> id,           //关联发布者ID
        $request -> validity_time,//招聘有效期
        $request -> experience,   //经验要求
        $request -> education,    //学历要求
        $request -> age,          //年龄要求
        $request -> sex,          //性别要求
        $request -> welfare,      //工作福利
        $request -> longitude,    //经度
        $request -> latitude,     //纬度
        $request -> intro,        //公司信息
        $request -> type,         //职位类型（小标签）
        $request -> cate,         //工作分类
     * @return mixed
     */
    public function edit(Request $request )
    {
        $error = ReturnJson::parameter(['workid','title','address','cycle','wages','validity_time','experience','longitude','latitude','cate'],$request);
        if($error) return $error;

        $deduct = isset($request->deduct) ? $request->deduct : 0;
        $data = [
            'title'        => $request -> title,        //工作标题
            'header'       => $request -> header,       //工作头像
            //'logo'         => $request -> logo,         //用人公司的logo
            'address'      => $request -> address,      //工作地址
            'deduct'       => $deduct,                  //应扣除时间
            'cycle'        => $request -> cycle,        //结算周期
            'wages'        => $request -> wages,        //工资*100
            //'recruiter_id' => $request -> recruiter_id, //关联发布者ID
            'validity_time'=> $request -> validity_time,//招聘有效期
            'experience'   => $request -> experience,   //经验要求
            'education'    => $request -> education,    //学历要求
            'age'          => $request -> age,          //年龄要求
            'sex'          => $request -> sex,          //性别要求
            'welfare'      => $request -> welfare,      //工作福利
            'longitude'    => $request -> longitude,    //经度
            'latitude'     => $request -> latitude,     //纬度
            'intro'        => $request -> intro,        //公司信息
            'type'         => $request -> type,         //职位类型（小标签）
            'cate'         => $request -> cate,         //工作分类
            'number'       => $request -> number,
            'updated_at'   => date('Y-m-d H:i:s',time()),//记录修改时间
        ];
        $res = Works::where('id',$request->workid) -> update($data);
        if($res) return ReturnJson::json('ok',0,'修改成功！');
        return ReturnJson::json('err',1,'修改失败！');
    }

    /**
     * 修改工作详情
     * @param Request $request
     * @return mixed
     */
    public function editDescribe(Request $request)
    {
        $error = ReturnJson::parameter(['describe','workid'],$request);
        if($error) return $error;

        $res = Describe::where('work_id',$request->workid) -> update(['content' => $request->describe]);
        if($res) return ReturnJson::json('ok',0,'更新成功');
        return ReturnJson::json('err',1,'更新失败');
    }

    /**
     * 获取工作列表,对工作标题进行模糊搜索
     * @param Request $request
     * $request->keyword        关键词
     * @return mixed
     */
    public function get(Request $request)
    {
        //$distance = isset($request -> distance) ? $request -> distance :
        $filed = ['works.id','works.title','works.header as image','works.type','works.wages','works.cycle','works.recruiter_id','works.welfare','works.cycle','works.address','recruiters.username','recruiters.header'];       //规定要搜索的字段
        $work = DB::table('works')
            -> join('recruiters','works.recruiter_id','=','recruiters.id')
            ->select($filed);
        $keyword = $request->keyword;
        $cate = $request->cate;

        //判断是否有分类
        $cate && $work          -> where('works.cate',$cate);
        //根据title模糊搜索
        $keyword && $work       -> Where('works.title','like','%' . $keyword . '%');
        //根据address进行模糊搜索
        $keyword && $work       -> Where('works.address','like','%' . $keyword . '%');

        go(function () use ($keyword){
            \co::sleep(1);
            if($keyword != ''){
                $redis = Redis::connection('keyword');
                $redis->zincrby('keyword',1,$keyword);
            }
        });

        //查询上架中的工作，根据发布时间排序，查询5条
        $res = $work -> where('works.status',0)
            ->orderBy('works.created_at','desc')
            ->paginate(5);
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::josn('err',1,'获取失败！');
    }

    /**
     * 获取热门关键词
     * @return mixed
     */
    public function getKeyword()
    {
        $redis = Redis::connection('keyword');
        $data = $redis->zrevrangebyscore('keyword','+inf','-inf');
        if($data){
            $keyword = array_slice($data,0,8);
            return ReturnJson::json('ok',0,$keyword);
        }
        return ReturnJson::json('err',1,[]);
    }

    /**
     * 更换工作头像
     * @param Request $request
     * $request->workid             工作ID
     * @return mixed
     */
    public function changeHeader(Request $request)
    {
        //将文件存入服务器，返回网络路径
        $header = BaseFile::processing(['contentType'=>'file','content'=>$request -> file('header')]);

        if($header) return ReturnJson::json('ok',0,$header);
        return ReturnJson::json('err',1,'上传失败！请稍后重试');
        //查询当前工作的头像
        //$old = Works::where('id',$request->workid) ->select('header') ->first() ->toArray();
        //如果头像不为空，就删除头像文件并更新头像路径
        //if($old['header'] != null){
        //去掉路径最前面的网址
            //$old = substr($old['header'],26);
        //通过绝对路径删除文件
            //$result = unlink(base_path('public').$old);
        //删除成功后把头像地址更新进数据库
            //if($result){
//                $res = Works::where('id',$request->workid) ->update(['header'=>$header]);
//                if($res) return ReturnJson::json('ok',0,'头像更新成功！');
            //}
        //}
        //return ReturnJson::json('err',1,'头像更新失败！');
    }

    /**
     * 上穿头像
     * @param Request $request
     * $request -> file('logo')         logo文件
     * @return mixed
     */
    public function uploadLogo(Request $request)
    {
        //将文件存入服务器，返回网络路径
        $logo = BaseFile::processing(['contentType'=>'logo','content'=>$request -> file('logo')]);

        if($logo) return ReturnJson::json('ok',0,$logo);
        return ReturnJson::json('err',1,'上传失败！请稍后重试');
    }

    /**
     * 获取某个工作的详情
     * @param Request $request
     * @return mixed
     *
     */
    public function workDetail(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = Works::with(['describe','workImage:id,work_id,work_image'])
            -> where('id',$request->id) -> first();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 获取某B端用户的在招工作
     * @param Request $request
     * @return mixed
     */
    public function getRecruitWork(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = Works::where('recruiter_id',$request->id) -> where('status',0) -> select('id','title','header','type','cycle','wages','number','address','welfare') -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

}