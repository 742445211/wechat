<?php


namespace App\Admin\Controllers\Works;


use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Recovery extends RowAction
{
    public $name = '恢复';

    public function handle(Model $model)
    {
        $res = $model -> update(['status'=>0]);
        if($res) return $this->response()->success('已恢复')->refresh();
    }

    public function dialog()
    {
        $this -> confirm('确定恢复？');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-post">恢复</a>
HTML;
    }
}