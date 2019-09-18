<link rel="stylesheet" type="text/css" href="/index/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />

<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/style.css" />   
<article class="cl pd-20">
<!--	<form action="/useredit" method="post" class="form form-horizontal" id="form-member-add">-->
	<br>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">分类标签：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" id="cates" name="cates" maxlength="10">
			</div>
		</div>
	<br><br><br><br>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<input class="btn btn-primary radius" onclick="func()" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
			</div>
		</div>
<!--	</form>-->
</article>
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script> 
 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/jquery.validate.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/validate-methods.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/messages_zh.js"></script> 
<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script> 
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script> 
<script>
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
		var val = $('#cates').val();
		if(val == ''){
			alert('分类名称不能为空!');
		}else{
			$.get('/cateup',{val:val},function(data){
			if(data == 1){
				alert('添加成功!');
				var index = parent.layer.getFrameIndex(window.name);
				parent.layer.close(index);
				parent.$('.btn-refresh').click();
			}else{
				alert('添加失败!');
			}
		})
		}
		
	}
</script> 
	
@section('title','添加热门标签')
@section('banner','添加热门标签')