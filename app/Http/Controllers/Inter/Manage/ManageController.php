<?php


namespace App\Http\Controllers\Inter\Manage;


use App\Facades\FromId;
use App\Facades\ReturnJson;
use App\Facades\SendSms;
use App\Http\Controllers\Controller;
use App\Model\Collection;
use App\Model\Feedback;
use App\Model\Recruiters;
use App\Model\UserWork;
use App\Model\Workers;
use App\Model\Works;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use BaseFile;
use Illuminate\Support\Facades\Redis;

class ManageController extends Controller
{
    /**
     * 工人端小程序的appid
     * @var string
     */
    protected $appid = 'wxa32f710ad7d22b94';

    /**
     * 工人端小程序的secret
     * @var string
     */
    protected $secret = '656c551e33e60b4d351d1cafff77aade';

    /**
     * 判断用户是否是第一次进入
     * @param Request $request
     * $request->code  小程序前台获取到的code
     * @return array
     */
    public function isFirst(Request $request)
    {
        $error = ReturnJson::parameter(['code'],$request);
        if($error) return $error;

        //调取微信接口，用appid，secret，code换取openID和session_key
        $api    = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->appid . '&secret=' . $this->secret . '&js_code=' . $request->code . '&grant_type=authorization_code';
        $json = file_get_contents($api);            //使用file_get_content()函数获取$api的内容
        $json = json_decode($json,true);
        $isFirst = Workers::where('openid',$json['openid']) -> first();//查询数据库中是否有这个openID
        if($isFirst){
            //存在返回token
            $token = Hash::make($json['openid']);
            Redis::set('job_' . $isFirst->id,$token);//token存入redis，key为rec_加上用户ID
            return ['msg'=>'ok','code'=>0,'result'=>['token'=>$token,'id'=>$isFirst->id,'openid'=>$json['openid']]];
        }else{
            return ['msg'=>'ok','code'=>1,'result'=>['openid'=>$json['openid'],'session_key'=>$json['session_key']]];
        }
    }

    /**
     * 注册用户
     * @param Request $request
     *
     * @return array
     */
    public function register(Request $request)
    {
        $error = ReturnJson::parameter(['username','phone','header','openid','idcard'],$request);
        if($error) return $error;

        //验证身份证信息是否属实
        $auth = SendSms::authentication($request->idcard, $request->username);
        if($auth) return $auth;

        $data['username']     = $request -> username;   //用户名
        $data['phone']        = $request -> phone;      //手机号
        $data['header']       = $request -> header;     //头像
        $data['openid']       = $request -> openid;     //openID
        $data['idcard']       = $request -> idcard;     //身份证号
        $data['bank']         = $request -> bank;       //开户行
        $data['bank_number']  = $request -> bank_number;//银行卡号
        $data['sex']          = $request -> sex;        //性别
        $data['weight']       = $request -> weight;     //体重
        $data['height']       = $request -> height;     //身高
        $data['hometown']     = $request -> hometown;   //家乡
        $data['education']    = $request -> education;  //学历
        $data['birthday']     = $request -> birthday;   //生日
        $data['residence']    = $request -> residence;  //现住地址
        $data['created_at']   = date('Y-m-d H:m:i',time());
        $token = Hash::make($request -> openid);
        $redis = Redis::connection('census');
        $redis->decr('registered');
        $res = Workers::insertGetId($data);
        if($res){
            if(Redis::set('job_' . $res,$token)) return ['msg'=>'ok','code'=>0,'result'=>['token'=>$token,'id'=>$res,'username'=>$data['username'],'header'=>$data['header']]];
        }else{
            return ['msg'=>'err','code'=>1,'result'=>'添加失败'];
        }
    }

    public function edit(Request $request)
    {
        $error = ReturnJson::parameter(['id','username','phone','header','openid','idcard'],$request);
        if($error) return $error;

        //验证身份证信息是否属实
        $auth = SendSms::authentication($request->idcard, $request->username);
        if($auth) return $auth;

        $data['username']     = $request -> username;   //用户名
        $data['phone']        = $request -> phone;      //手机号
        $data['header']       = $request -> header;     //头像
        $data['openid']       = $request -> openid;     //openID
        $data['idcard']       = $request -> idcard;     //身份证号
        $data['bank']         = $request -> bank;       //开户行
        $data['bank_number']  = $request -> bank_number;//银行卡号
        $data['sex']          = $request -> sex;        //性别
        $data['weight']       = $request -> weight;     //体重
        $data['height']       = $request -> height;     //身高
        $data['hometown']     = $request -> hometown;   //家乡
        $data['education']    = $request -> education;  //学历
        $data['birthday']     = $request -> birthday;   //生日
        $data['created_at']   = date('Y-m-d H:m:i',time());
        $res = Workers::where('id',$request -> id) -> update($data);
        if($res){
            return ['msg'=>'ok','code'=>0,'result'=>'更新成功'];
        }else{
            return ['msg'=>'err','code'=>1,'result'=>'更新失败'];
        }
    }

    /**
     * 获取工作信息
     * @param Request $request
     * $request->work_id        工作ID
     * @return array
     */
    public function workDetail(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $detail = Works::with(['workImage:work_image,work_id','describe:content,work_id','recruiters:id,header,username,phone,is_company']) -> where('id',$request->workid) -> get();
        //DB::table('works') ->
        $isCollection = Collection::where('work_id',$request->workid)
            -> where('worker_id',$request->id)
            -> where('status',0)
            -> first();
        $isCollection = count($isCollection) == 0 ? 0 : 1;
        if($detail) return ['msg'=>'ok','code'=>0,'result'=>['work'=>$detail,'is_collection'=>$isCollection]];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 获取个人详细信息
     * @param Request $request
     * $request->id     工人ID
     * @return array
     */
    public function getMyDetail(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $detail = Workers::where('id',$request->id) -> first() -> toArray();
        $n_year = date('Y');
        $n_month= date('m');
        $n_day  = date('d');
        $year   = date('Y',strtotime($detail['birthday']));
        $month  = date('m',strtotime($detail['birthday']));
        $day    = date('d',strtotime($detail['birthday']));
        if($n_month >= $month && $n_day >= $day){
            $detail['age'] = $n_year - $year + 1;
        }else{
            $detail['age'] = $n_year - $year;
        }
        $detail['hometown']= str_replace(',','',$detail['hometown']);
        if($detail) return ['msg'=>'ok','code'=>0,'result'=>$detail];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 获取工作记录
     * @param Request $request
     * $request->id  工人ID
     * @return array
     */
    public function workRecord(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $data = UserWork::with(['works'=>function($query){
            $query -> with('recruiters:id,username,header') -> select('id','title','recruiter_id','address') -> get();
        }]) -> where('worker_id',$request->id) -> get();
        if($data) return ['msg'=>'ok','code'=>0,'result'=>$data];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 获取待面试工作
     * @param Request $request
     * @return array
     */
    public function getMyToBeAuditedWork(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $data = UserWork::with(['works'=>function($query){
            $query -> with('recruiters:id,username,header') -> select('id','title','recruiter_id','address','header','welfare','cycle','wages') -> get();
        }]) -> where('worker_id',$request->id) -> where('status',0) -> get();

        if($data) return ['msg'=>'ok','code'=>0,'result'=>$data];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 反馈接口
     * @param Request $request
     * $request->content    反馈内容
     * $request->worker_id  反馈者ID
     * @return array
     */
    public function feedback(Request $request)
    {
        $error = ReturnJson::parameter(['content','workerid'],$request);
        if($error) return $error;

        $content = 'content';
        $data['content']    = $request -> $content;
        $data['worker_id']  = $request -> workerid;
        $data['created_at'] = date('Y-m-d H:i:s',time());
        $res = Feedback::insert($data);     //insert方法不触发ORM自动添加created_at
        if($res) return ['msg'=>'ok','code'=>0,'result'=>'反馈成功！'];
        return ['msg'=>'err','code'=>1,'result'=>'反馈失败！'];
    }

    /**
     * 将工人与对应工作绑定
     * @param Request $request
     * $request->worker_id     工人ID
     * $request->work_id       工作ID
     * @return array
     */
    public function bindJob(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $data['worker_id'] = $request -> workerid;                  //员工ID
        $data['work_id']   = $request -> workid;                    //工作ID
        $data['status']    = 1;                                     //直接添加为工作中
        $data['created_at']= date('Y-m-d',time());

        //判断员工是否已添加该工作
        $has = UserWork::where('worker_id',$request -> workerid) -> where('work_id',$request -> workid) -> first();
        if(isset($has -> status) && $has -> status  == 3){       //判断用户是否是再次加入工作（已离职再次申请）
            $res = UserWork::where('worker_id',$request -> workerid) -> where('work_id',$request -> workid) -> update(['status'=>1]);
            if($res) return ReturnJson::json('ok',0,'添加成功！');

        }else{ if($has)  return ReturnJson::json('err',3,'你已添加该工作！');}   //否者判断用户是否已添加该工作

        $res = UserWork::insert($data);
        if($res) return ['msg'=>'ok','code'=>0,'result'=>'绑定成功！'];
        return ['msg'=>'err','code'=>1,'result'=>'绑定失败！'];
    }

    /**
     * 用户报名接口
     * @param Request $request
     * $request->workerid       员工ID
     * $request->workid         工作ID
     * @return array
     */
    public function joinWork(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $data['worker_id'] = $request -> workerid;                  //员工ID
        $data['work_id']   = $request -> workid;                    //工作ID
        $data['status']    = 4;
        $data['created_at']= date('Y-m-d',time());

        go(function () use($data){
            \co::sleep(1);

            $res = Works::with('recruiters:id,openid')
                -> where('id',$data['work_id'])
                -> select('id','recruiter_id','title','recruitment')
                -> first()
                -> toArray();
            $name = Workers::where('id',$data['worker_id']) -> select('username') -> first();
            $openid = $res['recruiters']['openid'];
            $result = [
                "touser"        => $openid,
                "template_id"   => 'oOidHLRgieNqBYvpZ324uSoF9z_zb0YIdGXFyXHRYGs',
                "page"          => '',
                "data"          => [
                    "keyword1"      => ["value" => $res['title']],
                    "keyword2"      => ["value" => date('Y-m-d H:m:i',time())],
                    "keyword3"      => ["value" => $name->username]
                ]
                //"emphasis_keyword" => ''
            ];
            $msg = FromId::sendRecruitFromid($result,$res['recruiters']['id']);
            Works::where('id',$data['work_id']) -> update(['recruitment' => $res['recruitment'] + 1]);
        });

        $res = UserWork::insert($data);
        if($res) return ['msg'=>'ok','code'=>0,'result'=>'报名成功！'];
        return ['msg'=>'err','code'=>1,'result'=>'报名失败！'];
    }

    /**
     * 查询用户的报名状态
     * @param Request $request
     * $request->workerid       员工ID
     * $request->workid         工作ID
     * @return mixed
     */
    public function isJoin(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $res = UserWork::where('worker_id',$request->workerid) -> where('work_id',$request->workid) -> first();    //查询用户报名状态
        if(!$res) return ReturnJson::json('ok',0,'点击报名');                  //无查询结果，用户未报名
        if($res->status == 0) return ReturnJson::json('ok',0,'待面试');        //status=0用户审核中
        if($res->status == 1) return ReturnJson::json('ok',0,'工作中');        //status=1用户工作中
        if($res->status == 2) return ReturnJson::json('ok',0,'已离职');        //status=2用户已离职
        if($res->status == 3) return ReturnJson::json('ok',0,'面试不通过');
        if($res->status == 4) return ReturnJson::json('ok',0,'已报名');
    }

    /**
     * 获取二维码接口
     * @param Request $request
     * $request->workid    工作ID
     * @return array
     */
    public function getCode(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $has = Works::where('id',$request->workid) -> value('image_path');                  //查询是否已存在二维码
        if($has){
            return ['msg'=>'ok','code'=>0,'result'=>$has];                                  //存在直接返回路径
        }else{                                                                              //不存在就调取接口
            //从工人端获取二维码的网络路径
            $src = ManageController::getQRCode($request->workid);
            $res = Works::where('id',$request->workid) -> update(['image_path'=>$src]);     //把二维码路径存入数据库
            if($res) return ['msg'=>'ok','code'=>0,'result'=>$src];
        }
        return ['msg'=>'err','code'=>1,'result'=>'获取失败'];
    }

    /**
     * 获取加入工作的二维码
     * @param $workId   工作ID
     * @return mixed    图片网络路径
     */
    public function getQRCode($workId)
    {
        //获取access_token,参数为（存入redis的键名，当前小程序的appid，当前小程序的secret）
        $access_token = SendSms::returnAsskey('C_wxasskey', $this->appid, $this->secret);
        $url = 'https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=' . $access_token;
        //按微信小程序(https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/qr-code/wxacode.getUnlimited.html)接口配置参数
        $data = [
            'scene'      => $workId,
            'page'       => 'pages/receiveQRcode/receiveQRcode',
            'auto_color' => false,
            'line_color' => ['r'=>224,'g'=>100,'b'=>0]
        ];
        $buffer = SendSms::postCurl($url,$data,'json');                                 //通过curl的post请求获取二维码的buffer
        $src    = BaseFile::processing(['content'=>$buffer,'contentType'=>'buffer']);   //将buffer存入文件，返回文件网络路径
        return $src;
    }

    /**
     * 获取用户发布过的工作
     * @param Request $request
     * @return mixed
     */
    public function getWorkByRecruiter(Request $request)
    {
        $error = ReturnJson::parameter(['recruiter_id'],$request);
        if($error) return $error;

        $select = ['id','title','header'];
        $res = Works::where('recruiter_id',$request->recruiter_id) -> where('status',0) -> select($select) -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！');
    }

    /**
     * 获取招聘者详情
     * @param Request $request
     * @return mixed
     */
    public function getRecruiterDetail(Request $request)
    {
        $error = ReturnJson::parameter(['recruiter_id'],$request);
        if($error) return $error;

        $res = Recruiters::where('id',$request->recruiter_id) -> select('id','username','header','sex') -> first();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 添加银行卡号及开户行
     * @param Request $request
     * @return mixed
     */
    public function addBank(Request $request)
    {
        $error = ReturnJson::parameter(['id','bank','bank_number','bank_user'],$request);
        if($error) return $error;

        $result = SendSms::bank($request->bank_number,$request->bank_user);
        $result = json_decode($result);
        if($result['error_code'] == 0){
            if($result['result']['res'] == 2) return ReturnJson::json('err',8,'银行卡信息不匹配');
        }else{
            return ReturnJson::json('err',1,'更改失败');
        }
        $res = Workers::where('id',$request -> id) -> update(['bank'=>$request->bank,'bank_number'=>$request->bank_number,'bank_user'=>$request->bank_user]);
        if($res) return ReturnJson::json('ok',0,'更改成功');
        return ReturnJson::json('err',1,'更改失败');
    }

    /**
     * 获取银行卡号及开户行
     * @param Request $request
     * @return mixed
     */
    public function getBank(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = Workers::where('id',$request->id) -> select('id','bank','bank_number','bank_user') -> first();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }
}