@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')

<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20">
				<span class="l"> 
					<a href="javascript:;" onclick="admin_add('添加链接','/linklist/create','800','500')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加链接</a> </span>
				<span class="r">共有数据：<strong>{{$num}}</strong> 条</span>
			</div>
			<table class="table table-border table-bordered table-bg">
				<thead>
					<tr>
						<th scope="col" colspan="9">链接列表</th>
					</tr>
					<tr class="text-c">
						<!-- <th >ID</th> -->
						<th>链接名称</th>
						<th >链接地址</th>
						<th >状态</th>
						<th width="100">操作</th>
					</tr>
				</thead>
				<tbody>
					@foreach($link as $list)
					<tr class="text-c">
						<!-- <td>{{$list->id}}</td> -->
						<td>{{$list->linkname}}</td>
						<td>{{$list->linkurl}}</td>
						<td class="td-status">
							@if($list->status == 1)
							<span class="label label-success radius">已启用</span></td>
							<td class="td-manage">
							<a style="text-decoration:none" onClick="admin_stop(this,{{$list->id}})" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
							@else
							<span class="label radius">已停用</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="admin_start(this,{{$list->id}})" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe615;</i></a>
							@endif
							<a title="编辑" href="javascript:;" onclick="admin_edit('链接编辑','/linklist/{{$list->id}}/edit','800','500')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a> <a title="删除" href="javascript:;" onclick="link_del(this,{{$list->id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
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
function link_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		//此处请求后台程序，下方是成功后的前台处理……
		$.get('/linkdel',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}else{
				layer.msg('删除失败!',{icon:2,time:1000});
			}
		});
		
	});
}
/*分类-编辑*/
function admin_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*分类-停用*/
function admin_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		//此处请求后台程序，下方是成功后的前台处理……
		$.get('/linkstatus',{id:id},function(data){
			if(data == 0){
				$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_start(this,id)" href="javascript:;" title="启用" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">已禁用</span>');
				$(obj).remove();
				layer.msg('已停用!',{icon: 5,time:1000});
			}
		});
	});
}

/*分类-启用*/
function admin_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		//此处请求后台程序，下方是成功后的前台处理……
		$.get('/linkstatus',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_stop(this,id)" href="javascript:;" title="停用" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
				$(obj).remove();
				layer.msg('已启用!', {icon: 6,time:1000});
			}
		});
		
	});
}

</script> 
@endsection
@section('title','友情链接')
@section('banner','友情链接')