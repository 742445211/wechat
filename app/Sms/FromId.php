<?php


namespace App\Sms;


use App\RecruitFromid;
use App\UserFromid;
use Illuminate\Support\Facades\Redis;

class FromId
{
    /**
     * 存recruit_fromid
     * @param $fromid
     */
    public function recruitSave($fromid)
    {
        $data['recruit_fromid'] = $fromid;//fromid
        $data['created_at'] = time();//添加时间
        RecruitFromid::create($data);
    }

    /**
     * 存user_fromid
     * @param $fromid
     */
    public function userSave($fromid)
    {
        $data['user_fromid'] = $fromid;
        $data['created_at'] = time();
        UserFromid::create($data);
    }

    /**
     * 发送企业端的消息通知
     * @param $data   传入格式必须和微信上的格式一致
     *$data = array(//这里一定要按照微信给的格式
     *"touser"=>$openid,  //接受者的openid
     *"template_id"=>$temid,  //模板ID
     *"page"=>$page,  //跳转页面的路径
     *"form_id"=>$all['formId'],  //fromID
     *"data"=>array(
     *"keyword1"=>array(
     *  "value"=>$key4,
     *  "color"=>"#173177"
     *),
     *"keyword2"=>array(
     *  "value"=>$key2,
     *  "color"=>"#173177"
     *),
     *"keyword3"=>array(
     *  "value"=>$key3,
     *  "color"=>"#173177"
     *),
     *"keyword4"=>array(
     *  "value"=>$key1,
     *  "color"=>"#173177"
     *)
     *),
     *"emphasis_keyword"=>"keyword1.DATA",//需要进行加大的消息
     *);
     * @return array
     */
    public function sendRecruitFromid($data, $id)
    {
        $appid = 'wx83c8456fe41fba10';//小程序的appid
        $secret = 'c8744902ddcc55dae919fd453e3ab954';//小程序的secret
        $formid = FromId::getFormID(1,$id);
        $data['form_id'] = $formid;
        $access_token = FromId::returnAssKey($appid, $secret);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;

        $res = FromId::postCurl($url,$data,'json');//将data数组转换为json数据
        return $res;
    }

    /**
     * 发送用户端的消息通知
     * @param $data   传入格式必须和微信上的格式一致
     * @return array
     */
    public function sendInterFormid($data, $id)
    {
        $appid = 'wxa32f710ad7d22b94';
        $secret = '656c551e33e60b4d351d1cafff77aade';
        $formid = FromId::getFormID(0,$id);
        $data['form_id'] = $formid;
        $access_token = FromId::returnAssKey($appid, $secret);
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;

        $res = FromId::postCurl($url,$data,'json');//将data数组转换为json数据
        return $res;
    }

    /**
     * 获取formid
     * @param $is_rec
     * @param $id
     * @return bool|string
     */
    public function getFormID($is_rec, $id)
    {
        $offset = $is_rec == 0 ? 'C' : 'B';
        $redis = Redis::connection('formId');
        $time = time();
        $redis -> zremrangebyscore($offset . $id,0,$time - 596160);
        $res = $redis -> zrange($offset . $id,0,0);
        $redis -> zremrangebyrank($offset . $id,0,0);
        if($res) return $res[0];
        return false;
    }

    public function sendTemplateMessage()
    {

    }

    //获取access_key
    public function returnAsskey($appid, $secret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret='.$secret;
        $ass_key = FromId::curl_get($url);
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