<?php


namespace App\Admin\Controllers\Feedback;


use App\Http\Controllers\Controller;
use App\Model\Feedback;
use Encore\Admin\Grid;

class FeedbackController extends Controller
{
    public function grid()
    {
        $grid = new Grid(new Feedback);

        $grid->model()->orderBy('id','desc');
        $grid->column('id',__('ID'))->setAttributes(['style'=>'text-align:center;']);
        

        return $grid;
    }
}