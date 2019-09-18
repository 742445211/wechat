<?php


namespace App\Http\Controllers\ZhaoXian\Store;


use App\Facades\BaseFile;
use App\Facades\ReturnJson;
use App\Http\Controllers\Controller;
use App\Model\Store;
use App\Model\StoreImage;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * 添加店铺
     * @param Request $request
     * $request -> title            店铺名
     * $request -> phone            联系电话
     * $request -> address          店铺地址
     * $request -> recruiter_id     关联店主ID
     * @return mixed
     */
    public function addStore(Request $request)
    {
        $error = ReturnJson::parameter(['title','phone','address','recruiter_id'],$request);
        if($error) return $error;

        $data = [
            'title'         => $request -> title,
            //店铺logo
            'logo'          => $request -> logo,
            //店铺营业时间
            'business'      => $request -> business,
            'phone'         => $request -> phone,
            'address'       => $request -> address,
            'recruiter_id'  => $request -> recruiter_id,
            'status'        => 0,
            //发布时间
            'created_at'    => time(),
        ];
        $res = Store::insertGetId($data);
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'添加失败');
    }

    /**
     * 修改店铺信息
     * @param Request $request
     * @return mixed
     */
    public function editStore(Request $request)
    {
        $error = ReturnJson::parameter(['id','recruiter_id'],$request);
        if($error) return $error;

        $data = [
            'title'         => $request -> title,
            //店铺logo
            'logo'          => $request -> logo,
            //店铺营业时间
            'business'      => $request -> business,
            'phone'         => $request -> phone,
            'address'       => $request -> address,
            //更新时间
            'updated_at'    => time(),
        ];
        $res = Store::where('id',$request -> id)
            -> where('recruiter_id',$request -> recruiter_id)
            -> update($data);
        if($res) return ReturnJson::json('ok',0,'更新成功');
        return ReturnJson::json('err',1,'更新失败！');
    }

    /**
     * 删除店铺信息
     * @param Request $request
     * @return mixed
     */
    public function deleteStore(Request $request)
    {
        $error = ReturnJson::parameter(['id','recruiter_id'],$request);
        if($error) return $error;

        $res = Store::where('id',$request -> id) -> where('recruiter_id',$request -> recruiter_id) -> update(['status' => 1]);
        if($res) return ReturnJson::json('ok',0,'删除成功');
        return ReturnJson::json('err',1,'删除失败');
    }

    /**
     * 获取店铺信息
     * @param Request $request
     * $request->recruiter_id       招聘端用户ID
     * @return mixed
     */
    public function getStore(Request $request)
    {
        $error = ReturnJson::parameter(['recruiter_id'],$request);
        if($error) return $error;

        $res = Store::with('storeImage:id,image_path')
            -> where('recruiter_id',$request -> recruiter_id)
            -> where('status',0)
            ->select('id','title','logo','business','phone','address','image_id')
            -> get();
        if($res) return ReturnJson::json('ok',0,$res);
        return ReturnJson::json('err',1,'获取失败');
    }

    /**
     * 添加店铺图片
     * @param Request $request
     * $request->store_id           店铺ID
     * @return mixed
     */
    public function addImage(Request $request)
    {
        $error = ReturnJson::parameter(['store_id'],$request);
        if($error) return $error;

        $image = BaseFile::processing(['contentType'=>'logo','content'=>$request -> file('image')]);
        $data = [
            'image_path'        => $image,
            'store_id'          => $request -> store_id
        ];
        $res = StoreImage::insert($data);
        if($res) return ReturnJson::json('ok',0,$image);
        return ReturnJson::json('err',1,'添加失败');
    }

    /**
     * 删除图片
     * @param Request $request
     * $request->id         店铺图片的ID
     * @return mixed
     */
    public function delImage(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $res = StoreImage::where('id',$request -> id) -> update(['status'=>1]);
        if($res) return ReturnJson::json('ok',0,'已删除');
        return ReturnJson::json('err',1,'删除失败');
    }

    /**
     * 修改店铺图片
     * @param Request $request
     * $request -> id           图片ID
     * @return mixed
     */
    public function editImage(Request $request)
    {
        $error = ReturnJson::parameter(['id'],$request);
        if($error) return $error;

        $image = BaseFile::processing(['contentType'=>'logo','content'=>$request -> file('image')]);
        $res = StoreImage::where('id',$request -> id) -> where('status',0) -> update(['image_path' => $image]);
        if($res) return ReturnJson::json('ok',0,$image);
        return ReturnJson::json('err',1,'更新失败');
    }
}