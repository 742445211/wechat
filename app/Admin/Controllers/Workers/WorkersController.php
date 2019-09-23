<?php


namespace App\Admin\Controllers\Workers;


use App\Http\Controllers\Controller;
use App\Model\Workers;
use Encore\Admin\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class WorkersController extends Controller
{
    public function grid()
    {
        $grid = new Grid(new Workers);

        $grid->model()->orderBy('id','desc');
        $grid->column('id',__('ID'))->setAttributes(['style'=>'text-align:center;']);
        $grid->column('username',__('用户名'))->display(function (){
            return "<a href='/admin/workers/detail?id=$this->id'>{$this -> username}</a>";
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('sex','性别')->display(function ($sex){
            $text = $sex == 0 ? '男' : '女';
            return $text;
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('birthday',__('年龄'))->display(function ($birthday){
            $n_year = date('Y');
            $n_month= date('m');
            $n_day  = date('d');
            $year   = date('Y',strtotime($birthday));
            $month  = date('m',strtotime($birthday));
            $day    = date('d',strtotime($birthday));
            if($n_month >= $month && $n_day >= $day){
                return $n_year - $year + 1;
            }else{
                return $n_year - $year;
            }
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('phone','联系方式')->setAttributes(['style'=>'text-align:center;']);
        $grid->column('idcard',__('身份证号'))->setAttributes(['style'=>'text-align:center;']);
        $grid->column('works','所在岗位')->display(function ($works){
            foreach ($works as $v){
                if($v != []){
                    return "<a href='/admin/works/show?id={$v['id']}'>{$v['title']}</a>";
                }
            }
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('created_at',__('注册时间'))->setAttributes(['style'=>'text-align:center;']);
        $grid->disableActions();
        $grid->disableCreateButton();
//        Admin::style('a {color:#1f1f1f;text-decoration:underline}');

        return $grid;
    }

    public function index(Content $content)
    {
        return $content
            -> title('C端用户列表')
            -> description('列表')
            -> body($this -> grid());
    }

    public function show(Request $request, Content $content)
    {
        return $content->header('用户详情')
            ->description('详情')
            ->body(\Encore\Admin\Facades\Admin::show(Workers::findOrFail($request->id),function (Show $show){
                $show->id('ID');
                $show->header('头像')->image();
                $show->username('姓名');
                $show->sex('性别')->as(function ($sex){
                    $sex = $sex == 0 ? '男' : '女';
                    return $sex;
                });
                $show->birthday('年龄')->as(function ($birthday){
                    $n_year = date('Y');
                    $n_month= date('m');
                    $n_day  = date('d');
                    $year   = date('Y',strtotime($birthday));
                    $month  = date('m',strtotime($birthday));
                    $day    = date('d',strtotime($birthday));
                    if($n_month >= $month && $n_day >= $day){
                        return $n_year - $year + 1;
                    }else{
                        return $n_year - $year;
                    }
                });
                $show->phone('联系电话');
                $show->idcard('身份证号');
                $show->bank('开户行');
                $show->bank_number('银行卡号');
            }));
    }
}