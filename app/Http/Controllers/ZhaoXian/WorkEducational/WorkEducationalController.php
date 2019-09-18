<?php


namespace App\Http\Controllers\ZhaoXian\WorkEducational;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\WorkEducational;
use Illuminate\Http\Request;

class WorkEducationalController extends Controller
{
    /**
     * 员工添加教育经历
     * @param Request $request
     *  $request -> workerid,            //员工ID
        $request -> school,              //学校名称
        $request -> enrol,               //入学时间
        $request -> finish,              //毕业时间
        $request -> major,               //所学专业
     * @return mixed
     */
    public function create(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $data = [
            'worker_id'         => $request -> workerid,            //员工ID
            'school'            => $request -> school,              //学校名称
            'enrol'             => $request -> enrol,               //入学时间
            'finish'            => $request -> finish,              //毕业时间
            'major'             => $request -> major,               //所学专业
        ];
        $res = WorkEducational::insert($data);
        if($res) return ReturnJson::json('ok',0,'添加成功！');
        return ReturnJson::json('err',1,'添加失败！');
    }

    /**
     * 员工修改教育经历
     * @param Request $request
     *  $request -> workerid,            //员工ID
        $request -> school,              //毕业学校
        $request -> enrol,               //入学时间
        $request -> finish,              //毕业时间
        $request -> major,               //所学专业
     * @return mixed
     */
    public function edit(Request $request)
    {
        $error = ReturnJson::parameter(['workerid','id'],$request);
        if($error) return $error;

        $data = [
            'worker_id'         => $request -> workerid,            //员工ID
            'school'            => $request -> school,              //毕业学校
            'enrol'             => $request -> enrol,               //入学时间
            'finish'            => $request -> finish,              //毕业时间
            'major'             => $request -> major,               //所学专业
        ];
        $res = WorkEducational::where('worker_id',$request->workerid) -> where('id',$request->id) -> update($data);
        if($res) return ReturnJson::json('ok',0,'更新成功！');
        return ReturnJson::json('err',1,'更新失败！');
    }

    /**
     * 获取员工教育经历（三端可用）
     * @param Request $request
     * $request->workerid           员工ID
     * @return mixed
     */
    public function get(Request $request)
    {
        $error = ReturnJson::parameter(['workerid'],$request);
        if($error) return $error;

        $filed = ['id','school','enrol','finish','major'];
        $res = WorkEducational::where('worker_id',$request->workerid) -> select($filed) -> get();
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

        $res = WorkEducational::where('id',$request->id) -> where('worker_id',$request->workerid) -> delete();
        if($res) return ReturnJson::json('ok',0,'已删除');
        return ReturnJson::json('err',1,'删除失败！');
    }
}