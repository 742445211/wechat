<?php


namespace App\Admin\Controllers\Recruiter;


use App\Http\Controllers\Controller;
use App\Model\Recruiters;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\Request;

class RecruiterController extends Controller
{
    public function grid()
    {
        $grid = new Grid(new Recruiters);

        $grid->column('id',__('ID'));
        $grid->column('username',__('用户名'))->display(function (){
            return "<a href='/admin/recruiter/show?id=$this->id' style='color:#61A2D3;'>{$this -> username}</a>";
        })->setAttributes(['style'=>'text-align:center;']);
//        $grid->column('comment',__('综合分数'))->display(function ($comment){
//            if($comment != []){
//                $count = 0;
//                foreach ($comment as $value){
//                    $count += $value['synthesize'];
//                }
//                return $count / count($comment);
//            }else{
//                return 0;
//            }
//        })->setAttributes(['style'=>'text-align:center;']);
//        $grid->column('comment',__('评论数'))->display(function ($id){
//            return count($id);
//        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('is_company',__('所属'))->display(function ($is){
            $text = $is == 0 ? '个人' : '企业';
            $color = $is == 0 ? 'blue' : 'yellow';
            return "<span class=\"badge bg-grey\">$text</span>";
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('status','审核状态')->display(function ($status){
            if($status == 0){
                return "<span style='color: #666666'>待审核</span>";
            }elseif ($status == 1){
                return "<span style='color: #46BB36'>审核通过</span>";
            }elseif ($status == 2){
                return "<span style='color: #FC7201'>审核未通过</span>";
            }
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->column('works',__('最近发布时间'))->pluck('created_at')->display(function ($works){
            if(count($works) == 0){
                return '无';
            }else{
                return $works[count($works)-1];
            }
        })->setAttributes(['style'=>'text-align:center;']);
        $grid->disableCreateButton();
        //$grid->disableActions();
        $grid->actions(function ($actions){
            $actions -> disableDelete();
            $actions -> disableView();
            $actions -> disableEdit();
            $actions -> add(new PassAuth);
            $actions -> add(new FailurePass);
        });
        \Encore\Admin\Admin::style(
            '.content-wrapper, .right-side {background-color : #F8F8F8} 
            a.dropdown-toggle {color : #969696}
            .pagination>.active>span {background-color: #FC7501;border-color: #FC7501;}
            .pagination>.active>span:hover {background-color: #FC7501;border-color: #FC7501;}
            .btn-dropbox,.btn-instagram {color : #808080;background-color : #ECECEC}
            .btn-dropbox:hover,.btn-instagram:hover {color : #000000;background-color : rgb(238,238,238)}
            .btn-twitter {color : #808080;background-color : #ECECEC}
            .btn-twitter:hover {color : #000000;background-color : rgb(238,238,238)}
            .btn-dropbox.active {color : #ffffff;background-color : #FC7501}
            .btn-twitter:active {color : #ffffff;background-color : #FC7501}
            .open>.dropdown-toggle.btn-instagram {color : #ffffff;background-color : #FC7501}'
        );

        return $grid;
    }

    public function index(Content $content)
    {
        return $content
            -> title('b端用户列表')
            -> description('列表')
            -> body($this->grid());
    }

    public function show(Request $request, Content $content)
    {
        return $content->header('用户详情')
            -> description('详情')
            -> body(Admin::show(Recruiters::findOrFail($request->id),function (Show $show){
                $show->panel()->tools(function ($tools){
                    $tools->disableEdit();
                });
                $show->id('ID');
                $show->header('头像')->image(90,90);
                $show->username('姓名');
                $show->idcard('身份证号');
                $show->sex('性别')->as(function ($sex){
                    return $sex == 0 ? '男' : '女';
                });
                $show->is_company('类别')->as(function ($is_company){
                    return $is_company == 0 ? '个人' : $this->company;
                });
                $show->license('营业执照')->image();
                $show->position('身份证正面')->image();
                $show->back('身份证背面')->image();
            }));
    }
}