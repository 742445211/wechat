<?php


namespace App\Admin\Controllers\Census;


use App\Http\Controllers\Controller;
use Encore\Admin\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Redis;

class CensusController extends Controller
{
    protected $title = '数据统计';

    public function index(Content $content)
    {
        $redis = Redis::connection('census');
        $data = [
            'work'          => $redis->get('work'),
            'recruitment'   => $redis->get('recruitment'),
            'atwork'        => $redis->get('atwork'),
            'finish'        => $redis->get('finish'),
            'registered'    => $redis->get('registered'),
            'onthejob'      => $redis->get('onthejob'),
            'business'      => $redis->get('business'),
            'certified'     => $redis->get('certified'),
            'uncertified'   => $redis->get('uncertified'),
        ];

        return $content
            ->header('Chartjs')
            ->body(new Box('统计', view('Census.chartjs',$data)));
    }
}