@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<!--/_menu 作为公共模版分离出去-->

	<div class="Hui-article">
		<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20"> 
				
			  <span class="l"> </span> <span class="r">共有数据：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<!-- <th width="40">ID</th> -->
							<th>报名人</th>
							<!-- <th>发布人</th> -->
							<th>报名兼职</th>
							<th>报名时间</th>
							<th>联系电话</th>
							<th>当前状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
					@if(empty($all))
						<tr class="text-c">
							<td colspan="8">暂无数据!</td>
						</tr>
					@else
						@foreach($all as $val)
						<tr class="text-c">
							<!-- <td>{{$val['id']}}</td> -->
							<td><u style="cursor:pointer" class="text-primary" onclick="member_show('兼职信息','/usershow/{{$val['id']}}/cplt','360','400')">{{$val['username']}}</u></td>
							<!-- <td>{{$val['adminname']}}</td> -->
							<td>{{$val['worktitle']}}</td>
							<td class="" style="text-align:center">
								{{date('Y-m-d',$val['cplttime'])}}
							</td>
							
							<td>{{$val['phone']}}</td>
							
							@if($val['status'] == 0)
							<td class="td-status"><span class="label radius">待通过</span></td>
							<td class="td-manage1"><a style="text-decoration:none" onClick="member_ok(this,{{$val['id']}},'gg',{{$val['workid']}})" href="javascript:;" title="通过"><i class="Hui-iconfont">&#xe615;</i></a>&nbsp;<a style="text-decoration:none" onClick="member_no(this,{{$val['id']}},'no')" href="javascript:;" title="拒绝"><i class="Hui-iconfont">&#xe6a6;</i></a>&nbsp;<a style="text-decoration:none" onClick="member_ok(this,{{$val['id']}},'ms')" href="javascript:;" title="通知面试"><i class="Hui-iconfont">&#xe60c;</i></a>
							@elseif($val['status'] == 1)
							<td class="td-status"><span class="label label-success radius">待面试</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="member_ms(this,{{$val['id']}},'ok',{{$val['workid']}})" href="javascript:;" title="面试通过"><i class="Hui-iconfont">&#xe615;</i></a>  <a style="text-decoration:none" onClick="member_ms(this,{{$val['id']}},'no',{{$val['workid']}})" href="javascript:;" title="面试不通过"><i class="Hui-iconfont">&#xe6a6;</i></a>
							@elseif($val['status'] == 2)
							<td class="td-status"><span class="label label-success radius">已通过</span></td>
							<td class="td-manage">
							@elseif($val['status'] == 3)
							<td class="td-status"><span class="label label-danger radius">未通过</span></td>
							<td class="td-manage">
							@endif
								<!-- <a title="删除" href="javascript:;" onclick="member_del(this,{{$val['id']}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a></td> -->
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
/*用户-停用*/
function member_no(obj,id,status){
	layer.confirm('确认要拒绝吗？',function(index){
		$.get('/cpltstatus',{id:id,status:status},function(data){
			if(data == 3){
				$(obj).parents("tr").find(".td-status").html('<span class="label label-danger radius">未通过</span>');
				$(obj).remove();
				layer.msg('已拒绝!',{icon: 5,time:1000});
			}
		});
	});
}

/*用户-启用*/
function member_ok(obj,id,status,workid){
	layer.confirm('确认要通过吗？',function(index){
		$.get('/cpltstatus',{id:id,status:status,workid:workid},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">待面试</span>');
				$(obj).remove();
				layer.msg('等待面试!',{icon: 6,time:1000});
			}else if(data == 2){
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已通过</span>');
				$(obj).remove();
				layer.msg('已通过!',{icon: 6,time:1000});
			}
		});
	});
}
//处理面试状态
function member_ms(obj,id,status,workid){
	layer.confirm('确认要通过吗？',function(index){
		$.get('/cpltms',{id:id,status:status,workid:workid},function(data){
			if(data == 2){
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已通过</span>');
				$(obj).remove();
				layer.msg('已通过!',{icon: 6,time:1000});
			}else if(data == 3){
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">未通过</span>');
				$(obj).remove();
				layer.msg('已拒绝!',{icon: 5,time:1000});
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
		$.get('/cpltdel',{id:id},function(data){
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
@section('title','报名列表')
@section('banner','报名列表')