<!--_meta 作为公共模版分离出去-->
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="Bookmark" href="favicon.ico" >
<link rel="Shortcut Icon" href="favicon.ico" />
<!--[if lt IE 9]>
<script type="text/javascript" src="lib/html5.js"></script>
<script type="text/javascript" src="lib/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" type="text/css" href="/index/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/index/tatic/h-ui.admin/css/style.css" />
<!--[if IE 6]>
<script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
<script>DD_belatedPNG.fix('*');</script><![endif]-->
<!--/meta 作为公共模版分离出去-->

<title>新增群公告</title>
</head>
<body>
<article class="cl pd-20">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">公告内容：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<textarea value="" class="textarea" id="res" maxlength="100"  placeholder="说点什么.." onKeyUp="textarealength()"></textarea>
				<p class="textarea-numberbar"><em class="textarea-length">0</em>/100</p>
			</div>
		</div>
		<input type="hidden" id="gid" value="{{$groupid}}">
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<input class="btn btn-primary radius" onclick="submits()" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
			</div>
		</div>
</article>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script>
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script>
<script type="text/javascript" src="/index/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/jquery.validate.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/validate-methods.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/messages_zh.js"></script> 
<script type="text/javascript">
	function submits()
	{
		var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
		var res = $('#res').val();
		if(res.length > 100 || res.length <= 0){
            parent.layer.msg('请填写公告内容!');  //提示
		}
		var gid = $('#gid').val();
		$.get('/doaddgg',{res:res,gid:gid},function(data){
			if(data == '1'){
 				parent.location.reload(); //刷新父页面
                parent.layer.msg('发布成功!');  //提示
                parent.layer.close(index);   //关闭当前页面
			}else{
				parent.layer.msg('发布失败!');  //提示
			}
		})
	};
	function textarealength()
	{
		var length = $('.textarea').val();
		var a = length.length;
		console.log(a);
		if(a>=100){
			a = 100;
		}
		$('.textarea-length').html(a);
	}
	
</script> 
<!--/请在上方写此页面业务相关的脚本-->
</body>
</html>