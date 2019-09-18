<?php


namespace App\Http\Controllers\Admin\Audit;


use App\Http\Controllers\Controller;
use App\Recruiter;
use App\Sms\Sendsms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


/**
 * 审核企业相关
 * Class AuditController
 * @package App\Http\Controllers\Admin\Audit
 */
class AuditController extends Controller
{
    private $recruiter;

    public function __construct()
    {
        $this->recruiter = new Recruiter();
    }

    /**
     * 按条件查询用户
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request,$status)
    {
        $level = DB::table('adminuser') -> where('id','=',session('userid')) -> value('level');  //为1是超管
        if($level == '1'){   //超级管理员
            $handle = $this->recruiter -> where('status',$status);

            $endtime = $request -> endtime;   //结束时间
            $starttime = $request -> starttime;   // 开始时间
            $keyword = $request -> keyword;    // 关键词

            if($starttime){   // 开始时间
                $starttime=strtotime($starttime); //获取日期转换后的时间戳
            }
            if($endtime){   //结束时间
                $endtime=strtotime($endtime); //获取日期转换后的时间戳
            }

            $keyword && $handle -> where('username','like','%'.$keyword.'%');
            $starttime && $handle -> where('created_at','>',$starttime);
            $endtime && $handle -> where('created_at','<',$endtime);
            // 获取数据
            $all = $handle -> orderBy('created_at','desc') -> paginate(8);
            //遍历所有用户
                $count = $handle -> count();
            return view('Admin.Audit.audit',['all'=>$all,'count'=>$count,'endtime'=>$request -> endtime,'keyword'=>$request -> keyword,'request' => $request -> all(),'status' => $status]);
        }else{
            return back();
        }
    }

    /**
     * 查看已审核和未审核的所有企业用户
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function authentication($status,Request $request)
    {
        $status = isset($status)? $status : 0;
        $recruiter = $this->recruiter;
        $all = $recruiter::where('status',$status) -> orderBy('id','desc') -> paginate(8);//按状态查询所有用户并分页，每页8条
        $count = count($all);

        return view('Admin.Audit.audit',['all' => $all,'count' => $count,'status'=>$status,'request'=>$request->all()]);
    }

    /**
     * 查看用户详情
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $recruiter = $this->recruiter;
        $data = $recruiter::find($id);//查询这个用户的详细信息
        //处理性别
        $sex = ['0'=>'女','1'=>'男','2'=>'保密'];

        return view('Admin.Audit.show',['data' => $data,'sex' => $sex]);
    }

    /**
     * 审核操作
     * @param Request $request
     * @return array
     */
    public function status(Request $request)
    {
        $status = $request->status;
        $id = $request->id;
        $recruiter = $this->recruiter;
        $a = $recruiter::find($id);//查询到这个ID的用户
        $a->status = $status;
        if($a->save()){//更新字段，更新成功返回状态
            if($status == 1){
                $send = new Sendsms();//调用聚合数据短信接口
                $res = $send->send($a->phone,'112443');//发送短信
            }elseif ($status == 0){
                $send = new Sendsms();//调用聚合数据短信接口
                $res = $send->send($a->phone,'112443');//发送短信
            }
            return $status;
        }
    }

    /**
     * 未通过审核
     * @param Request $request
     * @return int
     */
    public function no(Request $request)
    {
        $id = $request->id;
        $recruiter = $this->recruiter;
        $a = $recruiter::find($id);//查询到这个ID的用户
        $send = new Sendsms();
        $res = $send->send($a->phone,'112443');
        $a->status = 3;
        if($a->save()){//更新字段，更新成功返回状态
            return 1;
        }
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