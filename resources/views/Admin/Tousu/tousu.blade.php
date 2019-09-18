@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<!--/_menu 作为公共模版分离出去-->


	<div class="Hui-article">
		<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"> <font color="red">请尽快处理用户的投诉请求</font></span> <span class="r">收到的投诉：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<!-- <th width="40">ID</th> -->
							<th>投诉人</th>
							<th>投诉的职位</th>
							<th>投诉内容</th>
							<th>投诉时间</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
					@if($num == 0)
					<tr class="text-c">
						<td colspan="7">暂无数据!</td>
					</tr>
					@else
						@foreach($list as $val)

						<tr class="text-c">
							<!-- <td>{{$val->id}}</td> -->
							<td><?php echo DB::table('homeuser')->where('id','=',$val->userid)->value('username');?></td>
							<td><u style="cursor:pointer" class="text-primary" onclick="member_show('兼职信息','/worklist/{{$val->id}}','360','400')"><?php
									echo DB::table('work') -> where('id','=',$val->workid) -> value('title');
								?></u></td>
							<td>{{$val->content}}</td>
							<td class="" style="text-align:center">
								{{date('Y-m-d H:i',$val->sendtime)}}
							</td>
							@if($val -> status == 1)
							<td class="td-status"><span class="label label-success radius">已查看</span></td>
							<td class="td-manage">
							<!-- <a title="删除" href="javascript:;" onclick="member_del(this,{{$val->id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a> -->
						</td>
							@elseif($val->status == 0)
							<td class="td-status"><span class="label label-danger radius">未查看</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="member_start(this,{{$val->id}})" href="javascript:;" title="已查看"><i class="Hui-iconfont">&#xe615;</i></a></td>
							@endif
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
			</div>
		</article>
	</div>

<script type="text/javascript" src="/index/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/index/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/index/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">
$(function(){
	$('.table-sort').dataTable({
		"aaSorting": [[ 1, "desc" ]],//默认第几个排序
		"bStateSave": true,//状态保存
		"aoColumnDefs": [
		  //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
		  {"orderable":false,"aTargets":[0,8,9]}// 制定列不参与排序
		]
	});
	$('.table-sort tbody').on( 'click', 'tr', function () {
		if ( $(this).hasClass('selected') ) {
			$(this).removeClass('selected');
		}
		else {
			table.$('tr.selected').removeClass('selected');
			$(this).addClass('selected');
		}
	});
});
/*用户-查看*/
function member_show(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*用户-启用*/
function member_start(obj,id){
	layer.confirm('确认要发布吗？',function(index){
		$.get('/tousuok',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已查看</span>');
				$(obj).remove();
				layer.msg('已查看!',{icon: 6,time:1000});
			}else{
					layer.msg('处理失败!',{icon: 5,time:1000});
			}
		});
	});
}
/*用户-编辑*/
function member_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*密码-修改*/
function change_password(title,url,id,w,h){
	layer_show(title,url,w,h);	
}
/*用户-删除*/
function member_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.get('/del',{id:id},function(data){
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
@section('title','所有投诉')
@section('banner','所有投诉')