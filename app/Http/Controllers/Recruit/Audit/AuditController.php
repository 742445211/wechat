<?php


namespace App\Http\Controllers\Recruit\Audit;


use App\Recruiter;
use BaseFile;
use App\Http\Controllers\Controller;
use FromId;
use Illuminate\Http\Request;

/**
 * 审核报名
 * Class AuditController
 * @package App\Http\Controllers\Recruit\Audit
 */
class AuditController extends Controller
{
    /**
     * 提交认证信息
     * @param Request $request
     * @return array
     */
    public function setIdentity(Request $request)
    {
        $userInfo = json_decode($request -> info,true);

        if(in_array('',$userInfo)){//判断是否有空值
            return ['msg'=>'err','code'=>'2','result'=>'信息不全'];
        }else{
            //验证身份证账号姓名是否正确
            $name = $userInfo['username'];   //真实姓名
            $idcard = $userInfo['idcard'];   //身份证号码

            $res = file_get_contents("http://op.juhe.cn/idcard/query?key=99b7d4903977231daa777402742c6946&idcard=".$idcard."&realname=".$name."");
            $result = json_decode($res,true);
            if($result['error_code'] == '210304'){   //身份证或者姓名错误
                return ['msg'=>'err','code'=>'10','result'=>'身份证或姓名格式错误'];
            }

            if($result['result']['res'] == '2'){    //  res为1 匹配   2 不匹配
                //不匹配
                return ['msg'=>'err','code'=>'9','result'=>'身份信息不匹配'];
            }

            if(!(preg_match("/^1[34578]\d{9}$/", $userInfo['phone']))){
                return ['msg'=>'err','code'=>'3','result'=>'手机号输入不正确'];
            }

            $data['created_at'] = time();
            $data['username']   = $name;
            $data['idcard']     = $idcard;
            $data['phone']      = $userInfo['phone'];
            $data['sex']        = $userInfo['sex'];

            $a = FromId::recruitSave($request->fromid);//将fromid存入数据库

            //将图片存入upload_audit中，并将图片路径存入数据库
            $img = json_decode($request -> img,true);//$request->img全部图片（二进制流形式）
            if(count($img) != 3){
                return ['msg'=>'err','code'=>3,'result'=>'图片信息不完整'];
            }
            foreach ($img as $k => $v){
                $image['contentType'] = 'img';
                $image[]  = $v;  //$v包含content和file
                $data[$k] = BaseFile::processing($image);
            }
            $res = Recruiter::where('token',$request->token) -> update($data);
            if($res){
                return ['msg'=>'ok', 'code'=>0,'result'=>'提交成功，等待审核！'];
            }else{
                return ['msg'=>'err','code'=>1,'result'=>'未知错误'];
            }
        }
    }

    
}