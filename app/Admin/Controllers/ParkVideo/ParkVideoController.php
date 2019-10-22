<?php


namespace App\Admin\Controllers\ParkVideo;


use App\Http\Controllers\Controller;
use App\Model\ParkVideo;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ParkVideoController extends Controller
{
    public function grid()
    {
        $grid = new Grid(new ParkVideo);

        $grid->column('id',__('ID'));
        $grid->column('name',__('园区名'));
        $grid->column('path',__('文件路径'));
        $grid->column('created_at',__('上传时间'));
        $grid->column('updated_at',__('更新时间'));

        return $grid;
    }

    public function index(Content $content)
    {
        return $content
            -> title('视频展示')
            -> description('列表')
            -> body($this -> grid());
    }

    public function from()
    {
        $form = new Form(new ParkVideo);

        $form->display('id');
        $form->text('园区名');

    }
}