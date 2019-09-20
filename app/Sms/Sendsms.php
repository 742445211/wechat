<?php


namespace App\Sms;



use Illuminate\Support\Facades\Redis;

/**
 * 发送短信及curl、获取微信access_token
 * Class Sendsms
 * @package App\Sms
 */
class Sendsms
{
    /**
     * c端小程序appid
     * @var string
     */
    private $c_appid = 'wxa32f710ad7d22b94';

    /**
     * c端小程序secret
     * @var string
     */
    private $c_secret = '656c551e33e60b4d351d1cafff77aade';

    /**
     * b端小程序appid
     * @var string
     */
    private $b_appid = 'wx83c8456fe41fba10';

    /**
     * b端小程序secret
     * @var string
     */
    private $b_secret = 'c8744902ddcc55dae919fd453e3ab954';

    /**
     * 调用发送短信接口
     * @param $phone
     * @param $id   //模板id [112444:面试不通过;112443:面试通过;112442:面试通知]
     * @param string $value
     * @return mixed
     */
    public function send($phone, $id, $value='')
    {
        $url = "http://v.juhe.cn/sms/send";
        $params = array(
            'key'   => '6b56f0bfe1af609232227466899c4776',
            'mobile'    => $phone, //接受短信的用户手机号码
            'tpl_id'    => $id,
            'tpl_value' => $value //您设置的模板变量，根据实际情况修改
        );
        $paramstring = http_build_query($params);
        $content = $this->juheCurl($url, $paramstring);
        return json_decode($content, true);
    }

    /**
     * 验证银行卡信息
     * @param $bankcard
     * @param $realname
     * @return mixed
     */
    public function bank($bankcard, $realname)
    {
        $url = "http://v.juhe.cn/verifybankcard/query";
        $params = array(
            "bankcard" => $bankcard,//银行卡卡号
            "realname" => $realname,//姓名(需utf8编码的urlencode)
            "key" => '6b56f0bfe1af609232227466899c4776',//应用APPKEY(应用详细页查询)
        );
        $paramstring = http_build_query($params);
        $content = $this->juheCurl($url,$paramstring);
        $result = json_decode($content,true);
        return $result;
    }

    /**
     * 请求接口返回内容
     * @param  string $url [请求的URL地址]
     * @param  string $params [请求的参数]
     * @param  int $ipost [是否采用POST形式]
     * @return  string
     */
    public function juheCurl($url, $params = false, $ispost = 0)
    {
        $httpInfo = array();
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'JuheData');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                curl_setopt($ch, CURLOPT_URL, $url.'?'.$params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }
        $response = curl_exec($ch);
        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }

    /**
     * 调用聚合数据验证身份证接口
     * @param $idcard
     * $idcard          身份证号
     * $name            姓名
     * @param $name
     * @return array|bool
     */
    public function authentication($idcard, $name)
    {
        $res = file_get_contents("http://op.juhe.cn/idcard/query?key=99b7d4903977231daa777402742c6946&idcard=".$idcard."&realname=".$name."");
        $result = json_decode($res,true);
        if($result['error_code'] == '210304') return ['msg'=>'err','code'=>'10','result'=>'身份证或姓名格式错误'];

        if($result['result']['res'] == '2') return ['msg'=>'err','code'=>'9','result'=>'身份信息不匹配'];

        if($result['result']['res'] == '1') return false;       //为1时身份信息匹配成功
    }

    /**
     * 识别身份证正面照片上的身份证号
     * @param int $is_rec
     * @param string $type
     * @param $img_url
     * @param $id
     * @return bool
     */
    public function wxOcrIdCard($img_url, $id, $username, $is_rec=1, $type='photo')
    {
        //判断是c端请求还是b端请求
        $name = $is_rec == 0 ? 'C_wxasskey' : 'B_wxasskey';
        $appid = $is_rec == 0 ? $this->c_appid : $this->b_appid;
        $secret = $is_rec == 0 ? $this->c_secret : $this->b_secret;
        //获取相应客服端的微信asskey
        $asskey = Sendsms::returnAsskey($name,$appid,$secret);
        //微信OCRidCard识别接口
        $url = "https://api.weixin.qq.com/cv/ocr/idcard?type={$type}&img_url={$img_url}&access_token={$asskey}";
        $res = Sendsms::postCurl($url,'','json');
        $res = json_decode($res);
        if($res -> errcode != 0) return true;
        if($res->type == 'Front'){
            if($res->id != $id ){ //|| $res->name != $username
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 微信小程序接口识别营业执照
     * @param $img_url
     * @param $company
     * @param int $is_rec
     * @return bool
     */
    public function wxBusinessLicense($img_url, $company, $is_rec=1)
    {
        //判断是c端请求还是b端请求
        $name = $is_rec == 0 ? 'C_wxasskey' : 'B_wxasskey';
        $appid = $is_rec == 0 ? $this->c_appid : $this->b_appid;
        $secret = $is_rec == 0 ? $this->c_secret : $this->b_secret;
        //获取相应客服端的微信asskey
        $asskey = Sendsms::returnAsskey($name,$appid,$secret);
        //微信小程序营业执照OCR识别接口
        $url = "https://api.weixin.qq.com/cv/ocr/bizlicense?img_url={$img_url}&access_token={$asskey}";
        $res = Sendsms::postCurl($url,'','json');
        $res = json_decode($res);
        if($res->errcode == 0){
            if($res->enterprise_name == $company){
                return false;
            }
            return true;
        }
        return true;
    }

    /**
     * 获取微信Asskey
     * @param $name     redis键名(c端为：C_wxasskey；b端为B_wxasskey)
     * @param $appid    小程序的appid
     * @param $secret   小程序的secret
     * @return mixed
     */
    public function returnAsskey($name, $appid, $secret)
    {
        $ass_key = Redis::get($name);
        if(!$ass_key){
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret=' . $secret;
            $ass_key = SendSms::curl_get($url);
            $ass_key = collect($ass_key);
            $ass_key = $ass_key->prepend(time(),'time');
            Redis::setex($name,7150,$ass_key['access_token']);
            $ass_key = Redis::get($name);
        }
        return $ass_key;
    }

    /**
     * curlpost请求
     * @param $url
     * @param $data
     * @param $type
     * @return bool|string
     */
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

    /**
     * curlget请求
     * @param $url
     * @return mixed
     */
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