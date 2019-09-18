<?php


namespace App\Http\Controllers\ZhaoXian\Banner;


use App\Facades\BaseFile;
use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * 添加轮播图
     * @param Request $request
     * $request->file('banner')         轮播图文件
     * @return mixed
     */
    public function create(Request $request)
    {
        if($request -> file('banner') != null){
            $data = [
                'image_path'       => BaseFile::processing(['contentType' => 'file','content' => $request -> file('banner')]),
                'created_at'       => date('Y-m-d H:i:s',time())
            ];
            $res = Banner::insert($data);
            if($res) return ReturnJson::json('ok',1,'添加成功！');
            return ReturnJson::json('err',0,'添加失败！');
        }
    }

    /**
     * 上线或下架轮播图（添加完成后 默认时上线状态）
     * @param Request $request
     * $request->id             banner图ID
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['id','status'],$request);
        if($error) return $error;

        $res = Banner::where('id', $request -> id) -> update(['status' => $request -> status]);
        if($res) return ReturnJson::json('ok',0,'更新成功！');
        return ReturnJson::json('err',1,'更新失败！');
    }

    /**
     * 获取发布中的banner图
     * @return mixed
     */
    public function get()
    {
        $res = Banner::where('status',0) -> orderBy('level','desc') -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败！');
    }
}