<link rel="stylesheet" type="text/css" href="/admin/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/admin/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/admin/lib/Hui-iconfont/1.0.8/iconfont.css" />

<link rel="stylesheet" type="text/css" href="/admin/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/admin/static/h-ui.admin/css/style.css" />
<style type="text/css">
      .cl{
      	margin-top:25px;
      	margin-left:15px;
      }
</style>
<form action="/donedit" method="post">
	<input type="hidden" name="id" value="{{$node->id}}">
        <div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">角色名称：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" style="width:200px" id="roleName" name="nodename" datatype="*4-16" nullmsg="用户账户不能为空" value="{{$node->nodename}}">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">控制器名称</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" style="width:200px" value="{{$node->kname}}" name="kname">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">方法名称</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" style="width:200px" value="{{$node->fname}}" name="fname">
			</div>
		</div>
		{{csrf_field()}}
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<button type="submit" class="btn btn-success radius" id="admin-role-save" name=""><i class="icon-ok"></i> 确定</button>
			</div>
		</div>
</form>