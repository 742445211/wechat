@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<!--/_menu 作为公共模版分离出去-->


	<div class="Hui-article">
		<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"> <a href="javascript:;" onclick="admin_add('添加热门标签','/hotsadd','510','300')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加热门标签</a></span> <span class="r">共有数据：<strong>{{$num}}</strong> 条</span> </div>
			<div class="mt-20">
				<table class="table table-border table-bordered table-hover table-bg table-sort" id="mytab">
					<thead>
						<tr class="text-c">
							<!-- <th width="">ID</th> -->
							<th>热门标签</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
					@if($num)
						@foreach($hots as $val)
						<tr class="text-c">
							<td  style="display:none">{{$val->id}}</td>
							<td>{{$val->type}}</td>
							@if($val -> status == 1)
							<td class="td-status"><span class="label label-success radius">正常</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="member_stop(this,{{$val->id}})" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
							@elseif($val->status == 0)
							<td class="td-status"><span class="label radius">停用</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="member_start(this,{{$val->id}})" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe615;</i></a>
							@endif
								<!-- <a title="删除" href="javascript:;" onclick="member_del(this,{{$val->id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a> -->
							</td>
						</tr>
						@endforeach
						@else
						<tr class="text-c">
							<td colspan="4">暂无数据!</td>
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
	   //获取table的对象
   var tabs = document.getElementById('mytab');
     //遍历行
     for(var i=1;i<tabs.rows.length;i++){
     	//遍历单元格 tabs.rows[i].cells.length
     	for(m=1;m<2;m++){
          //给每一个单元格绑定双击事件
          tabs.rows[i].cells[m].ondblclick = function(){
			  //获取双击的那和单元格的id
			  id=this.previousSibling.previousSibling.innerHTML;
              //给单击的那个单元格加上输入框,并把单元格里面的数据放到输入框里面
              this.innerHTML = '<input type="text" class="input-text" value="'+this.innerHTML+'">';
              //获取输入框的对象
              var inp = document.getElementsByTagName('input');
              //获取焦点事件
              inp[0].focus();
              //失去焦点后将input的value值写入到表格里面
              inp[0].onblur = function(){
              	//把内容写入到父级标签里面
              	this.parentNode.innerHTML = this.value;
				v = this.value;
				$.get('/hotsedit',{id:id,v:v},function(data){
					if(data == 1){
						layer.msg('修改成功!',{icon: 1,time:1000});
					}else{
						layer.msg('修改失败!',{icon: 2,time:1000});
					}
				})
              }
			  
          }
     	}
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
	/*管理员-增加*/
function admin_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*用户-停用*/
function member_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$.get('/hotsstatus',{id:id},function(data){
			if(data == 0){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe6e1;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">停用</span>');
				$(obj).remove();
				layer.msg('已停用!',{icon: 5,time:1000});
			}
		});
	});
}

/*用户-启用*/
function member_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$.get('/hotsstatus',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,{{isset($val->id)?$val->id:''}})" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">正常</span>');
				$(obj).remove();
				layer.msg('已启用!',{icon: 6,time:1000});
			}
		});
	});
}
/*用户-删除*/
function member_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.get('/hotsdel',{id:id},function(data){
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
@section('title','热门标签管理')
@section('banner','热门标签管理')