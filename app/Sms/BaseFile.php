<?php


namespace App\Sms;


use Intervention\Image\ImageManager;

class BaseFile
{
    /**
     * 网站根网址
     * @var string
     */
    protected $www = 'https://www.xiaoshetong.cn';

    /**
     * 二维码储存目录
     * @var string
     */
    protected $QRCode = 'upload_qrcode';

    /**
     *传入数据处理
     * @param $data
     * @return mixed
     */
    public function processing($data){
        //$data = json_decode($data,true);
        switch ($data['contentType']){
            case 'text':
                return $data;
                break;
            case 'img':
                $file = $data['content'];
                $arr = explode('.', $data['file']);
                $ext = array_pop($arr);
                $filename = uniqid() . '.' . $ext;
                $src = $this->saveBase($file, 'img', $filename);
                //$data['content'] = $src;
                return $src;
                break;
            case 'audio':
                $file = $data['content'];
                $arr = explode('.', $data['file']);
                $ext = array_pop($arr);
                $filename = uniqid() . '.' . $ext;
                $src = $this->saveBase($file, 'audio', $filename);
                //$data['content'] = $src;
                return $src;
                break;
            case 'buffer':
                $date = DIRECTORY_SEPARATOR . $this->QRCode . DIRECTORY_SEPARATOR . date('Y-m-d') . DIRECTORY_SEPARATOR ;
                $dir = base_path('public');
                $filename =uniqid() . '.' . 'png';
                BaseFile::makeDir($dir . $date);
                if(file_put_contents($dir . $date . $filename, $data['content'])) return $this->www . $date . $filename;
                return  '添加失败！';
                break;
            case 'file':
                $file = $data['content'];
                $entension = $file -> getClientOriginalExtension(); //上传文件的后缀.
                $newName = str_random('19');
                $dir = '/upload_auth/' . date('Y-m-d') . DIRECTORY_SEPARATOR;
                $path = $newName.'.'.$entension;
                $info = $file -> move(base_path('public') . $dir,$path);
                if($info){
                    $img = new ImageManager();
                    $img -> make(base_path('public') . $dir . $path) -> save(base_path('public').$dir.$newName.'_thumbnail.'.$entension,60);
                    return 'https://www.xiaoshetong.cn' . $dir .$path;   //路径
                }
                return false;
                break;
            case 'logo':
                $file = $data['content'];
                $entension = $file -> getClientOriginalExtension(); //上传文件的后缀.
                $newName = str_random('19');
                $dir = '/upload_logo/' . date('Y-m-d') . DIRECTORY_SEPARATOR;
                $path = $newName.'.'.$entension;
                $info = $file -> move(base_path('public') . $dir,$path);
                if($info){
                    $img = new ImageManager();
                    $img = $img -> make(base_path('public') . $dir . $path);
                    //if($img -> filesize() > 100000){
                    $img -> save(base_path('public').$dir.$newName.'_thumbnail.'.$entension,60);
                    //}
                    return 'https://www.xiaoshetong.cn' . $dir .$path;   //路径
                }
                return false;
                break;
            default:
                return $data;
        }
    }

    /**
     * 检查目录是否存在，不存在则创建一个
     * @param $path
     */
    private function makeDir($path){
        if(!is_dir($path)){
            mkdir($path, 0777, true);
        }
    }

    /**
     * 将base64流转文件
     * @param $base64
     * @type $type
     * @param $filename
     * @return string
     */
    public function saveBase($base64, $type, $filename){
        $regular = '';
        if($type == 'img'){
            $regular = '/^(data:\s*image\/(\w+);base64,)/';
        }elseif($type == 'audio'){
            $regular = '/^(data:\s*audio\/(\w+);base64,)/';
        }
        $a = base_path('public');
        $dir = '/upload_msg/' . date('Ymd') . '/';
        $this->makeDir($a . $dir);
        $www = 'https://www.xiaoshetong.cn';
        $src = $dir . $filename;
        if(preg_match($regular, $base64, $result)){
            if(file_put_contents(($a . $src), base64_decode(str_replace($result[1],'',$base64)))){
                return $www . $src;
            }
        }
    }

    /**
     * 删除服务器上的文件
     * @param $www          文件的网络路径（https://www.xiaoshetong.cn/upload_auth/2019-07-08/d2LxFy4PtoeAPP83hwe.jpg）
     * @return bool
     */
    public function unlinkFile($www){
        //去掉路径最前面的网址
        $www = substr($www['header'],26);
        //通过绝对路径删除文件
        $result = unlink(base_path('public').$www);
        return $result;
    }
}