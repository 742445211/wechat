<?php


namespace App\Http\Controllers\ZhaoXian\Cate;


use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Cate;
use Illuminate\Http\Request;

class CateController extends Controller
{
    /**
     * 新增分类
     * @param Request $request
     * $request->title              分类标题
     * $request->level              分类等级
     * $request->pid                父级ID
     * @return mixed
     */
    public function create(Request $request)
    {
        $error = ReturnJson::parameter(['title','pid'],$request);
        if($error) return $error;

        $data = [
            'title'             => $request -> title,           //分类标题
            'pid'               => $request -> pid,             //父级ID,为0时表示顶级分类
        ];
        $res = Cate::insert($data);
        if($res) return ReturnJson::json('ok',0,'添加成功！');
        return ReturnJson::json('err',1,'添加失败！');
    }

    /**
     * 删除分类
     * @param Request $request
     * $request->id             分类ID
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = Cate::where('id',$request->id) -> delete();
        if($res) return ReturnJson::json('ok',0,'删除成功！');
        return ReturnJson::json('err',1,'删除失败！');
    }

    /**
     * 修改分类
     * @param Request $request
     *  $request -> title,           //分类标题
        $request -> level,           //分类等级
        $request -> pid,             //父级ID
     * @return mixed
     */
    public function edit(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $data = [
            'title'             => $request -> title,           //分类标题
            'pid'               => $request -> pid,             //父级ID
        ];
        $res = Cate::where('id',$request->id) -> update($data);
        if($res) return ReturnJson::json('ok',0,'修改成功！');
        return ReturnJson::json('err',1,'修改失败！');
    }

    /**
     * 获取分类
     * @param Request $request
     * $request->pid            父级分类ID
     * @return mixed
     */
    public function get(Request $request)
    {
        //存在参数pid时，根据pid获取分类详情
        if(is_int( (int)$request->pid)){$res = Cate::where('pid',$request->pid) -> get();};
        //没有输入参数时获取全部分类
        if($request->pid === null) {$res = Cate::all();};
        if(isset($res)) return ReturnJson::json('ok',0,$res);

        return ReturnJson::json('err',1,'获取失败！');
    }
}