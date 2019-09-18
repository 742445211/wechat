<?php


namespace App\Http\Controllers\Admin\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Excel;
class ExcelController extends Controller
{

    //导出工作信息
    public function signExport(Request $request, $groupid)
    {
        $groupid = (int)$groupid;
        $adminid = $request->session()->get('userid');
        $workid = DB::table('workgroup') -> where('group_id',$groupid) -> value('workid');//查询当前用户发布的工作ID
        $data = DB::table('sign')
                -> join('homeuser','sign.userid','=','homeuser.id')
                -> join('price_detail','sign.price_detail','=','price_detail.id')
                -> join('work','sign.workid','=','work.id')
                -> select('sign.*','homeuser.nicename','homeuser.phone','homeuser.idcard','price_detail.price_status','price_detail.allprice','work.title','work.startdate','work.enddate','work.groupinfo')
                -> where('sign.workid',$workid)
                -> get();//查询当前用户的有效兼职信息
        $time = DB::table('time') -> where('adminid',$adminid) -> where('status',1) -> get();//查询当前用户兼职计时类型
        foreach ($time as $v){
            $types[$v->id] = $v->type;
        }
        $cellData[] = ['序号','姓名','电话','身份证号','项目标签','服务公司','工作开始时间','工作结束时间','签到时间','签退时间','工资计算标准','工资发放','发放金额','总计工作时间'];//表头，定义每一列存啥
        $i = 1;
        foreach ($data as $v){
            $priceunit    = explode('/',$v->priceunit);
            $priceunit[1] = $types[$priceunit[1]];
            $priceunit    = implode('/',$priceunit);//把工资时间汉化
            if($v->price_status == 1){
                $isok = '已发放';
            }else {
                $isok = '未发放';
            }
            $date   = floor((strtotime($v->signout_time)-strtotime($v->signin_time))/86400);
            $hour   = floor((strtotime($v->signout_time)-strtotime($v->signin_time))%86400/3600);
            $minute = floor((strtotime($v->signout_time)-strtotime($v->signin_time))%86400/60);
            $cellData[] = [$i, $v->nicename, $v->phone, ' '.$v->idcard, $v->title, $v->groupinfo, $v->startdate, $v->enddate, $v->signin_time, $v->signout_time, $priceunit, $isok, $v->allprice, $date . '天/' . $hour . '小时/' . $minute . '分钟'];//拼接表中的具体内容
            $i++;
        }

        $name = iconv('UTF-8', 'GBK', "{$cellData[1][4]}情况表");//设置编码格式和表名

        Excel::create($name,function($excel) use ($cellData){

            $excel->sheet('score', function($sheet) use ($cellData){
                $tot = count($cellData) ;
                $sheet->setWidth(array(
                    'A'     =>  6,
                    'B'     =>  15,
                    'C'     =>  15,
                    'D'     =>  24,
                    'E'     =>  16,
                    'F'     =>  20,
                    'G'     =>  15,
                    'H'     =>  20,
                    'I'     =>  20,
                    'J'     =>  20,
                    'K'     =>  14,
                    'L'     =>  14,
                    'M'     =>  14,
                    'N'     =>  20,
                ))->rows($cellData)->setFontSize(14);

                // 数据内容主题 左对齐
                $sheet->cells('A1:N'.$tot, function($cells) {
                    $cells->setAlignment('center');
                });
                // 菜单 样式
                $sheet->cells('A1:N1', function($cells) {
                    $cells->setAlignment('center');
                    $cells->setFontWeight('bold');
                });
                // 总金额 高亮显示
                $sheet->cells('M2:M'.$tot, function($cells) {
                    $cells->setFontColor('#33FC14');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize(14);
                });

            });

        })->export('xls');//导出表格

    }

    
}