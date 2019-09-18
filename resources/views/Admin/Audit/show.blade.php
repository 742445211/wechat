<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link href="/index/static/h-ui/css/H-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="/index/static/h-ui.admin/css/H-ui.admin.css" rel="stylesheet" type="text/css" />
    <link href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" rel="stylesheet" type="text/css" />
    <title>企业用户查看</title>
</head>
<body>
<div class="cl pd-20" style=" background-color:#5bacb6">
    <img class="avatar size-XL l" src="{{isset($data -> header)?$data -> header:'/index/default_header.jpg'}}">
    <dl style="margin-left:80px; color:#fff">
        <dt><span class="f-18">{{$data->username}}</span></dt>
        {{--<dd class="pt-10 f-12" style="margin-left:0">{{$data->syn}}</dd>--}}
    </dl>
</div>
<div class="pd-20">
    <table class="table">
        <tbody>
        <tr>
            <th class="text-r">身份证：</th>
            <td>{{$data->idcard}}</td>
        </tr>

        <tr>
            <th class="text-r" width="80">性别：</th>
            <td>{{isset($data->sex)?$sex[$data->sex]:$sex['2']}}</td>
        </tr>

        <tr>
            <th class="text-r">电话：</th>
            <td>{{$data->phone}}</td>
        </tr>

        {{-- <tr>
           <th class="text-r">职业：</th>
           <td>
           @if($data -> type == 1)
           学生
             @else
             非学生
             @endif
             </td>
         </tr>--}}

        {{--<tr>
            <th class="text-r">学校：</th>
            <td>{{$data->school}}</td>
        </tr>

        <tr>
            <th class="text-r">专业：</th>
            <td>{{$data->spec}}</td>
        </tr>--}}


        {{--<tr>
            <th class="text-r">工作类型：</th>
            <td>{{$data->like_work_type}}</td>
        </tr>--}}
        <tr>
            <th class="text-r">公司名称：</th>
            <td>{{$data->company}}</td>
        </tr>


        {{--<tr>
            <th class="text-r">工作经历：</th>
            <td>{{$data->workdetail}}</td>
        </tr>--}}
        {{--@if($account)
            <tr>
                <th class="text-r">账户类型：</th>
                <td>{{$type}}</td>
            </tr>
            <tr>
                <th class="text-r">账户：</th>
                <td>{{$account -> num}} / {{$account -> name}}</td>
            </tr>
            <tr>
                <th class="text-r">账户机构：</th>
                <td>{{$account -> a_in}}</td>
            </tr>
        @else
        @endif--}}


        <tr>
            <th class="text-r">注册时间：</th>
            <td>{{$data->created_at}}</td>
        </tr>
        <tr>
            <th class="text-r">账号状态：</th>
            <td>
                @if($data->status==1)
                    已审核
                @elseif($data->status == 0)
                    未审核
                @else
                    已删除
                @endif
            </td>
        </tr>
        <tr>
            <th>正面</th>
            <td>
                <img class="l" style='width: 189px;height: 189px' src="{{$data->positive}}" alt="">
            </td>
        </tr>
        <tr>
            <th>背面</th>
            <td>
                <img class="l" style='width: 189px;height: 189px' src="{{$data->back}}" alt="">
            </td>
        </tr>
        <tr>
            <th>手持</th>
            <td>
                <img class="l" style='width: 189px;height: 189px' src="{{$data->hand}}" alt="">
            </td>
        </tr>
        </tbody>
    </table>
</div>
<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script>
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script>
<!--/_footer /作为公共模版分离出去-->
<script>
    $(".table tbody tr td img").click(function(e){
        layer.photos({ photos: {"data": [{"src": e.target.src}]} ,shift: 5});
    });
</script>
</body>
</html>