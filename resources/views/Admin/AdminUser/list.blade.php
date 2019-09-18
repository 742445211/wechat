@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20">
				<span class="l"> 
					<a href="javascript:;" onclick="admin_add('添加管理员','/adminuser/create','800','500')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加管理员</a> </span>
				<span class="r">共有数据：<strong>{{$num}}</strong> 条</span>
			</div>
			<table class="table table-border table-bordered table-bg">
				<thead>
					<tr>
						<th scope="col" colspan="9">所有管理员</th>
					</tr>
					<tr class="text-c">
						<!-- <th width="40">ID</th> -->
						<th>用户名</th>
						<th>手机</th>
						<th>公司</th>
						<th>权限</th>
						<th>加入时间</th>
						<th>账号状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
					@foreach($adminuser as $row)
					<tr class="text-c">
						<!-- <td>{{$row -> id}}</td> -->
						<td>{{$row -> username}}</td>
						<td>{{$row -> phone}}</td>
						<td>{{$row -> group}}</td>
						<!-- 如果角色表里面的id等于当前用户下的权限id  就显示当前用户的权限id对应的角色名 -->
						<td>
						@foreach($level as $info)
						@if($info -> id == $row -> level)
							{{$info -> level}}
						@endif
						@endforeach
						</td>

						<td>{{date('Y-m-d H:i',$row -> addtime)}}</td>
						<td class="td-status">
							@if($row->status == 1)
							<span class="label label-success radius">已启用</span></td>
							<td class="td-manage">
							<a style="text-decoration:none" onClick="admin_stop(this,{{$row->id}})" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
							@elseif($row->status == 0)
							<span class="label radius">已停用</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="admin_start(this,{{$row->id}})" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe615;</i></a>
							@endif
							
							<a title="编辑" href="javascript:;" onclick="admin_edit('管理员编辑','/adminuser/{{$row->id}}/edit','800','500')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> 
							@if($row -> id == '1')
							@else
							<!-- <a title="删除" href="javascript:;" onclick="admin_del(this,{{$row -> id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a> -->
							@endif
							</td>
					</tr>
					@endforeach

				</tbody>
			</table>
		</article>
<script type="text/javascript" src="/admin/lib/My97DatePicker/4.8/WdatePicker.js"></script> 
<script type="text/javascript" src="/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="/admin/lib/laypage/1.2/laypage.js"></script> 
<script type="text/javascript">
/*
	参数解释：
	title	标题
	url		请求的url
	id		需要操作的数据id
	w		弹出层宽度（缺省调默认值）
	h		弹出层高度（缺省调默认值）
*/
/*管理员-增加*/
function admin_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-删除*/
function admin_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		//ajax处理请求
		$.get('/admin/del',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}else{
				layer.msg('删除失败!',{icon:2,time:1000});
			}
		});
		
	});
}
/*管理员-编辑*/
function admin_edit(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-停用*/
/*管理员-停用*/
function admin_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$.get('/adminusersta',{id:id},function(data){
			if(data == 0){
				$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_start(this,{{$row->id}})" href="javascript:;" title="启用" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">已停用</span>');
				$(obj).remove();
				layer.msg('已停用!',{icon: 5,time:1000});
			}
		});
	});
}

/*管理员-启用*/
function admin_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$.get('/adminusersta',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_stop(this,{{$row->id}})" href="javascript:;" title="停用" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
				$(obj).remove();
				layer.msg('已启用!', {icon: 6,time:1000});
			}
		});
		
	});
}


</script> 
@endsection
@section('title','管理员列表')
@section('banner','管理员列表')