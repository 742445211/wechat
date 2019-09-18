<link rel="stylesheet" type="text/css" href="/admin/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/admin/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/admin/lib/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="/admin/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/admin/static/h-ui.admin/css/style.css" />
<title>新建角色</title>
<body>
<article class="cl pd-20">
	<form action="/doroleedit" method="get" class="form form-horizontal" id="form-admin-role-add">
		@foreach($role as $list)
		
		<input type="hidden" name="id" value="{{$list->id}}">
        <div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">角色名称：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="text" class="input-text" style="width:200px" id="roleName" name="rolename" datatype="*4-16" value="{{$list->rolename}}">
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">描述</label>
			<div class="formControls col-xs-8 col-sm-9">
				<textarea type="text" class="input-text" name="js">{{$list->js}}</textarea>
			</div>
		</div>
		<div class="row cl">
			<label class="form-label col-xs-4 col-sm-3">状态：</label>
			<div class="formControls col-xs-8 col-sm-9">
				<input type="radio" name="status" value="1" @if($list->status==1)checked @endif>开启
				<input type="radio" name="status" value="0" @if($list->status==0)checked @endif>关闭
			</div>
		</div>
		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
				<button type="submit" class="btn btn-success radius" id="admin-role-save"><i class="icon-ok"></i> 确定</button>
			</div>
		</div>
						@endforeach
							</form>
</article>
</body>
</html>