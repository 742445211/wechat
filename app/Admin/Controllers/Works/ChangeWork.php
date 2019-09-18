<?php


namespace App\Admin\Controllers\Works;


use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class ChangeWork extends RowAction
{
    public $name = '下架';

    public function handle(Model $model)
    {
        $res = $model -> update(['status'=>1]);
        if($res) return $this->response()->success('已下架')->refresh();
    }

    public function dialog()
    {
        $this -> confirm('确定下架？');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-post">下架</a>
HTML;
    }
}