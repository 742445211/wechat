<?php


namespace App\Http\Controllers\Recruit\Price;


use App\Http\Controllers\Controller;
use App\Price;
use App\Sign;
use App\Work;
use Illuminate\Http\Request;

/**
 * 薪资类
 * Class PriceController
 * @package App\Http\Controllers\Recruit\Price
 */
class PriceController extends Controller
{

    /**
     * 当前用户上架工作的薪资相关资料
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $id = $request->recruit_id;
        $work_id = Work::where('rid',$id) -> where('status',1) -> select('id') -> get();//通过用户ID查询用户发布的工作
        if($work_id){
            $data = Price::with('work:id,title,price,days','homeuser:id,username')
                -> whereIn('workid',$work_id)
                -> get();//查询账单资料
            if($data){
                return ['msg'=>'ok','code'=>0,'result'=>$data];
            }else{
                return ['msg'=>'err','code'=>1,'result'=>'未知错误'];
            }
        }
    }

    /**
     * 查询签到
     * @param Request $request
     * @return array
     */
    public function sign(Request $request)
    {
        /*$id = $request->recruit_id;
        $work_id = Work::where('rid',$id) -> where('status',1) -> select('id') -> get();//通过用户ID查询用户发布的工作*/
        $workid = $request->workid;
        $data = Sign::with('homeuser:id,username')
            -> where('workid',$workid)
            -> get();
        if($data){
            return ['msg'=>'ok', 'code'=>0,'result'=>$data];
        }else{
            return ['msg'=>'err','code'=>1,'result'=>'未知错误'];
        }
    }


}