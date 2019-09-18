@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
	<div class="Hui-article">
		<article class="cl pd-20">
			<div class="text-c">
				<input type="text" class="input-text" style="width:250px" placeholder="输入兼职标题" id="" name="">
				<button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
			</div>
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"></span> <span class="r">共有账户信息：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<th >用户</th>
							<th >账户/名</th>
							<th >类型</th>
							<th >机构</th>
						</tr>
					</thead>
					<tbody>
					@if(empty($data))
						<tr class="text-c">
							<td colspan="8">暂无数据!</td>
						</tr>
					@else
						@foreach($data as $val)
							<tr class="text-c">
								<td>{{$val['username']}}</td>
								<td>{{$val['num']}} / {{$val['name']}}</td>
								<td>{{$val['type']}}</td>
								<td class="td-manage">{{$val['a_in']}}</td>
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
//已发薪资
function priceok(userid,workid)
{
	layer.confirm('确认该用户薪资已全部发放吗？',function(index){
		$.get('/putprice',{userid:userid,workid:workid},function(data){
			// console.log(data);
			if(data == '0'){
				document.location.reload();
				layer.msg('已发放!',{icon: 6,time:2000});
			}else{
				layer.msg('发放失败!',{icon: 5,time:1000});
			}
		});
	});
}
/*用户-查看*/
function member_show(title,url,id,w,h){
	layer_show(title,url,w,h);
}
</script>
@endsection
@section('title','用户工资')
@section('banner','用户工资')