@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="Hui-article">
<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20">
				<span class="r">共有数据：<strong>{{$num}}</strong> 条</span>
			</div>
			<table class="table table-border table-bordered table-bg" id="mytab">
				<thead>
					<tr class="text-c">
						<!-- <th >分类ID</th> -->
						<th>分类名称</th>
						<th>兼职数量</th>
					</tr>
				</thead>
				<tbody>
				@if($num)
					@foreach($cate as $key => $list)
					<tr class="text-c">
						<!-- <td>{{$list->id}}</td> -->
						<td>{{$list->cates}}</td>
						<td>{{$nums[$key]}}</td>
						
					</tr>
                    @endforeach
                    @else
                    <tr class="text-c">
                    	<td colspan="3">暂无数据!</td>
                    </tr>
                    @endif
				</tbody>
			</table>
		</article>
</div>
<script type="text/javascript" src="/admin/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="/admin/lib/laypage/1.2/laypage.js"></script> 
<script type="text/javascript">
/*
	参数解释：
	title	标题
	url		请求的url
	id		需要操作的数据id
	w		弹出层宽度（缺省调默认值）
	h		弹出层高度（缺省调默认值）
*/
	/*分类-增加*/
function admin_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*分类-编辑*/
function admin_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}

</script> 
@endsection
@section('title','分类数据')
@section('banner','分类数据')