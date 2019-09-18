@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
	<div class="Hui-article">
		<article class="cl pd-20">
			<div class="text-c">
				<input type="text" class="input-text" style="width:250px" placeholder="输入兼职标题" id="" name="">
				<button type="submit" class="btn btn-success radius" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜兼职</button>
			</div>
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"></span> <span class="r">共有数据：<strong>{{$nums}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort">
					<thead>
						<tr class="text-c">
							<th >兼职</th>
							<th >人数</th>
							<th >总工资(元)</th>
							<th >操作</th>
						</tr>
					</thead>
					<tbody>
					@if($nums == 0)
						<tr class="text-c">
							<td colspan="8">暂无数据!</td>
						</tr>
					@else
						@foreach($adminGroup as $val)
						<tr class="text-c">
							<td>{{$val -> group_title}}</td>
							@if($val -> group_num >= 2)
							<td>{{$val -> group_num - 1}}</td>
							@else
							<td>{{$val -> group_num}}</td>
							@endif
							<td>
							@if(count($all) > 0)
								@foreach($all as $v)
									@if(isset($v['workid']) && $v['workid'] == $val -> workid)
										{{$v['allprice']}}
									@endif
								@endforeach
							@endif
							</td>
							<td class="td-manage">
								<form action="/workprice" method="post">
									<input type="hidden" name="workid" value="{{$val -> workid}}">
									{{csrf_field()}}
									<input type="submit" value="查看详情" class="btn btn-success">
								</form>
							</td>
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
@endsection
@section('title','工资管理')
@section('banner','工资管理')