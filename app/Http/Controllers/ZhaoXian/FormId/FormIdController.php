<?php


namespace App\Http\Controllers\ZhaoXian\FormId;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FormIdController extends Controller
{
    /**
     * 获取微信小程序的formid，存如redis
     * @param Request $request
     * @return mixed
     */
    public function setFormID(Request $request)
    {
        $error = ReturnJson::parameter(['formId','is_rec','id'],$request);
        if($error) return $error;

        $offset = $request->is_rec == 0 ? 'C' : 'B';
        $redis = Redis::connection('formId');
        //存入redis zset b端为formIdB c端为formIdC 存入时的时间戳为分数，formid为值
        $res = $redis -> zadd($offset . $request->id, time(), $request->formId);
        if($res) return ReturnJson::json('ok',0,'成功');
        return ReturnJson::json('err',1,'失败');
    }

    /**
     * 获取formid
     * @param $request
     * @return bool|string
     */
    public function getFormID(Request $request)
    {
        $offset = $request->is_rec == 0 ? 'C' : 'B';
        $redis = Redis::connection('formId');
        $time = time();
        $redis -> zremrangebyscore($offset . $request->id,0,$time - 596160);
        $res = $redis -> zrange($offset . $request->id,0,0);
        $redis -> zremrangebyrank($offset . $request->id,0,0);
        if($res) return $res[0];
        return false;
    }
}