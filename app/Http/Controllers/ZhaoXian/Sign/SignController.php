<?php


namespace App\Http\Controllers\ZhaoXian\Sign;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Sign;
use Illuminate\Http\Request;

class SignController extends Controller
{
    /**
     * 添加签到记录
     * @param Request $request
     * $request->workerid   员工ID
     * $request->workid     工作ID
     * $request->start      开始时间
     * $request->end        结束时间
     * @return mixed
     */
    public function sign(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','workid','time','status'],$request);
        if($error) return $error;

        $time = explode(' ',$request -> time);
        $ymd  = explode('/',$time[0]);
        if($request -> status === 'start'){
            $data = [
                'worker_id'  => $request -> workerid,
                'work_id'    => $request -> workid,
                'year'       => $ymd[0],
                'month'      => $ymd[1],
                'day'        => $ymd[2],
                'start'      => $time[1],
                'address'    => $request -> address
            ];
            $res = Sign::insert($data);
        }else{ //liugeloudong
            $res = Sign::where('worker_id',$request -> workerid)
                -> where('work_id',$request -> workid)
                -> where('year',$ymd[0])
                -> where('month',$ymd[1])
                -> where('day',$ymd[2])
                -> update(['end' => $time[1]]);
        }
        if($res) return ReturnJson::json('ok',0,'记录成功！');
        return ReturnJson::json('err',1,'记录失败！');
    }

    /**
     * 获取工人当前工作的签到记录
     * @param Request $request
     * $request->workerid   员工ID
     * $request->woekid     工作ID
     * @return mixed
     */
    public function getWorkerSign(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','offset'],$request);
        if($error) return $error;

        $handle = Sign::where('worker_id',$request -> workerid) -> select('id','work_id','year','month','day','start','end');
        //进入后返回的最后一次记录
        if($request -> offset == 0){
            $res = $handle -> orderBy('id','desc') -> first();
        }
        //点击右边按钮返回下一条数据
        if($request -> offset == 'next'){
            $res = $handle -> where('id','>',$request -> id) -> first();
        }
        //点击左边按钮返回上一条数据
        if($request -> offset == 'prev'){
            if($request -> id){
                $res = $handle -> where('id','<',$request -> id) -> first();
            }else{
                $res = $handle -> orderBy('id','desc') -> first();
            }
        }
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'查询失败！');
    }

    /**
     * 获取某工作的签到,按年查询
     * @param Request $request
     * $request->workid         工作ID
     * $request->year           年份
     * @return mixed
     */
    public function getWorkSign(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $chan1 = new \Co\Channel(1);
        $chan2 = new \Co\Channel(2);
        $chan3 = new \Co\Channel(3);
        //获取签到时间，按月分组,按年查询
        $res = Sign::select('month','start','end')
            -> where('work_id',$request -> workid)
            -> where('year',$request -> year)
            -> get()
            -> groupBy('month')
            -> toArray();
        //获取签到人，按月分组，按年查询
        $member = Sign::select('month','worker_id')
            -> where('work_id',$request -> workid)
            -> where('year',$request -> year)
            -> get()
            -> groupBy('month')
            -> toArray();
        //计算总时间和总人数
        $data = [];
        foreach ($res as $key=>$val){
            go(function () use($chan1,$chan2,$val){
                $hour = 0;
                foreach ($val as $v){
                    go(function () use($chan2,$v){
                        $end = explode(':',$v['end']);
                        $start = explode(':',$v['start']);
                        //分钟大于30加一小时
                        if($end[1] > 30){
                            $end[0] += 1;
                        }
                        if($start[1] > 30){
                            $start[0] += 1;
                        }
                        //如果签退比签到小就加24小时
                        if($end[0] < $start[0]){
                            $end[0] = $end[0] + 24;
                        }
                        $chan2 -> push((int)$end[0] - (int)$start[0]);
                    });
                    $hour += $chan2 -> pop();
                }
                $chan1 -> push($hour);
            });
            $data[$key]['hour']  = $chan1 -> pop();
            $data[$key]['month'] = $key;
        }
        $mem = [];
        foreach ($member as $key => $val){
            go(function () use($val,$key,$chan3,$mem){
                foreach ($val as $v){
                    $mem[$key]['worker'][] = $v['worker_id'];
                }
                $a = array_flip($mem[$key]['worker']);
                $a = array_flip($a);
                $a = count($a);
                $chan3 -> push($a);
            });
            $data[$key]['member'] = $chan3 -> pop();
        }
        rsort($data);
        if($res) return ReturnJson::json('ok',0,$data);
        return ReturnJson::json('err',1,'查询失败！');
    }

    /**
     * 根据月份查询签到记录
     * @param Request $request
     * $request->workid         工作ID
     * $request->month          当前月份
     * @return mixed
     */
    public function getWorkSignByMonth(Request $request)
    {
        $error = ReturnJson::parameter(['workid','month'],$request);
        if($error) return $error;

        $res = Sign::with('workers:workers.id,workers.header,workers.username,workers.phone')
            -> where('work_id',$request -> workid)
            -> where('month',$request -> month)
            -> select('sign.id','sign.worker_id','sign.start','sign.end')
            -> get()
            -> groupBy('worker_id')
            -> toArray();
        $data = [];
        foreach ($res as $key => $val){
            $time = 0;
            foreach ($val as $v){
                $data[$key]['worker'] = $v['workers'];
                $end = explode(':',$v['end']);
                $start = explode(':',$v['start']);
                //分钟大于30加一小时
                if($end[1] > 30){
                    $end[0] += 1;
                }
                if($start[1] > 30){
                    $start[0] += 1;
                }
                //如果签退比签到小就加24小时
                if($end[0] < $start[0]){
                    $end[0] = $end[0] + 24;
                }
                $time += $end[0] - $start[0];
            }
            $data[$key]['time'] = $time;
            $data[$key]['days'] = count($val);
        }
        rsort($data);
        if($res) return ReturnJson::json('ok',0,$data);
        return ReturnJson::json('err',1,'查询失败！');
    }

    /**
     * 通过员工ID获取签到信息
     * @param Request $request
     * $request->workid         工作ID
     * $request->workerid       员工ID
     * @return mixed
     */
    public function getWorkSignByWorker(Request $request)
    {
        $error = ReturnJson::parameter(['workid','workerid'],$request);
        if($error) return $error;

        $res = Sign::with('workers:workers.id,workers.header,workers.username,workers.phone,workers.idcard')
            -> where('work_id',$request -> workid)
            -> where('worker_id',$request -> workerid)
            -> select('month','start','end','worker_id')
            -> get()
            -> groupBy('month')
            -> toArray();
        $data = [];
        foreach ($res as $key => $val){
            $time = 0;
            foreach ($val as $v){
                $end = explode(':',$v['end']);
                $start = explode(':',$v['start']);
                //分钟大于30加一小时
                if($end[1] > 30){
                    $end[0] += 1;
                }
                if($start[1] > 30){
                    $start[0] += 1;
                }
                //如果签退比签到小就加24小时
                if($end[0] < $start[0]){
                    $end[0] = $end[0] + 24;
                }
                $time += $end[0] - $start[0];
            }
            $data[$key]['month'] = $v['month'];
            $data[$key]['time']  = $time;
            $data[$key]['days']  = count($val);
        }
        if($res) return ReturnJson::json('ok',0,$data);
        return ReturnJson::json('err',1,'查询失败');
    }

    /**
     * 获取员工某月在某岗位上的工作天数
     * @param Request $request
     * @return mixed
     */
    public function getWorkerSignByMonth(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','year','month','workid'],$request);
        if($error) return $error;

        $year = $request -> year;
        $month= $request -> month;
        $res = Sign::where('worker_id',$request -> workerid)
            -> where('work_id', $request -> workid)
            -> where('year',$year)
            -> where('month',$month)
            -> select('id')
            -> get();
        if($res) return ReturnJson::json('ok',0,count($res));
        return ReturnJson::json('err',1,'获取失败');
    }
}