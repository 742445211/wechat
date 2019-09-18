<?php


namespace App\Sms;



class ReturnJson
{
    /**
     * 判断参数是否存在
     * @param array $data   所需要的参数
     * @param $request      前台请求数据集合
     * @return array|bool   存在返回false，不存在返回错误
     */
    public function parameter(array $data, $request)
    {
        foreach ($data as $v) {
            if($request->$v === 0){
                //为0时通过
            }else{
                if(!isset($request->$v) || $request->$v == null) return ['mag'=>'err','code'=>2,'result'=>'参数错误！缺少参数'.$v];
            }
        }
        return false;
    }

    /**
     * 接口返回数据拼接
     * @param $msg      信息
     * @param $code     返回码
     * @param $result   返回数据
     * @return array
     */
    public function json($msg, $code, $result)
    {
        $result = is_object($result) ? $result -> toArray() : $result;
        $result = ReturnJson::nullToEmpty($result);
        return ['msg'=>$msg, 'code'=>$code, 'result'=>$result];
    }

    /**
     * 把数组中的null转为Empty string
     * @param $result
     * @return array|string
     */
    public function nullToEmpty($result)
    {
        if($result !== null){
            if(is_array($result)){
                if(!empty($result)){
                    foreach($result as $key => $value){
                        if($value === null){
                            $result[$key] = '';
                        }else{
                            $result[$key] = ReturnJson::nullToEmpty($value);      //递归再去执行
                        }
                    }
                }
            }else{
                if($result === null){ $result = ''; }         //注意三个等号
            }
        }else{ $result = ''; }
        return $result;
    }

    /**
     * 省市区转拼音
     * @param $address
     * @return mixed
     */
    public function toPinyin($address)
    {
        $address = app('pinyin') -> sentence($address);
        $address = str_replace(' ','',$address);
        return $address;
    }
}