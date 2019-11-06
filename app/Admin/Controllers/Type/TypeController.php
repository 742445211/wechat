<?php


namespace App\Admin\Controllers\Type;


use App\Http\Controllers\Controller;
use App\Model\Type;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Form;

class TypeController extends Controller
{
    use HasResourceActions;

    protected function treeView()
    {
        return Type::tree(function (Tree $tree){
            $tree->branch(function ($branch){
                $payload = "&nbsp;<strong>{$branch['title']}</strong>";
                if(!isset($branch['children'])){
                    $payload .= "&nbsp;&nbsp;&nbsp;<a href='/admin/works?&type=" . $branch['id'] . "' class=\"dd-nodrag\">点击查看此类工作</a>";
                }
                return $payload;
            });
        });
    }

    public function index(Content $content)
    {
        return $content
            -> title('分类列表')
            -> row(function (Row $row){
                $row->column(6, $this->treeView()->render());

                $row->column(6,function (Column $column){
                    $form = new Form(new Type);
                    $form->select('pid','选择父级分类')->options(Type::selectOptions());
                    $form->text('title','标题')->rules('required');

                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });
    }

    public function form()
    {
        $form = new \Encore\Admin\Form(new Type);

        $form->display('id','ID');
        $form->select('pid','父级分类')->options(Type::selectOptions());
        $form->text('title','标题')->rules('required');
        $form->display('created_at','创建时间');
        $form->display('updated_at','更新时间');

        return $form;
    }

    public function edit($id, Content $content)
    {
        return $content
            ->title('分类管理')
            ->description('分类详情')
            ->row($this->form()->edit($id));
    }
}