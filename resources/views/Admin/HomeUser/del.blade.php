@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" onclick="datadel()" class="btn btn-danger radius"><i class="Hui-iconfont">&#xe6e2;</i> 批量删除</a> </span> <span class="r">共有数据：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
			<table class="table table-border table-bordered table-hover table-bg table-sort">
				<thead>
					<tr class="text-c">
						<th width="25"><input type="checkbox" name="" value=""></th>
						<!-- <th width="80">ID</th> -->
						<th>头像</th>
						<th>用户名</th>
						<th>手机</th>
						<th>身份证</th>
						<th>加入时间</th>
						<th>状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
				@if($num == 0)
				<tr class="text-c">
					<td colspan="9">暂无数据!</td>
				</tr>
				@else
					@foreach($del as $user)
					<tr class="text-c">
						<td><input type="checkbox" value="1" name=""></td>
						<!-- <td>{{$user->id}}</td> -->
						<td><img src="{{$user->header}}"></td>
						<td><u style="cursor:pointer" class="text-primary" onclick="member_show('个人信息','/usershow/{{$user->id}}','10001','360','450')">{{$user->username}}</u></td>
						<td>{{$user->phone}}</td>
						<td>{{$user->idcard}}</td>
						<td>{{date('Y-m-d H:i:s',$user->addtime)}}</td>
						<td class="td-status"><span class="label label-danger radius">已删除</span></td>
						<td class="td-manage"><a style="text-decoration:none" href="javascript:;" onClick="member_huanyuan(this,{{$user->id}})" title="还原"><i class="Hui-iconfont">&#xe66b;</i></a> <a title="删除" href="javascript:;" onclick="member_del(this,{{$user->id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
					</tr>
					@endforeach
					@endif
				</tbody>
			</table>
			</div>
		</article>
<script type="text/javascript" src="/admin/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/admin/lib/laypage/1.2/laypage.js"></script>

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

/*用户-还原*/
function member_huanyuan(obj,id){
	layer.confirm('确认要还原吗？',function(index){
		$.get('/userdelback',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").remove();
				layer.msg('已还原!',{icon: 6,time:1000});
			}else{
				layer.msg('还原失败!',{icon: 5,time:1000});
			}
		});
		
	});
}
/*用户-查看*/
function member_show(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*用户-删除*/
function member_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.get('/userdeldel',{id:id},function(data){
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
@section('title','删除的会员')
@section('banner','删除的会员')