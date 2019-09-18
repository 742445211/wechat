@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
	<div class="Hui-article">
		<article class="cl pd-20">
			<div class="text-c">
				<input type="text" class="input-text" style="width:250px" placeholder="输入兼职标题" id="" name="">
				<button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
			</div>
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">当前用户:<font color="#1db682">{{$username}}</font></span> 　　在当前工作中共签到 <font color="#1db682">{{$num}}</font> 天，薪资合计 <font color="#1db682">{{$price}}</font> 元(当前工资计算仅供管理员参考，具体薪资以实际为准)<span class="r">共有签到信息：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<th >日期</th>
							<th >签到时间</th>
							<th >签退时间</th>
							<th >奖/罚(元)</th>
							<th >最终工资(元)</th>
							<th >状态</th>
							<th >操作</th>
						</tr>
					</thead>
					<tbody>
						@if($usersign)
						@foreach($usersign as $val)
						<tr class="text-c">
							<td>{{$val['date']}}</td>
							<td>{{$val ['signin_time']}}</td>

							@if($val ['signout_time'] == '未签退')
							<td style="color:red">{{$val ['signout_time']}}</td>
							@else
							<td>{{$val ['signout_time']}}</td>
							@endif

							<td>{{$val ['reward']}} / {{$val ['pun']}}</td>
							<td>{{$val ['allprice']}}</td>
							@if($val ['status'] == '1')
							<td style="color:#1db682">已结算</td>
							@else
							<td style="color:red">未结算</td>
							@endif

							<td class="td-manage">
								@if($val['status'] == 0)
								<span class="btn btn-danger" onclick="ggsmida({{$val['price_detail_id']}})">发放工资</span>
								@else
								<span class="btn btn-success" onclick="ojbk()">工资已发</span>
								@endif
							</td>
						</tr>
						@endforeach
						@else
						<tr class="text-c">
							<td colspan="7">暂无签到和薪资信息</td>
						</tr>
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
function ggsmida(id)
{
		layer.confirm('确认发放今日工资吗？',function(index){
			$.get('/dayprice',{id:id},function(data){
				console.log(data);
				if(data == '1'){
					document.location.reload();
					layer.msg('已发放!',{icon: 6,time:2000});
				}else{
					layer.msg('发放失败!',{icon: 5,time:1000});
				}
			});
		});
}
function ojbk()
{
	layer.msg('请不要重复操作!',{icon: 5,time:1000});
}

</script>
@endsection
@section('title','用户工资')
@section('banner','用户工资')