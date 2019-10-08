<?php


namespace App\Http\Controllers\Recruit\Manage;


use App\Facades\FromId;
use App\Facades\ReturnJson;
use App\Facades\SendSms;
use App\Model\Describe;
use App\Model\Interview;
use App\Model\WorksGeo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Controller;
use App\Model\Recruiters;
use App\Model\UserWork;
use App\Model\Workers;
use App\Model\Works;
use BaseFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class ManageController extends Controller
{
    /**
     * 招聘端小程序的appid
     * @var string
     */
    protected $appid = 'wx83c8456fe41fba10';

    /**
     * 招聘端小程序的secret
     * @var string
     */
    protected $secret = 'c8744902ddcc55dae919fd453e3ab954';

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
        $isFirst = Recruiters::where('openid',$json['openid']) -> first();//查询数据库中是否有这个openID
        if($isFirst){
            //存在返回token
            $token = Hash::make($json['openid']);
            Redis::set('rec_' . $isFirst->id,$token);//token存入redis，key为rec_加上用户ID
            return ReturnJson::json('ok',0,['token'=>$token,'id'=>$isFirst->id,'username'=>$isFirst->username]);
        }else{
            return ReturnJson::json('ok',1,$json['openid']);
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
        $error = ReturnJson::parameter(['username','idcard','header','openid','isCompany','phone'],$request);
        if($error) return $error;

        //验证身份证信息是否属实
//        $auth = SendSms::authentication($request->idcard, $request->username);
//        if($auth) return $auth;

        $data['username']   = $request -> username;  //用户名
        $data['idcard']     = $request -> idcard;    //身份证号
        $data['header']     = $request -> header;    //头像
        $data['openid']     = $request -> openid;    //openID
        $data['sex']        = $request -> sex;       //性别
        $data['phone']      = $request -> phone;     //电话号码
        //判断用户类型，0为个人，1为公司
        if($request->isCompany == 0){
            $error = ReturnJson::parameter(['position','back'],$request);
            if($error) return $error;

            $error = SendSms::wxOcrIdCard($request->position, $request->idcard,$request->username);
            if($error) return $error;
            $info = [
                //通过basefile把base64文件流转为图片，返回图片路径
                //身份正正面照
                'position'    => $request->position,
                //身份证反面照
                'back'        => $request->back,
                'is_company'  => $request -> isCompany,
            ];
        }elseif ($request->isCompany == 1){
            $error = ReturnJson::parameter(['company','license'],$request);
            if($error) return $error;

            $error = SendSms::wxBusinessLicense($request->license,$request->company);
            if($error) return $error;
            $info = [
                'company'     => $request -> company,                //公司名称
                //营业执照
                'license'     => $request->license,
                'is_company'  => $request -> isCompany,
            ];
        }
        $data['created_at'] = date('Y-m-d H:i:s',time());    //记录注册时间
        $data = array_merge($data,$info);
        $token = Hash::make($request -> openid);
        $res = Recruiters::insertGetId($data);
        if($res){
            $redis = Redis::connection('census');
            $redis -> incr('business');
            $redis -> incr('uncertified');
            if(Redis::setex('rec_' . $res,'216000',$token)) return ReturnJson::json('ok',0,['token'=>$token,'id'=>$res,'username'=>$request->username]);
        }else{
            return ReturnJson::json('err',1,'添加失败！');
        }
    }

    /**
     * 用户首页
     * @param Request $request
     * $request->id    $request->token
     * @return array
     */
    public function getMyWork(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $token = Redis::get('rec_' . $request -> id); //从redis中取到当前用户的token
        //判断token是否有效
        if($request -> token == $token){
            //通过用户的ID获取他发布的所有工作
            $data = Works::with('userWork:id,status,work_id')
                -> where('recruiter_id',$request->id)
                //-> where('status',0)
                -> select('id','title','header','type','cycle','wages','number','address','welfare','status')
                -> orderBy('id','desc')
                -> get()
                -> toArray();
            foreach ($data as $key => $value){
                $data[$key]['sign'] = count($value['user_work']);       //当前工作报名人数
                $data[$key]['audit_pass'] = 0;                          //当前工作审核通过人数
                $data[$key]['interview_pass'] = 0;                      //当前工作面试通过人数
                foreach ($value['user_work'] as $item){
                    if($item['status'] == 0){
                        $data[$key]['audit_pass'] += 1;
                    }

                    if($item['status'] == 1){
                        $data[$key]['interview_pass'] += 1;
                    }
                }
            }
            if($data) return ReturnJson::json('ok',0,$data);
            return ReturnJson::json('err',1,'获取失败！');
        }else{
            return ReturnJson::json('err',5,'登陆过期！');
        }
    }

    /**
     * 新增岗位
     * @param Request $request
     * $request->id   $request->token
     * @return array
     */
    public function addJob(Request $request)
    {
        $error = ReturnJson::parameter(['title','header','location','cycle','wages','describe','validity_time','experience','longitude','latitude','cate'],$request);
        if($error) return $error;

        $is_ok = Recruiters::where('id',$request->id) -> select('status') -> first();
        if($is_ok->status == 0) return ReturnJson::json('err',20,'用户信息审核中，请耐心等待');
        if($is_ok->status == 2) return ReturnJson::json('err',21,'审核未通过');

        $token = Redis::get('rec_' . $request -> id); //从redis中取到token
        $deduct = isset($request->deduct) ? $request->deduct : 0;
        //用户输入的地址
        $address = $request->location;
        //判断token是否有效
        if($request -> token == $token){
            $data = [
                'title'        => $request -> title,        //工作标题
                'header'       => $request -> header,       //工作头像
                'address'      => $address,                 //工作地址
                'deduct'       => $deduct,                  //应扣除时间
                'cycle'        => $request -> cycle,        //结算周期
                'wages'        => $request -> wages,        //工资*100
                'recruiter_id' => $request -> id,           //关联发布者ID
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
                'number'       => $request -> number,       //招聘人数
                'created_at'   => date('Y-m-d H:i:s',time()),//添加时间
            ];
            $res = Works::insertGetId($data);

            //使用swoole协程创建工作群聊天记录表，一个工作对应一张表，表名为group_msg拼接上工作ID
            \go(function() use($res) {
                \co::sleep(0.25);
                if(!Schema::hasTable('group_msg' . $res)){
                    Schema::create('group_msg' . $res,function ($table){
                        $table->increments('id');
                        $table->integer('member_id');
                        $table->json('content');
                        $table->string('created_at',255);
                        $table->tinyInteger('is_rec');
                        $table->string('username',255);
                        $table->string('header',255);
                    });
                }
            });

            $lng = (float)$request -> longitude;//经度
            $lat = (float)$request -> latitude; //纬度
            $street = $request->street;         //街道
            $district = $request->district;     //区
            $city     = $request -> city;       //市
            //记录位置信息到redisgeo中用于地图找
            if($res){
                go(function () use($lng,$lat,$res,$street,$district,$city){
                    \co::sleep(0.25);
                    if($lng && $lat){
                        $district = ReturnJson::toPinyin(substr($district,0,strlen($district)-3));//把区名转为拼音
                        $city     = ReturnJson::toPinyin(substr($city,0,strlen($city)-3));
                        $redis = Redis::connection('geo');
                        $redis -> incr($district);          //redis记录区工作数
                        $street_pin = app('pinyin') -> sentence($street);
                        $street_pin = str_replace(' ','',$street_pin);
                        $redis -> geoadd($city . 'street',$lng,$lat,$street);//记录当前工作的位置信息
                        $redis -> incr($street_pin);         //redis记录街道工作数
                        $data = [
                            'work_id'       => $res,
                            'street_pin'    => $street_pin,
                            'street'        => $street,
                            'district'      => $district,
                        ];
                        WorksGeo::insert($data);                //把工作及位置信息存入数据库
                    }
                });
                go(function (){
                    \co::sleep(0.25);
                    $redis = Redis::connection('census');
                    $redis -> incr('work');
                    $redis -> incr('recruitment');
                });
                go(function () use($request,$res){
                    \co::sleep(1);
                    Describe::insert(['content'=> $request -> describe,'work_id'=>$res]);
                });
            }
            if($res) return ['msg'=>'ok','code'=>0,'result'=>$res];
            return ['msg'=>'err','code'=>1,'result'=>'添加失败！'];
        }else{
            return ['msg'=>'err','code'=>5,'result'=>'你已在其他地方登陆！'];
        }
    }

    /**
     * 查询岗位详情,获取在职员工
     * @param Request $request
     * $request->workid   工作ID
     * @return array
     */
    public function workDetails(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $work = Works::with('workers:workers.id,workers.username,workers.header')
            -> where('id',$request->workid)
            -> get();
        if($work) return ['msg'=>'ok','code'=>0,'result'=>$work];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 获取该工作已离职员工
     * @param Request $request
     * $request->workid     工作ID
     * @return array
     */
    public function workQuit(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $work = Works::with('quit:workers.id,username,header')
            -> where('id',$request->workid)
            -> get();
        if($work) return ['msg'=>'ok','code'=>0,'result'=>$work];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 获取全部有意向（已报名及待面试）的员工
     * @param Request $request
     * @return mixed
     */
    public function allIntentionWorkers(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $res = UserWork::with('workers:id,username,header,education,experience')
            -> where('work_id',$request->workid)
            -> whereIn('status',[0,4])
            -> select('id','worker_id','created_at','status')
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 获取该工作待审核员工
     * @param Request $request
     * $request->workid      工作ID
     * @return array
     */
    public function toBeAudited(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $work = Works::with('toBeAudited')
            -> where('id',$request->workid)
            -> get();
        if($work) return ['msg'=>'ok','code'=>0,'result'=>$work];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 获取某工作已报名员工
     * @param Request $request
     * @return array
     */
    public function enrolment(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $work = Works::with('enrolment')
            -> where('id',$request->workid)
            -> get();
        if($work) return ReturnJson::json('ok',0,$work);
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 查看用户信息
     * @param Request $request
     * $request->workerid    工人ID
     * @return array
     */
    public function workerDetails(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        //$filed = ['id','username','idcard','phone','header','bank','bank_number'];
        $data = Workers::with(['educational','experiences'])
            ->where('id',$request->workerid)
            //-> select($filed)
            -> get() -> toArray();
        $n_year = date('Y');
        $n_month= date('m');
        $n_day  = date('d');
        $year   = date('Y',strtotime($data[0]['birthday']));
        $month  = date('m',strtotime($data[0]['birthday']));
        $day    = date('d',strtotime($data[0]['birthday']));
        if($n_month >= $month && $n_day >= $day){
            $data[0]['birthday'] = $n_year - $year + 1;
        }else{
            $data[0]['birthday'] = $n_year - $year;
        }
        if($data) return ['msg'=>'ok','code'=>0,'result'=>$data];
        return ['msg'=>'err','code'=>1,'result'=>'查询失败！'];
    }

    /**
     * 工人的审核操作
     * @param Request $request
     * $request->workerid    工人ID
     * $request->workid      工作ID
     * $request->status      操作状态
     * @return array
     */
    public function removeWorker(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $res = UserWork::where('worker_id',$request->workerid)
            -> where('work_id',$request->workid)
            -> update(['status' => 1]);
        $work = Works::find($request -> workid);
        $work -> recruitment = $work -> recruitment + 1;
        $work -> save();
        if($res){
            go(function (){
                \co::sleep(1);
                $redis = Redis::connection('census');
                $redis -> incr('onthejob');
            });
            go(function () use($request){
                \co::sleep(1);
                $redis = Redis::connection('msg');
                $msg_id = DB::table('group_msg' . $request->workid) -> select('id') -> orderBy('id','desc') -> first();
                if($msg_id){
                    $redis -> hset('c'.$request->workerid, $request->workid, $msg_id->id);
                }
            });
            go(function () use($request){
                \co::sleep(1);
                Interview::where('work_id',$request->workid) -> where('worker_id',$request->workerid) -> update(['status'=>1]);
            });
        }
        if($res) return ['msg'=>'ok','code'=>0,'result'=>'成功！'];
        return ['msg'=>'err','code'=>1,'result'=>'失败！'];
    }

    /**
     * 面试不通过
     * @param Request $request
     * @return array
     */
    public function refuse(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid'],$request);
        if($error) return $error;

        $res = UserWork::where('worker_id',$request->workerid)
            -> where('work_id',$request->workid)
            -> update(['status' => 3]);
        go(function () use($request){
            \co::sleep(1);
            Interview::where('work_id',$request->workid) -> where('worker_id',$request->workerid) -> update(['status'=>1]);
        });
        if($res) return ['msg'=>'ok','code'=>0,'result'=>'成功！'];
        return ['msg'=>'err','code'=>1,'result'=>'失败！'];
    }

//    /**
//     * 下架工作，标示工作开始并停止找人
//     * @param Request $request
//     * @return mixed
//     */
//    public function atWork(Request $request)
//    {
//        $error = ReturnJson::parameter(['id','workid'],$request);
//        if($error) return $error;
//
//        $res = Works::where('recruiter_id',$request -> id) -> where('id',$request -> workid) -> update(['status' => 1]);
//        if($res){
//            go(function (){
//                $redis = Redis::connection('census');
//            });
//        }
//        if($res) return ReturnJson::json('ok',0,'下架成功');
//        return ReturnJson::json('err',1,'操作失败');
//    }

    /**
     * 接受b端用户注册时上传的图片
     * @param Request $request
     * @return mixed
     */
    public function uploadImage(Request $request)
    {
        if($request->file('image')){
            $data[$request->prefix] = BaseFile::processing(['contentType'=>'file','content'=>$request -> file('image')]);
            return $data;
        }
    }

    /**
     * 获取b端用户个人详情
     * id       用户id
     * token    用户的token
     * @param Request $request
     * @return mixed
     */
    public function getMyDetails(Request $request)
    {
        $error = ReturnJson::parameter(['id','token'],$request);
        if($error) return $error;

        $token = Redis::get('rec_' . $request -> id); //从redis中取到当前用户的token
        if($request->token == $token){
            $res = Recruiters::where('id',$request->id) -> first();
            if($res) return ReturnJson::json('ok',0,$res);
            return ReturnJson::json('err',1,'获取失败');
        }else{
            return ReturnJson::json('err',5,'token失效');
        }
    }

    /**
     * 个人认证成为公司
     * @param Request $request
     * @return mixed
     */
    public function editUserInfo(Request $request)
    {
        $error = ReturnJson::parameter(['isCompany','company','license','id'],$request);
        if($error) return $error;

        $data = [
            'is_company'        => $request->isCompany,
            'company'           => $request->company,
            'license'           => $request->license,
            'status'            => 0
        ];
        $res = Recruiters::where('id',$request -> id) -> update($data);
        if($res) return ReturnJson::json('ok',0,'修改成功,等待审核');
        return ReturnJson::json('err',1,'修改失败');
    }

    /**
     * b端用户修改手机号
     * @param Request $request
     * @return mixed
     */
    public function editPhone(Request $request)
    {
        $error = ReturnJson::parameter(['id','phone'],$request);
        if($error) return $error;

        $res = Recruiters::where('id',$request->id) -> update(['phone' => $request->phone]);
        if($res) return ReturnJson::json('ok',0,'修改成功');
        return ReturnJson::json('err',1,'修改失败');
    }

    /**
     * 通知面试
     * @param Request $request
     * @return mixed
     */
    public function notifyInterview(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid','time','address'],$request);
        if($error) return $error;

        //面试通知
        $openid = Workers::where('id',$request->workerid) -> select('openid','phone','username') -> first();
        $title = Works::where('id',$request->workid) -> select('title') -> first();
        //发送模板消息
        $result = [
            "touser"        => $openid->openid,
            "template_id"   => 'zkXZRzzIPm-07IAJxYT7QmKkUXnqAPTL00Ulw8PGCeg',
            "page"          => '',
            "data"          => [
                "keyword1"      => ["value" => $title->title],
                "keyword2"      => ["value" => $request->time],
                "keyword3"      => ["value" => $openid->phone],
                "keyword4"      => ["value" => $request->address],
                "keyword5"      => ["value" => $openid->username]
            ]
            //"emphasis_keyword" => ''
        ];
        $msg = FromId::sendInterFormid($result,$request->workerid);

        SendSms::send($openid->phone,112442);

        $res = UserWork::where('worker_id',$request->workerid) -> where('work_id',$request->workid) -> update(['status' => 0]);
        if($res){
            go(function () use ($request){
                \co::sleep(0.2);
                Interview::insert([
                    'worker_id'         => $request->workerid,
                    'work_id'           => $request->workid,
                    'time'              => $request->time,
                    'address'           => $request->address,
                    'status'            => 0,
                    'created_at'        => time()
                ]);
            });
            return ReturnJson::json('ok',0,'已通知');
        }
        return ReturnJson::json('err',1,'服务器忙');
    }

    /**
     * B端获取新求职者
     * @param Request $request
     * @return mixed
     */
    public function getNewWorkers(Request $request)
    {
        $error = ReturnJson::parameter(['id','token'],$request);
        if($error) return $error;

        $token = Redis::get('rec_' . $request -> id);
        if($token != $request->token) return ReturnJson::json('err',1,'你已在其他地方登陆');
        $res = Workers::with('experiences:intention_work,intention_place,worker_id')
            -> where('status',1)
            -> select('id','header','username','experience','education','created_at')
            -> orderBy('id','desc')
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }
}