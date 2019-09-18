@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="Hui-article">
<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20"> 
				<span class="l"><a class="btn btn-primary radius" onclick="picture_add('添加条目','/addinfo')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加关于条目</a></span>
				<span class="r">共有数据：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-bg table-hover table-sort">
					<thead>
						<tr class="text-c">
							<th>内容</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
				@if($num)
					@foreach($info as $k => $val)
						<tr class="text-c">
							<td>{{$val -> content}}</td>
							<td class="td-status">
								@if($val -> status == '1')
								<span class="label label-success radius">已发布</span>
								@else
								<span class="label label-defaunt radius">已下架</span>
								@endif
							</td>
							<td class="td-manage">
								<a style="text-decoration:none" onClick="picture_stop(this,{{$val -> id}})" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a> 
								<a style="text-decoration:none" class="ml-5" onClick="picture_del(this,{{$val -> id}})" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
							</td>
						</tr>
					@endforeach
					@else
						<tr class="text-c">
							<td colspan="3">暂无关于!</td>
						</tr>
					@endif
					</tbody>
				</table>
			</div>
		</article>
	</div>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript">
$('.table-sort').dataTable({
	"aaSorting": [[ 1, "desc" ]],//默认第几个排序
	"bStateSave": true,//状态保存
	"aoColumnDefs": [
	  //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
	  {"orderable":false,"aTargets":[0,8]}// 制定列不参与排序
	]
});
/*图片-添加*/
function picture_add(title,url){
	var index = layer.open({
		type: 2,
		title: title,
		content: url
	});
	layer.full(index);
}
/*图片-下架*/
function picture_stop(obj,id){
	layer.confirm('确认要下架吗？',function(index){
		$.get('/infostatus',{id:id},function(data){
			if(data == '0'){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_start(this,<?php
						echo isset($val -> id)?$val -> id:'';
					?>)" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已下架</span>');
				$(obj).remove();
				layer.msg('已下架!',{icon: 5,time:1000});
			}else{
				layer.msg('下架失败!',{icon: 5,time:1000});
			}
		})
	});
}

/*图片-发布*/
function picture_start(obj,id){
	layer.confirm('确认要发布吗？',function(index){
		$.get('/infostatus',{id:id},function(data){
			if(data == '1'){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_stop(this,<?php
						echo isset($val -> id)?$val -> id:'';
					?>)" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
				$(obj).remove();
				layer.msg('已发布!',{icon: 6,time:1000});
			}else{
				layer.msg('发布失败!',{icon: 5,time:1000});
			}
		})
		
	});
}
/*图片-删除*/
function picture_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.get('/delinfo',{id:id},function(data){
			if(data == 'del'){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}
		})
	});
}
</script>
@endsection
@section('title','关于页面')
@section('banner','关于页面')