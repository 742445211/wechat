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
<title>管理员修改</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" type="text/css" href="/index/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />

<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/style.css" />
</head>
<body>
<article class="cl pd-20">
	<form  class="form form-horizontal">
	<input type="hidden" id="id" value="{{$info->id}}">
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">用户名：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" value="{{$info->username}}  (用户名不提供修改)" placeholder="" id="adminName" disabled>
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">密码：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="password" class="input-text" id="passwd" placeholder="密码不改则留空" id="adminName" name="password">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">手机：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" autocomplete="off" id="phone" placeholder="必填项" value="{{$info -> phone}}" name="phone" required="required">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">公司：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" autocomplete="off" id="group" placeholder="必填项" value="{{$info -> group}}"  name="group" required="required">
			</div>
		</div>
		
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">权限：</label>
			<div class="formControls col-xs-8 col-sm-9"> <span class="select-box" style="width:150px;">
				<select class="select" id="level" name="level" size="1" required="required">
					@foreach($level as $row)
					<option value="{{$row->id}}" @if($row->id==$info->level) selected @endif>{{$row->level}}</option>
					@endforeach
				</select>
				</span> </div>
		</div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<a class="btn btn-primary radius" onclick="func()" >&nbsp;&nbsp;提交&nbsp;&nbsp;</a>
			</div>
		</div>
	</form>
</article>
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />

<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />
<!--<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/style.css" />   -->

</article>
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script> 
 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/jquery.validate.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/validate-methods.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/messages_zh.js"></script> 
<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script> 
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script> 
<!--<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script> -->
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script> 
 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/jquery.validate.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/validate-methods.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/messages_zh.js"></script> 
<!--<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script> -->
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script> 
<script type="text/javascript">
	$(function(){
	$('.skin-minimal input').iCheck({
		checkboxClass: 'icheckbox-blue',
		radioClass: 'iradio-blue',
		increaseArea: '20%'
	});
});
		function func()
	{
		//获取值
		var id = $('#id').val();
		var password = $('#passwd').val();
		var phone = $('#phone').val();
		var group = $('#group').val();
		var level = $("#level option:selected").val()
		$.get('/adminuseredit',{id:id,password:password,phone:phone,group:group,level:level},function(data){
			if(data == 1){
				alert('修改成功!');
				var index = parent.layer.getFrameIndex(window.name);
				parent.$('.btn-refresh').click();
				parent.layer.close(index);
			}else{
				alert('修改失败!请检查信息的完整性或稍后再试!');
			}
		})
	}
</script> 

</body>
</html>