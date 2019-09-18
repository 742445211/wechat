<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<!--[if lt IE 9]>
<script type="text/javascript" src="lib/html5.js"></script>
<script type="text/javascript" src="lib/respond.min.js"></script>
<![endif]-->
<link href="/index/static/h-ui/css/H-ui.min.css" rel="stylesheet" type="text/css" />
<link href="/index/static/h-ui.admin/css/H-ui.admin.css" rel="stylesheet" type="text/css" />
<link href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/index/layui/css/layui.css">
<!--[if IE 6]>
<script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script><![endif]-->
<title>兼职详情查看</title>
</head>
<body>
<div class="cl pd-20" style=" background-color:#5bacb6">
  <img class="avatar size-XL l" src="{{$data -> header}}">
  <dl style="margin-left:80px; color:#fff">
    <dt><span class="f-18">{{$data->title}}</span></dt>
    <dd class="pt-10 f-12" style="margin-left:0">发布人: 
    	<?php
            if($data->pid){
                echo DB::table('adminuser') -> where('id','=',$data->pid) -> value('username');
            }else{
                echo DB::table('recruiter') -> where('id','=',$data->rid) -> value('username');
            }
		?>
		</dd>
  </dl>
</div>

<div class="pd-20">
  <table class="table">
    <tbody>
      <tr>
        <th class="text-r" width="80">报酬：</th>
		  <td><font color="red">{{$data->price}}</font> / 天</td>
      </tr>
      <tr>
        <th class="text-r">联系人：</th>
        <td>{{$data->contacts}}</td>
      </tr>
      <tr>
        <th class="text-r">电话：</th>
        <td>{{$data->phone}}</td>
      </tr>
      <tr>
        <th class="text-r">公司：</th>
        <td>
        	{{$data -> groupinfo}}
		  </td>
      </tr>
      <tr>
        <th class="text-r">工作地址：</th>
        <td>{{$data->address}}</td>
      </tr>
      <tr>
        <th class="text-r">发布时间：</th>
        <td>{{date('Y-m-d H:i:s',$data->addtime)}}</td>
      </tr>
       <tr>
        <th class="text-r">详情：</th>
        <td>{{$data->content}}</td>
      </tr>
       <tr>
        <th class="text-r">报名人数：</th>
		   <td>
		   		<?php
          if($data -> cplt == ''){
			   		echo '0';
          }else{
            $num = count(explode(',',rtrim($data->cplt,',')));
             echo "<a>$num</a>";
          }
			   ?>
		  </td>
      </tr>
      </tr>
       <tr>
        <th class="text-r">浏览量：</th>
        <td>{{$data->views}}</td>
      </tr>
      </tr>
       <tr>
        <th class="text-r">收藏量：</th>
        <td>{{$data->likenum}}</td>
      </tr>
      
      <tr>
        <th class="text-r">发布状态：</th>
        <td>
		     @if($data->status==1)
			<font color="green">发布中</font>
			 @elseif($data->status == 0)
			 下架
			 @else
			 已删除
			 @endif
		  </td>
      </tr>
      <tr>
          <th class="text-r">展示图：</th>
          <td>
              @foreach($path as $v)
              <img class="size-XXXL l" style="width: 200px;" src="{{$v->imgpath}}" alt="">
              @endforeach
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
<script type="text/javascript" src="/index/layui/layui.all.js"></script>
<!--/_footer /作为公共模版分离出去-->
<script>
    $(".table tbody tr td img").click(function(e){
        layer.photos({ photos: {"data": [{"src": e.target.src}]} ,shift: 5});
    });
</script>
</body>
</html>