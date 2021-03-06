@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<!--/_menu 作为公共模版分离出去-->

<link rel="stylesheet" href="/page/bootstrap.css">
	<div class="Hui-article">
		<article class="cl pd-20">
		<form action="/smsss" method="get">
			<div class="text-c"> 按注册时间范围：
				<input type="text" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;" name="starttime" value="<?php echo isset($starttime)?$starttime:''?>">
				-
				<input type="text" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;" name="endtime" value="<?php echo isset($endtime)?$endtime:''?>">
				<input type="text" class="input-text" style="width:250px" placeholder="输入用户名称" name="username" value="">
			</div>
			<br>
			<div class="text-c">
				<input type="text" class="input-text" style="width:250px" placeholder="输入手机号" name="phone" value="">
				按状态搜索：<select name="status" id="" style="width:80px;height:30px">
					<option value="">--请选择--</option>
					<option value="1">开启</option>
					<option value="0">禁用</option>
				</select>
				&nbsp;&nbsp;&nbsp;<button type="submit" class="btn btn-success radius"><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
			</div>
		</form>
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
			
			
			      </span> <span class="r">共有用户数：<strong>{{$num}}</strong> 位</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<th width="25"><input type="checkbox" name="" value=""></th>
							<th width="80">ID</th>
							<th width="100">用户名</th>
							<th width="90">手机</th>
							<th width="90">注册时间</th>
							<th width="70">状态</th>
						</tr>
					</thead>
					<tbody>
					@if($num == 0)
					<tr class="text-c">
						<td colspan="7">暂无数据!</td>
					</tr>
					@else
						@foreach($user as $val)
						<tr class="text-c">
							<td><input type="checkbox" value="{{$val->id}}" name="sms"></td>
							<td>{{$val->id}}</td>
							<td><u style="cursor:pointer" class="text-primary" onclick="member_show('个人信息','/usershow/{{$val->id}}','360','400')">{{$val->username}}</u></td>
							<td>{{$val->phone}}</td>
							<td class="" style="text-align:center">
								{{date('Y-m-d H:i:s',$val->addtime)}}
							</td>
							@if($val -> status == 1)
							<td class="td-status"><span class="label label-success radius">已启用</span></td>
							@elseif($val->status == 0)
							<td class="td-status"><span class="label radius">已停用</span></td>

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
/*用户-停用*/
function member_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$.get('/userstatus',{id:id},function(data){
			if(data == 0){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已停用</span>');
				$(obj).remove();
				layer.msg('已停用!',{icon: 5,time:1000});
			}
		});
	});
}

/*用户-启用*/
function member_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$.get('/userstatus',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
				$(obj).remove();
				layer.msg('已启用!',{icon: 6,time:1000});
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
		$.get('/userdel',{id:id},function(data){
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
@section('title','群发短信')
@section('banner','群发短信')