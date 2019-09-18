<?php


namespace App\Http\Controllers\ZhaoXian\WorkGeo;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Works;
use App\Model\WorksGeo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class WorkGeoController extends Controller
{
    public $redis;

    public function __construct()
    {
        $redis = Redis::connection('geo');
        $this -> redis = $redis;
    }

    /**
     * 获取成都市每个区的工作数
     * @return mixed
     */
    public function getCityNumber()
    {
        $city = $this -> redis -> zrange('chengdu',0,-1);
        $data = [];
        foreach($city as $value){
            $data[$value] = $this -> redis -> get($value);
        }
        return ReturnJson::json('ok',0,$data);
    }

    /**
     * 获取某街道的街道名、工作数、经纬度
     * @param Request $request
     * @return mixed
     */
    public function getStreetNumber(Request $request)
    {
        $redis = $this -> redis;
        $data = $redis -> georadius('chengdustreet',$request -> lng,$request -> lat,10,'km','WITHCOORD');
        $res = [];
        foreach ($data as $value){
            $key = ReturnJson::toPinyin($value[0]);
            $number = $redis -> get($key);
            $res[] = [
                'street'        => $value[0],
                'number'        => $number,
                'lng'           => $value[1][0],
                'lat'           => $value[1][1]
            ];
        }
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'稍后再试');
    }

    /**
     * 获取该街道的工作列表
     * @param Request $request
     * @return mixed
     */
    public function getStreetWork(Request $request)
    {
        $error = ReturnJson::parameter(['street'],$request);
        if($error) return $error;

        //获取该街道的工作ID
        $result = WorksGeo::where('street_pin',$request -> street) -> where('status',0) -> select('work_id') -> get() -> toArray();
        //把所有的工作ID取出来
        $result = array_column($result,'work_id');
        //通过工作ID获取工作详情
        $data = Works::whereIn('id',$result) -> get();
        if($data) return ReturnJson::json('ok',0,$data);
        return ReturnJson::json('err',1,'稍后再试');
    }


}