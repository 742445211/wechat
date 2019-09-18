<?php


namespace App\Admin\Controllers\Recruiter;


use App\Facades\FromId;
use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class PassAuth extends RowAction
{
    public $name = '通过';

    public function handle(Model $model)
    {
        $res = $model -> update(['status'=>1]);
        $result = $model -> select('id','openid','username') -> first() -> toArray();
        $data = [
            "touser"        => $result['openid'],
            "template_id"   => 'GHkNYlTxzLlMV538eCWRmPn_4nyIDXkTQD-Rwop6GY8',
            "page"          => 'pages/index/index',
            "data"          => [
                "keyword1"      => ["value" => date('Y-m-d',time())],
                "keyword2"      => ["value" => $result['username']],
                "keyword3"      => ["value" => '您的申请已通过']
            ]
            //"emphasis_keyword" => ''
        ];
        FromId::sendRecruitFromid($data,$result['id']);
        if($res) return $this->response()->success('已通过')->refresh();
    }

    public function dialog()
    {
        $this -> confirm('确定通过？');
    }

    public function html()
    {
        return <<<HTML
        <a class="btn btn-sm btn-default import-post">通过</a>
HTML;
    }
}