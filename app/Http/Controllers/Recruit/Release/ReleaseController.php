<?php


namespace App\Http\Controllers\Recruit\Release;


use App\Http\Controllers\Controller;
use App\Recruiter;
use BaseFile;
use FromId;
use App\Work;
use Illuminate\Http\Request;

/**
 * 发布职位相关
 * Class ReleaseController
 * @package App\Http\Controllers\Recruit\Release
 */
class ReleaseController extends Controller
{
    /**
     * 已发布职位
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $id = $request->recruit_id;//获取当前用户的ID
        if($id){
            $work = Work::where('rid',$id) -> get();//获取当前用户发布的所有职位
            if($work){
                return ['msg'=>'ok', 'code'=>'0','result'=>$work];
            }else{
                return ['msg'=>'err','code'=>'1','result'=>'暂无数据'];
            }
        }
    }


    public function page()
    {
    }

    /**
     * 发布职位
     * @param Request $request
     * @return array
     */
    public function release(Request $request)
    {
        //$rid = $request->recruit_id;
        //把收到的二进制字符串转换为文件保存并写入数据库
        $img['file']        = $request->file;
        $img['content']     = $request->content;
        $img['contentType'] = 'img';//组装$base需要的参数
        $header = BaseFile::processing($img);//把base64二进制流转为文件

        $a = FromId::recruitSave($request->fromid);//把formid存入数据库备用

        if($header){
            $data = $request->info;
            $data['header'] = $header;//把地址存入数据库
            $data['status'] = 0;//默认待审核
            $data['cate']   = $request->cate;
            $res = Recruiter::create($data);
            if($res){
                return ['msg'=>'ok', 'code'=>0,'result'=>'发布成功，等待审核'];
            }else{
                return ['msg'=>'err','code'=>1,'result'=>'未知错误，请稍后再试'];
            }
        }
    }

    public function test(Request $request)
    {
        $data['content']     = $request->content;
        $data['contentType'] = 'img';
        $data['file']        = $request->file;
        $res = BaseFile::processing($data);
        return $res;
    }
}