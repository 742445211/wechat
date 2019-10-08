<?php


namespace App\Http\Controllers\ZhaoXian\WorkExperience;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Workers;
use App\Model\WorkExperience;
use Illuminate\Http\Request;

class WorkExperienceController extends Controller
{
    /**
     * 员工端添加工作经历
     * @param Request $request
     * $request -> workerid,            //关联员工ID
       $request -> work_unit,           //工作单位
       $request -> position,            //工作职位
       $request -> work_time,           //工作时间
       $request -> city,                //工作城市
       $request -> discribe,            //工作描述（100字内）
       $request -> intention_work,      //意向工作
       $request -> intention_place,     //意向地点
       $request -> intention_time       //意向工作时间
     * @return mixed
     */
    public function create(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $data = [
            'worker_id'         => $request -> workerid,            //关联员工ID
            'work_unit'         => $request -> work_unit,           //工作单位
            'position'          => $request -> position,            //工作职位
            'work_time'         => $request -> work_time,           //工作时间
            'city'              => implode('',$request -> city),                //工作城市
            'discribe'          => $request -> discribe,            //工作描述（100字内）
            //'intention_work'    => $request -> intention_work,      //意向工作
            //'intention_place'   => $request -> intention_place,     //意向地点
            //'intention_time'    => $request -> intention_time,      //意向工作时间
        ];
        //更新workers表的工作经验时长
        go(function () use ($request){
            \co::sleep(0.25);
            $time = explode('~',$request->work_time);
            $year = substr($time[0],0,4) - substr($time[1],0,4);
            $now = Workers::where('id',$request->workerid) -> select('experience') -> first();
            $now = $now->experience + $year;
            Workers::where('id',$request->workerid) -> update(['experience' => $now]);
        });
        if(count(WorkExperience::where('worker_id',$request->workerid)->select('id')->first())){
            $res = WorkExperience::where('worker_id',$request->workerid) -> update($data);
        }else{
            $data['worker_id'] = $request->workerid;
            $res = WorkExperience::insert($data);
        }
        if($res) return ReturnJson::json('ok',0,'添加成功！');
        return ReturnJson::json('err',1,'添加失败！');
    }

    /**
     * 员工修改工作经历
     * @param Request $request
     *  $request -> workerid,            //关联员工ID
        $request -> work_unit,           //工作单位
        $request -> position,            //工作职位
        $request -> work_time,           //工作时间
        $request -> city,                //工作城市
        $request -> discribe,            //工作描述（100字内）
        $request -> intention_work,      //意向工作
        $request -> intention_place,     //意向地点
        $request -> intention_time       //意向工作时间
     * @return mixed
     */
    public function edit(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $data = [
            'worker_id'         => $request -> workerid,            //关联员工ID
            'work_unit'         => $request -> work_unit,           //工作单位
            'position'          => $request -> position,            //工作职位
            'work_time'         => $request -> work_time,           //工作时间
            'city'              => $request -> city,                //工作城市
            'discribe'          => $request -> discribe,            //工作描述（100字内）
        ];
        $res = WorkExperience::where('worker_id',$request->workerid) -> update($data);
        //更新workers表的工作经验时长
        go(function () use ($request){
            \co::sleep(0.25);
            $all = WorkExperience::where('worker_id',$request->workerid) -> select('work_time') -> get() ->toArray();
            $experience = 0;
            foreach ($all as $value){
                $time = explode('~',$value['experience']);
                $experience += substr($time[0],0,4) - substr($time[1],0,4);
            }
            Workers::where('id',$request->workerid) -> update(['experience' => $experience]);
        });

        if($res) return ReturnJson::json('ok',0,'修改成功！');
        return ReturnJson::json('err',1,'修改失败！');
    }

    /**
     * 添加意向工作状态
     * @param Request $request
     * @return mixed
     */
    public function addIntention(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $data = [
            'intention_work'    => $request -> intention_work,      //意向工作
            'intention_place'   => $request -> intention_place,     //意向地点
            'intention_time'    => $request -> intention_time       //意向工作时间
        ];
        if(count(WorkExperience::where('worker_id',$request->workerid)->select('id')->first())){
            $res = WorkExperience::where('worker_id',$request->workerid) -> update($data);
        }else{
            $data['worker_id'] = $request->workerid;
            $res = WorkExperience::insert($data);
        }
        if($res) return ReturnJson::json('ok',0,'添加成功');
        return ReturnJson::json('err',1,'添加失败');
    }

    /**
     * 获取员工经历（三端可用）
     * @param Request $request
     * @return mixed
     */
    public function getWorkExperience(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $field = ['id','work_unit','position','work_time','city','discribe','intention_work','intention_place','intention_time'];
        $res = WorkExperience::where('worker_id',$request->workerid) -> select($field) -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！');
    }

    /**
     * 删除经历
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','id'],$request);
        if($error) return $error;

        $res = WorkExperience::where('id',$request->id) -> where('worker_id',$request->workerid) -> delete();
        //更新workers表的工作经验时长
        go(function () use ($request){
            \co::sleep(0.25);
            $all = WorkExperience::where('worker_id',$request->workerid) -> select('work_time') -> get() ->toArray();
            $experience = 0;
            foreach ($all as $value){
                $time = explode('~',$value['experience']);
                $experience += substr($time[0],0,4) - substr($time[1],0,4);
            }
            Workers::where('id',$request->workerid) -> update(['experience' => $experience]);
        });

        if($res) return ReturnJson::json('ok',0,'已删除');
        return ReturnJson::json('err',1,'删除失败！');
    }
}