@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="cl pd-5 bg-1 bk-gray"> <span class="l"><a class="btn btn-primary radius" href="javascript:;" onclick="admin_role_add('添加角色','/admin/roleadd','800')"><i class="Hui-iconfont">&#xe600;</i> 添加角色</a> </span> <span class="r">共有数据：<strong></strong> 条</span> </div>
			<div class="mt-10">
			<table class="table table-border table-bordered table-hover table-bg">
				<thead>
					<tr>
						<th scope="col" colspan="6">角色管理</th>
					</tr>
					<tr class="text-c">
						<th width="25">状态</th>
						<th width="40">ID</th>
						<th width="200">角色名</th>
						<th>用户列表</th>
						<th width="300">描述</th>
						<th width="70">操作</th>
					</tr>
				</thead>
				<tbody>
					@foreach($role as $list)
					<tr class="text-c">
						<td>
							@if($list->status==1)
							开启
							@else
							关闭
							@endif
						</td>
						<td>{{$list->id}}</td>
						<td>{{$list->rolename}}</td>
						<td><a href="#">用户1,用户2</a></td>
						<td>{{$list->js}}</td>
						<td class="f-14">
                                <a title="分配权限" href="javascript:;" onclick="admin_role('权限分配','/rolefp/{{$list->id}}')" style="text-decoration:none"><i class="Hui-iconfont">&#xe605;</i></a>
							<a title="编辑" href="javascript:;" onclick="admin_role_edit('角色编辑','/roleedit/{{$list->id}}')" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> <a title="删除" href="javascript:;" onclick="admin_role_del(this,{{$list->id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
					</tr>
					@endforeach
				</tbody>
			</table>
			</div>
			<script type="text/javascript" src="/index/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/index/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/index/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">
/*管理员-角色-添加*/
function admin_role_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-角色-编辑*/
function admin_role_edit(title,url,id,w,h){
	layer_show(title,url,id,w,h);
}
/*管理员-权限—分配*/
function admin_role(title,url,id,w,h){
	layer_show(title,url,id,w,h);
}
/*管理员-角色-删除*/
function admin_role_del(obj,id){
	layer.confirm('角色删除须谨慎，确认要删除吗？',function(index){
		//此处请求后台程序，下方是成功后的前台处理……
		$.get('/roledel',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}else{
				layer.msg('删除失败!',{icon:2,time:1000});
			}
		});
		
	});
}
</script>
@endsection
@section('title','角色管理')
@section('banner','角色管理')