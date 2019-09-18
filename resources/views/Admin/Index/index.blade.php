@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="Hui-article">
<article class="cl pd-20">
			<p>登录次数：{{session('loginnum')}} </p>
			<p>上次登录IP：{{session('loginip')}} </p>
			<p>上次登录时间：{{date('Y-m-d H:i:s',session('logintime'))}}</p>
			<br>
			<table class="table table-border table-bordered table-bg">
				<thead>
					<tr>
						<th colspan="7" scope="col">信息统计 <span style="float:right">管理员:{{session('username')}}</span></th>
			</tr>
					<tr class="text-c">
						<th>统计</th>
						<th>兼职数</th>
						<th>报名数</th>
						<th>投诉</th>
						<th>建议</th>
			</tr>
		</thead>
				<tbody>
					<tr class="text-c">
						<td>总数</td>
						<td>{{$work}}</td>
						<td>{{$cplt}}</td>
						<td>{{$tousu}}</td>
						<td>{{$yijian}}</td>
			</tr>
		</tbody>
	</table>
</article>
</div>
@endsection
@section('title','后台首页')
@section('banner','欢迎登录到后台')