@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<!--/_menu 作为公共模版分离出去-->
<link rel="stylesheet" href="/page/bootstrap.css">
	<div class="Hui-article">
		<article class="cl pd-20">
		<form action="/worklist" method="get">
			<div class="text-c"> 按发布时间段：
				<input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" name="starttime" value="<?php echo isset($starttime)?$starttime:''?>" style="width:120px;">
				-
				<input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" name="endtime" value="<?php echo isset($endtime)?$endtime:''?>" style="width:120px;">
				按标题：<input type="text" class="input-text" style="width:250px" placeholder="输入职位标题" id="" name="keyword" value="<?php echo isset($keyword)?$keyword:''?>">
				按状态：<select name="status" style="height:30px;">
					<option value="">--请选择--</option>
					<option value="1">已发布</option>
					<option value="0">已下架</option>
				</select>
				<!-- 查询sessionid下的对应的管理员级别 -->
				<?php 
					$level = DB::table('adminuser') -> where('id','=',session('userid')) -> value('level');
				?>
				@if($level == '1')
				<?php 
					$username = DB::table('adminuser') -> get();
				?>
				按发布人：<select name="username" style="height:30px;">
					<option value="">--请选择--</option>
					@foreach($username as $va)
						<option value="{{$va -> id}}">{{$va->username}}</option>
					@endforeach

				</select>
				@endif
				　<button type="submit" class="btn btn-success radius" id=""><i class="Hui-iconfont">&#xe665;</i>搜职位</button>
			</div>
		</form>
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"> <a href="/worklist/create" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 发布兼职</a></span> <span class="r">共有兼职数：<strong>{{$num}}</strong> 个</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<!-- <th width="40">ID</th> -->
							<th>发布人</th>
							<!-- <th>电话</th> -->
							<th>标题</th>
							<th>发布时间</th>
							<th>公司</th>
							<th>浏览量/报名量</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
					@if($num == 0)
					  <tr class="text-c">
						  <td colspan="10">暂无数据!</td>
					  </tr>
					  @else
						@foreach($list as $val)
						<tr class="text-c">
							<!-- <td>{{$val->id}}</td> -->

							<td><?php if($val->pid){
									echo DB::table('adminuser')->where('id','=',$val->pid)->value('username');
								}else{
									echo DB::table('recruiter')->where('id','=',$val->rid)->value('username');
								}?></td>
							<!-- <td>{{$val->phone}}</td> -->
							<td><u style="cursor:pointer" class="text-primary" onclick="member_show('兼职信息','/worklist/{{$val->id}}','450','500')">{{$val->title}}</u></td>
							<td class="" style="text-align:center">
								{{date('Y-m-d H:i',$val->addtime)}}
							</td>
							<td>{{$val->groupinfo}}</td>
							<td>{{$val->views}}/<?php
								if($val->cplt == ''){
									$num = 0;
								}else{
									$num = count(explode(',',rtrim($val->cplt,',')));
								}
									
									echo "<a>$num</a>";
								// "<a href='/cplts/$val->id' style='text-decoration: underline'>$num</a>";
								?>
									
							</td>
							@if($val -> status == 1)
							<td class="td-status"><span class="label label-success radius">已发布</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="member_stop(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe631;</i></a>
							@elseif($val->status == 0)
							<td class="td-status"><span class="label radius">已下架</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="member_start(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="上架"><i class="Hui-iconfont">&#xe615;</i></a>
							@endif
								<a href="/workedit/{{$val->id}}" title="编辑" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
								<a href="javascript:;" onclick="member_show('兼职信息','/jobimg/{{$val->id}}','450','500')" title="修改图片" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe715;</i></a>

                                @if($val->is_rec == 0)
								<a href="javascript:;" onclick="member_show('添加推荐','/showrec?id={{$val->id}}&status=0','450','500')" title="添加推荐" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe649;</i></a>
                                @else
                                <a href="javascript:;" onclick="recdump('/showrec?id={{$val->id}}&status=1')" title="取消推荐" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe648;</i></a>
                                @endif
							</td>
						</tr>
						@endforeach
						@endif
					</tbody>
				</table>
				
			</div>
			<div style="float:right">{!! $list-> appends($request) -> render() !!}</div>
		</article>
		
	</div>
<script type="text/javascript" src="/index/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/index/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/index/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">
	if(session('addok')){
		alert('添加成功!');
	}
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
	layer.confirm('确认要下架吗？',function(index){
		$.get('/workstatus',{id:id},function(data){
			if(data == 0){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="发布"><i class="Hui-iconfont">&#xe6e1;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已下架</span>');
				$(obj).remove();
				layer.msg('已下架!',{icon: 5,time:1000});
			}
		});
	});
}

/*用户-启用*/
function member_start(obj,id){
	layer.confirm('确认要发布吗？',function(index){
		$.get('/workstatus',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
				$(obj).remove();
				layer.msg('已发布!',{icon: 6,time:1000});
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
		$.get('/workdel',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}else{
				layer.msg('删除失败!',{icon:2,time:1000});
			}
		});
		
	});
}
	function recdump(url){
		$.ajax({
			type:'get',
			url:url,
			success:function (msg) {
				if(msg.code == 0){
					layer.msg('已取消推荐')
				}else{
					layer.msg('取消失败')
				}
			}
		})
	}
</script>
@endsection
@section('title','所有兼职')
@section('banner','所有兼职')