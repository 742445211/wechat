<?php


namespace App\Admin\Controllers\Works;


use App\Http\Controllers\Controller;
use App\Model\Works;
use Encore\Admin\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class WorksController extends Controller
{
    public function grid()
    {
        $grid = new Grid(new Works);

        $grid->model()->orderBy('id','desc');
        $grid->column('id',__('ID'));
        $grid->column(__('工作名称'))->display(function (){
            return "<a href='/admin/works/detail?id=$this->id'>{$this -> title}</a>";
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('recruiters.username',__('招聘者'));
        $grid->column(__('已招人数/招聘人数'))->display(function (){
            $recruitment = $this -> recruitment == null ? 0 : $this -> recruitment;
            $number = $this -> number == null ? 0 : $this->number;
            return $recruitment . '/' . $number;
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('created_at',__('发布时间'));
        $grid->column('validity_time',__('截止日期'));
        $grid->column('status',__('工作状态'))->display(function ($status){
            if($status == 0){
                $text = '上架中';
                $color= 'green';
            }elseif ($status == 1){
                $text = '下架暂停招聘';
                $color= 'red';
            }elseif ($status == 2){
                $text = '工作结束';
                $color= 'grey';
            }
            return "<span class=\"badge bg-$color\">$text</span>";
        });
        $grid->actions(function ($actions){
            $actions->disableDelete();
            $actions->disableEdit();
            $actions->disableView();
            $actions -> add(new ChangeWork);
            $actions -> add(new Recovery);
        });
        $grid->filter(function ($filter){
            $filter->disableIdFilter();
            $filter->equal('status')->radio([
                ''   => '全部',
                0    => '上架中',
                1    => '下架中(工作中)',
                2    => '工作结束',
            ]);
            $filter->equal('cate');
        });
//        Admin::style(
//            'a {color:#1f1f1f;text-decoration:underline}
//            .pagination>.active>span {background-color: #FC7501;border-color: #FC7501;}
//            .pagination>.active>span:hover {background-color: #FC7501;border-color: #FC7501;}'
//        );

        return $grid;
    }

    public function show(Request $request, Content $content)
    {
        $id = $request -> id;
        return $content->header('工作详情')
            ->description('详情')
            ->body(\Encore\Admin\Facades\Admin::show(Works::findOrFail($id), function (Show $show){
                $cycle = ['小时','天','月'];
                $show->panel()
                    ->style('danger');
                $show->title('工作标题');
                $show->number('已招人数/共招人数')->as(function (){
                    return $this->recruitment . '/' . $this->number;
                });
                $show->age('年龄要求');
                $show->wages('薪资')->as(function () use($cycle){
                    return $this->wages . '元/' . $cycle[$this->cycle];
                });
                $show->address('工作地址');
                $show->welfare('工作福利')->unescape()->as(function ($welfare){
                    $welfare = explode(',',$welfare);
                    $html = '';
                    foreach ($welfare as $value){
                        $html .= "<span style='height: 23px;background-color: #FF6900;border-radius: 2px;margin-right: 10px;color: #FFFFFF;display: inline-block;padding: 2px;'>$value</span>";
                    }
                    return $html;
                });
                $show->workImage('工作环境')->as(function ($image){
                    $path = [];
                    foreach ($image as $value){
                        array_push($path,$value->work_image);
                    }
                    return $path;
                })->image();
                $show->describe('职位描述')->unescape()->as(function ($query){
                    return $query->content;
                });

                $show->recruiters('职位发布者')->unescape()->as(function ($recruiter){
                    $company = $recruiter->is_company == 0 ? '个人' : '公司';
                    $html = '';
                    $html .= "<div style='color: #FF6900;'>";
                    $html .= "<a href='/admin/recruiter/show?id=$recruiter->id' style='color: #FF6900'>";
                    $html .= "<img src='$recruiter->header' height='65px' width='65px' style='border-radius: 50%'>";
                    $html .= "<span style='margin-left: 20px;'>$recruiter->username</span>";
                    $html .= "<span style='margin-left: 10px;height: 15px'> | </span>";
                    $html .= "<span style='margin-left: 10px'>$company</span>";
                    $html .= "</a>";
                    $html .= "</div>";
                    return $html;
                });
                $show->workers('已应聘者')->unescape()->as(function ($workers){
                    if($workers->toArray() == []){
                        return "<span>暂无应聘者</span>";
                    }else{
                        $html = "<div style='display: flex'>";
                        foreach ($workers as $value){
                            $html .= "<div style='width: 72px;height: 90px;flex-wrap: wrap;text-align: center'>";
                            $html .=    "<a href='/admin/workers/show?id=$value->id' style='color: #FF6900'>";
                            $html .=        "<img src='$value->header' style='width: 60px;height: 60px;border-radius: 50%;'>";
                            $html .=        "<span>$value->username</span>";
                            $html .=    "</a>";
                            $html .= "</div>";
                        }
                        $html .= "</div>";
                        return $html;
                    }
                });
            }));
    }

    public function index(Content $content)
    {
        return $content
            -> title('工作列表')
            -> description('列表')
            -> body($this -> grid());
    }
}