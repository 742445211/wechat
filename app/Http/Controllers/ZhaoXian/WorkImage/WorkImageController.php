<?php


namespace App\Http\Controllers\ZhaoXian\WorkImage;


use App\Facades\BaseFile;
use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\WorkImage;
use Illuminate\Http\Request;

class WorkImageController extends Controller
{
    /**
     * 前台上传图片
     * @param Request $request
     * @return mixed
     */
    public function upload(Request $request)
    {
        //判断是否有文件传入
        if($request -> file() != null){
            //把上传过来的图片存入服务器网站根目录下public/upload_auth文件夹内
            $src = BaseFile::processing(['contentType'=>'file','content'=>$request -> file('work_image')]);
            //写入成功后返回文件的网络路径
            if($src) return ReturnJson::json('ok',0,$src);
            return ReturnJson::json('err',1,'文件存入失败！');
        }
        return ReturnJson::json('err',7,'未获取到文件！');
    }

    /**
     * 添加工作详情图
     * @param Request $request
     * $request->workid             工作ID
     * $request->file('work_image') 图片文件
     * @return mixed
     */
    public function addImage(Request $request)
    {
        $error = ReturnJson::parameter(['workid','src'],$request);
        if($error) return $error;

        //判断是否存入多张图片
        $src = $request -> src;
        //如果是多张图片，把每张图片循环出来拼接成一个数组
        if(is_array($src)){
            foreach ($src as $v){
                $data[] = [
                    'work_id'           => $request -> workid,                  //工作ID
                    'work_image'        => $v,                                  //图片路径
                    'created_at'        => date('Y-m-d H:i:s',time())   //创建时间
                ];
            }
        }else{
            //如果不是多张图片，就存一张
            $data = [
                'work_id'           => $request -> workid,                  //工作ID
                'work_image'        => $src,                                //图片路径
                'created_at'        => date('Y-m-d H:i:s',time())   //创建时间
            ];
        }
        $res = WorkImage::insert($data);
        if($res) return ReturnJson::json('ok',0,$src);
        return ReturnJson::json('err',1,'添加失败！');
    }

    /**
     * 删除工作图片
     * @param Request $request
     * $request->id             工作ID
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        //查询图片是否存在，存在返回路径
        //$src = WorkImage::where('id',$request -> id) -> select('work_image') -> first() -> toArray();
        //if($src['work_image'] != null){
            //删除图片，再删除数据库中的记录
            //BaseFile::unlinkFile($src['header']);
            $res = WorkImage::where('id',$request -> id) -> delete();
            if($res) return ReturnJson::json('ok',0,'删除成功！');
            return ReturnJson::json('err',1,'删除失败！');
        //}
        //return ReturnJson::json('err',6,'图片不存在！');
    }

    /**
     * 修改工作图片
     * @param Request $request
     * $request->id         图片ID
     * $request->file('work_image')     新图片文件
     * @return mixed
     */
    public function edit(Request $request)
    {
        $error = ReturnJson::parameter(['workid','src'],$request);
        if($error) return $error;

        //保存新图片，获取路径
        //$src = BaseFile::processing(['contentType'=>'file','content'=>$request -> file('work_image')]);
        $data = [
            'work_id'           => $request->workid,
            'work_image'        => $request->src,
            'updated_at'        => date('Y-m-d H:i:s',time()),
        ];
        //$res = WorkImage::where('id',$request -> id) -> update($data);
        $res = WorkImage::insertGetId($data);
        if($res) return ReturnJson::json('ok',0,['id'=>$res,'image_path'=>$request->src]);
        return ReturnJson::json('err',1,'更新失败！');
    }

    /**
     * 获取某工作的图片信息
     * @param Request $request
     * $request->workid         工作ID
     * @return mixed
     */
    public function get(Request $request)
    {
        $error = ReturnJson::parameter(['workid'],$request);
        if($error) return $error;

        $filed = ['id','work_image','created_at','updated_at'];
        $res = WorkImage::where('work_id',$request -> workid) -> select($filed) -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'查询失败！');
    }
}