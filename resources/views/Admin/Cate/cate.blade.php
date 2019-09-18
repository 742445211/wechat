@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="Hui-article">
<article class="cl pd-20">
			<div class="cl pd-5 bg-1 bk-gray mt-20">
				<span class="l"> 
					<a href="javascript:;" onclick="admin_add('添加管分类','/cateadd','500','300')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加分类</a> </span>
				<span class="r">共有数据：<strong>{{$num}}</strong> 条</span>
			</div>
			<table class="table table-border table-bordered table-bg" id="mytab">
				<thead>
					<tr class="text-c">
						<!-- <th >ID</th> -->
						<th>分类</th>
						<th>状态</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody>
				@if($num)
					@foreach($cate as $list)
					<tr class="text-c">
						<td style="display:none">{{$list -> id}}</td>
						<td>{{$list->cates}}</td>
						<td class="td-status">
							@if($list->status == 1)
							<span class="label label-success radius">已启用</span></td>
							<td class="td-manage">
							<a style="text-decoration:none" onClick="admin_stop(this,{{$list->id}})" href="javascript:;" title="停用"><i class="Hui-iconfont">&#xe631;</i></a>
							@elseif($list->status == 0)
							<span class="label radius">已停用</span></td>
							<td class="td-manage"><a style="text-decoration:none" onClick="admin_start(this,{{$list->id}})" href="javascript:;" title="启用"><i class="Hui-iconfont">&#xe615;</i></a>
							@endif
							<!-- <a title="删除" href="javascript:;" onclick="admin_del(this,{{$list -> id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a> -->
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
		</article>
</div>
<script type="text/javascript" src="/admin/lib/My97DatePicker/4.8/WdatePicker.js"></script> 
<script type="text/javascript" src="/admin/lib/datatables/1.10.0/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="/admin/lib/laypage/1.2/laypage.js"></script> 
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
				$.get('/cateedit',{id:id,v:v},function(data){
					if(data == 1){
						alert('修改成功!');
					}else{
						alert('修改失败!');
					}
				})
             }
          }
     	}
     }
	/*分类-增加*/
function admin_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*分类-删除*/
function admin_del(obj,id){
	layer.confirm('确认要删除吗？',function(index){
		$.get('/catedel',{id:id},function(data){
			if(data == 1){
				layer.confirm('删除成功!');
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}else{
				layer.confirm('删除失败!');
			}
		});
	});
}
/*分类-编辑*/
function admin_edit(title,url,id,w,h){
	layer_show(title,url,w,h);
}
/*分类-停用*/
function admin_stop(obj,id){
	layer.confirm('确认要停用吗？',function(index){
		$.get('/catesta',{id:id},function(data){
			if(data == 0){
				$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_start(this,{{isset($list->id)?$list->id:''}})" href="javascript:;" title="启用" style="text-decoration:none"><i class="Hui-iconfont">&#xe615;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-default radius">已停用</span>');
				$(obj).remove();
				layer.msg('已停用!',{icon: 5,time:1000});
			}
		});
	});
}

/*分类-启用*/
function admin_start(obj,id){
	layer.confirm('确认要启用吗？',function(index){
		$.get('/catesta',{id:id},function(data){
			if(data == 1){
				$(obj).parents("tr").find(".td-manage").prepend('<a onClick="admin_stop(this,{{isset($list->id)?$list->id:''}})" href="javascript:;" title="停用" style="text-decoration:none"><i class="Hui-iconfont">&#xe631;</i></a>');
				$(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已启用</span>');
				$(obj).remove();
				layer.msg('已启用!', {icon: 6,time:1000});
			}
		});
		
	});
}

</script> 
@endsection
@section('title','分类标签列表')
@section('banner','分类列表')