<?php


namespace App\Http\Controllers\Admin\Images;


use App\Facades\BaseFile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $res = BaseFile::processing(['contentType'=>'file','content'=>$request -> file('image')]);
        $num = $request -> num;
        return ['url' => $res,'num' => $num];
    }
}