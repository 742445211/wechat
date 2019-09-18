<?php


namespace App\Admin\Controllers\Recruiter;


use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class FailurePass extends RowAction
{
    public $name = '不通过';

    public function handle(Model $model)
    {
        $res = $model -> update(['status'=>2]);
        if($res) return $this->response()->success('未通过')->refresh();
    }

    public function dialog()
    {
        $this -> confirm('确定不通过？');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-post">不通过</a>
HTML;
    }
}