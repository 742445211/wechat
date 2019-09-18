@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
	<div class="Hui-article">
		<article class="cl pd-20">
			<div class="text-c">
				<input type="text" class="input-text" style="width:250px" placeholder="输入兼职标题" id="" name="">
				<button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
			</div>
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">当前工作:<font color="#1db682">{{$workname}}</font> 　　Tips:根据每天签到信息进行工资计算,无签到则没有工资(结果仅供管理员参考，具体薪资以实际为准)</span> <span class="r">共有工资信息：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<th >姓名</th>
							<th >合计薪资(￥元)</th>
							<th >状态</th>
							<th >操作</th>
						</tr>
					</thead>
					<tbody>
						@foreach($username as $val)
						<tr class="text-c">
							<td>
								<?php $userid = $val['userid'];?>
								<u style="cursor:pointer" class="text-primary" onclick="member_show('兼职信息','/usershow/{{$userid}}/price','360','400')">{{$val['username']}}</u>
							</td>
							<td>{{$val['pricenum']}}</td>

								@if($val['status_text'] == '无薪资')
								<td style="color:red">{{$val['status_text']}}</td>

								@elseif($val['status_text'] == '已发完')
								<td style="color:green">{{$val['status_text']}}</td>

								@elseif($val['status_text'] == '薪资未发')
								<td style="color:orange">{{$val['status_text']}}</td>

								@else
								<td style="color:orange">{{$val['status_text']}}</td>
								@endif

							<td class="td-manage">
								<?php
								   if($val['status_text'] == '无薪资' || $val['status_text'] == '已发完'){

								   }else{
								?>
								<span class="btn btn-danger" id="price" onclick="priceok({{$val['userid']}},{{$workid}})">薪资发放</span>
								<?php  }?>
								<form action="/userprice" method="post">
									<input type="hidden" name="userid" class="userid" value="{{$val['userid']}}">
									<input type="hidden" name="workid" class="workid" value="{{$workid}}">
									{{csrf_field()}}
									<input type="submit" value="查看签到" class="btn btn-success">
								</form>
							</td>
						</tr>
						@endforeach
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