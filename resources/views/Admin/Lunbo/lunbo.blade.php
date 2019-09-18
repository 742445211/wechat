@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="Hui-article">
<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a class="btn btn-primary radius" onclick="picture_add('添加图片','/addlunbo')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加轮播</a></span>　　　排序数字越小轮播时越靠前 <span class="r">共有数据：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-bg table-hover table-sort">
					<thead>
						<tr class="text-c">
							<th>排序</th>
							<th>轮播</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						
						@if($num)
						@foreach($lunbo as $key => $val)
						<tr class="text-c">
                          <td>{{$val -> level}}</td>
							<td><img width="100" class="picture-thumb" src="{{$val -> url}}"></td>
							<td class="td-status">
								@if($val -> status == 1)
								<span class="label label-success radius">已发布</span>
								@else
								<span class="label label-defaunt radius">已下架</span>
								@endif
							</td>

							<td class="td-manage">
								<a style="text-decoration:none" onClick="picture_stop(this,{{$val -> id}})" href="javascript:;"
								   @if($val -> status == 1)
								       title="下架"
								   @else
									   title="发布"
								   @endif
								>
									<i class="Hui-iconfont">
										@if($val -> status == 1)
											&#xe6de;
										@else
											&#xe603;
										@endif
									</i></a>
								<a style="text-decoration:none" class="ml-5" onClick="picture_del(this,{{$val -> id}})" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
							</td>
						</tr>
						@endforeach
						@else
						<tr class="text-c">
							<td colspan="4">暂无轮播图!</td>
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
/*
$('.table-sort').dataTable({
	"aaSorting": [[ 1, "desc" ]],//默认第几个排序
	"bStateSave": true,//状态保存
	"aoColumnDefs": [
	  //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
	  {"orderable":false,"aTargets":[0,8]}// 制定列不参与排序
	]
});
*/
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
	var change = $(obj).attr('title');
	layer.confirm('确认要'+ change +'吗？',function(index){
		$.get('/setstatus',{id:id},function(data){
			if(data == '0'){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_start(this,'+ id +')" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe603;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已下架</span>');
				$(obj).remove();
				layer.msg('已下架!',{icon: 5,time:1000});
			}else{
				layer.msg('已发布!',{icon: 6,time:1000});
			}
		})
	});
}

/*图片-发布*/
function picture_start(obj,id){
	layer.confirm('确认要发布吗？',function(index){
		$.get('/setstatus',{id:id},function(data){
			if(data == '1'){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_stop(this,'+ id +')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
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
		$.get('/delajax',{id:id},function(data){
			if(data == 'del'){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}
		})
	});
}

$("body").on('click','.table tbody tr td img',function(e){
	layer.photos({ photos: {"data": [{"src": e.target.src}]} ,shift: 5});
});
</script>

@endsection
@section('title','首页轮播')
@section('banner','首页轮播')