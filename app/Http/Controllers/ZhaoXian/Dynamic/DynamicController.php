<?php


namespace App\Http\Controllers\ZhaoXian\Dynamic;


use App\Facades\BaseFile;
use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Dynamic;
use App\Model\DynamicImage;
use Illuminate\Http\Request;

class DynamicController extends Controller
{
    /**
     * 创建动态
     * @param Request $request
     * $request -> recruiter_id         发布者ID
     * $request -> content              动态内容
     * $request -> images（array）      图片路径
     * @return mixed
     */
    public function create(Request $request)
    {
        $error = ReturnJson::parameter(['recruiter_id'],$request);
        if($error) return $error;

        $data = [
            'recruiter_id'          => $request -> recruiter_id,
            'content'               => $request -> content,
            'created_at'            => time(),
        ];
        $res = Dynamic::insertGetId($data);
        //内容添加完成返回ID，再判断是否有图片上传过来，有就批量插入图片，没得就算了
        if($res){
            if($request -> images == null){
                return ReturnJson::json('ok',0,'添加成功');
            }else{
                $images = [];
                foreach ($request -> images as $k => $v){
                    $images[$k] = [
                        'image_path'        => $v,
                        'dynamic_id'        => $res,
                        'created_at'        => time()
                    ];
                }
                $res = DynamicImage::insert($images);
                if($res) return ReturnJson::json('ok',0,'添加成功');
            }
        }
        return ReturnJson::josn('err',1,'添加失败');
    }

    /**
     * 删除动态
     * @param Request $request
     * $request -> id           动态ID
     * $request -> recruiter_id 发布者ID
     * @return mixed
     */
    public function delete(Request $request)
    {
        $error = ReturnJson::parameter(['id','recruiter_id'],$request);
        if($error) return $error;

        $res = Dynamic::where('id',$request -> id)
            -> where('recruiter_id',$request -> recruiter_id)
            -> update(['status' => 1]);
        if($res) return ReturnJson::json('ok',0,'已删除');
        return ReturnJson::json('err',1,'删除失败');
    }

    /**
     * 修改动态
     * @param Request $request
     * @return mixed
     */
    public function edit(Request $request)
    {
        $error = ReturnJson::parameter(['id','recruiter_id'],$request);
        if($error) return $error;

        $data = [
            'content'           => $request -> content,
            'updated_at'        => time()
        ];
        $res = Dynamic::where('id',$request -> id)
            -> where('recruiter_id',$request -> recruiter_id)
            -> update($data);
        if($res) return ReturnJson::json('ok',0,'修改成功');
        return ReturnJson::json('err',1,'修改失败');
    }

    /**
     * 查询发布的动态
     * @param Request $request
     * $request -> recruiter_id         发布者ID
     * @return mixed
     */
    public function getDynamic(Request $request)
    {
        $error = ReturnJson::parameter(['recruiter_id'],$request);
        if($error) return $error;

        $res = Dynamic::with('dynamicImage','recruiter:id,username,header')
            -> where('recruiter_id',$request -> recruiter_id)
            -> where('status',0)
            -> select('id','content','created_at','updated_at','recruiter_id')
            -> orderBy('id','desc')
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'查询失败');
    }

    /**
     * 添加动态图
     * @param Request $request
     * $request -> dynamic_id           动态ID
     * $request -> file('image')        图片文件
     * @return mixed
     */
    public function addImage(Request $request)
    {
        $error = ReturnJson::parameter(['dynamic_id'],$request);
        if($error) return $error;

        $data = [
            'dynamic_id'            => $request -> dynamic_id,
            'image_path'            => BaseFile::processing(['contentType'=>'logo','content'=>$request -> file('image')]),
            'created_at'            => time()
        ];
        $res = DynamicImage::insertGetId($data);
        $data['id'] = $res;
        if($res) return ReturnJson::json('ok',0,$data);
        return ReturnJson::json('err',1,'添加失败');
    }

    /**
     * 删除图片
     * @param Request $request
     * $request -> image_id         图片ID
     * @return mixed
     */
    public function delImage(Request $request)
    {
        $error = ReturnJson::parameter(['image_id'],$request);
        if($error) return $error;

        $res = DynamicImage::where('id',$request -> image_id) -> update(['status'=>1]);
        if($res) return ReturnJson::json('ok',0,'删除成功');
        return ReturnJson::json('err',1,'删除失败');
    }

    /**
     * 修改动态图
     * @param Request $request
     * $request -> image_id             图片ID
     * $request -> recruiter_id         发布者ID
     * $request -> file('image')        图片文件
     * @return mixed
     */
    public function editImage(Request $request)
    {
        $error = ReturnJson::parameter(['image_id','recruiter_id'],$request);
        if($error) return $error;

        $data = [
            'image_path'        => BaseFile::processing(['contentType'=>'logo','content'=>$request -> file('image')]),
            'updated_at'        => time()
        ];
        $res = DynamicImage::where('id',$request -> image_id) -> update($data);
        if($res) return ReturnJson::json('ok',0,'修改成功');
        return ReturnJson::json('err',1,'修改失败');
    }

    /**
     *上传动态图片
     * @param Request $request
     * @return mixed
     */
    public function uploadDynamicImage(Request $request)
    {
        $image_path = BaseFile::processing(['contentType'=>'logo','content'=>$request -> file('image')]);
        $num = $request -> num;
        if($image_path) return ReturnJson::json('ok',0,['url' => $image_path, 'num' => $num]);
        return ReturnJson::json('err',1,'上传失败');
    }
}