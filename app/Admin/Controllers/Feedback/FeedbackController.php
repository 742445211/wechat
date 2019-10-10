<?php


namespace App\Admin\Controllers\Feedback;


use App\Http\Controllers\Controller;
use App\Model\Feedback;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class FeedbackController extends Controller
{
    public function grid()
    {
        $grid = new Grid(new Feedback);

        $grid->model()->orderBy('id','desc');
        $grid->column('id',__('ID'))->setAttributes(['style'=>'text-align:center;']);
        $grid->column('worker',__('投诉用户'))->display(function ($worker){
            return "<a href='/admin/workers/show?id={$worker['id']}'>{$worker['username']}</a>";
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('content',__('投诉内容'))->setAttributes(['style'=>'text-align:center;']);
        $grid->column('created_at',__('投诉时间'))->setAttributes(['style'=>'text-align:center;']);

        return $grid;
    }

    public function index(Content $content)
    {
        return $content
            -> title('投诉列表')
            -> description('列表')
            -> body($this -> grid());
    }
}