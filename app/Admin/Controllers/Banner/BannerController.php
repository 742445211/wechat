<?php


namespace App\Admin\Controllers\Banner;


use App\Facades\BaseFile;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use Encore\Admin\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;

class BannerController extends Controller
{
    protected $title = '轮播图管理';

    protected function grid()
    {
        $grid = new Grid(new Banner);

        //$grid->model()->where('status',0);
        $grid->model()->orderBy('level','desc');
        $grid->column('id', __('id'))->setAttributes(['style'=>'text-align:center;']);
        $grid->column('level',__('排序'))->setAttributes(['style'=>'text-align:center;']);
        $grid->column('image_path',__('图片'))->lightbox()->setAttributes(['style'=>'text-align:center;']);
        $grid->column('status',__('图片状态'))
            ->display(function ($status){
                $text = $status == 0 ? '使用中' : '已下架';
                $color = $status == 0 ? 'red' : 'grey';
                return "<span class=\"badge bg-$color\">$text</span>";
            });
        $grid->column('created_at', __('创建时间'));
        $grid->actions(function ($actions){
            $actions -> disableDelete();
            $actions -> disableView();
            $actions -> add(new DeleteBanner);
        });
        $grid->filter(function ($filter){
            $filter->disableIdFilter();
            $filter->equal('status')->radio([
                ''   => '全部',
                0    => '使用中',
                1    => '已下架',
            ]);
        });
        //$grid->picture()->gallery();
        $grid->paginate(10);
        Admin::style('.table>tbody>tr>td {vertical-align: middle;} a {color:#1f1f1f;text-decoration:underline}');

        return $grid;
    }

    protected function form()
    {
        $form = new Form(new Banner);

        $form->display('id');
        $form->image('image_path','图片');
        $form->number('level','排序')->default(1)->max(99);
        $form->display('created_at','上传时间');
        $form->tools(function (Form\Tools $tools){
            $tools->disableList();
            $tools->disableDelete();
            $tools->disableView();
        });
        $form->footer(function ($footer){
            $footer->disableViewCheck();
            $footer->disableEditingCheck();
            $footer->disableCreatingCheck();
        });

        return $form;
    }

    public function index(Content $content)
    {
        return $content
            -> title('轮播图管理')
            -> description('列表')
            -> body($this -> grid());
    }

    public function edit($id)
    {
        return \Encore\Admin\Facades\Admin::content(function (Content $content) use($id){
            $content -> title('轮播图编辑');
            $content -> description('编辑');
            $content -> body($this -> form() -> edit($id));
        });
    }

    public function create()
    {
        return \Encore\Admin\Facades\Admin::content(function (Content $content){
            $content -> title('新增轮播图');
            $content -> description('新增');
            $content -> body($this -> form());
        });
    }

    /**
     * 上传banner
     * @param Request $request
     */
    public function store(Request $request)
    {
        if($request -> file('image_path') != null){
            $data = [
                'image_path'       => BaseFile::processing(['contentType' => 'file','content' => $request -> file('image_path')]),
                'level'            => $request -> level,
                'created_at'       => date('Y-m-d H:i:s',time())
            ];
            $res = Banner::insert($data);
            if($res) return admin_success('success', '添加成功');
            return admin_error('error', '添加失败！请联系管理员');
        }
    }

    /**
     * 修改轮播图
     * @param $id
     * @param Request $request
     */
    public function update($id, Request $request)
    {
        if(empty($request -> file())){
            $data['level'] = $request -> level;
        }else{
            $data['image_path'] = BaseFile::processing(['contentType' => 'file','content' => $request -> file('image_path')]);
            $data['level']      = $request -> level;
        }
        $res = Banner::where('id',$id) -> update($data);
        if($res) return admin_success('success', '更新成功');
        return admin_error('error', '更新失败！请联系管理员');
    }

    public function show($id, Content $content)
    {
        return $content->header('轮播图展示')
            ->description('详情')
            ->body(\Encore\Admin\Facades\Admin::show(Banner::findOrFail($id), function (Show $show) {

                $show->id('ID');
                $show->image_path('图片')->image();
                $show->level('排序');
                $show->created_at('发布时间');
            }));
    }
}