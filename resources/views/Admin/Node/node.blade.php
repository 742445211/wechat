@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<link rel="stylesheet" href="/page/bootstrap.css">
<div class="text-c">
				<form class="/node" method="get" action="" target="_self">
					<input type="text" class="input-text" style="width:250px" placeholder="权限名称" value="<?php echo isset($keyword)?$keyword:''?>" name="keyword">
					<button type="submit" class="btn btn-success" id="" name=""><i class="Hui-iconfont">&#xe665;</i> 搜权限节点</button>
				</form>
			</div>
			<div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l"><a href="javascript:;" onclick="admin_permission_add('添加权限节点','/node/create','500','310')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加权限节点</a></span> <span class="r">共有数据：<strong>{{$num}}</strong> 条</span> </div>
				<table class="table table-border table-bordered table-hover table-bg table-sort" id="mytab">
					<thead>
						<tr class="text-c">
                          
							<th>节点名称</th>
							<th>状态</th>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
						@foreach($node as $val)
						<tr class="text-c">
							<td  style="display:none">{{$val->id}}</td>
							<td>{{$val->nodename}}</td>
							<td class="td-status"><span class="label label-success radius">正常</span></td>
							<td class="td-manage"><i class="Hui-iconfont">&#xe631;</i></a>
								<!-- <a title="删除" href="javascript:;" onclick="admin_permission_del(this,{{$val->id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a> -->
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			<div style="float:right">{!! $node -> appends($request) -> render() !!}</div>
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
              this.innerHTML = '<input type="text" class="input-text" id="in" value="'+this.innerHTML+'">';
              //获取输入框的对象
              var inp = document.getElementById('in');
              //获取焦点事件
              inp.focus();
              //失去焦点后将input的value值写入到表格里面
              inp.onblur = function(){
              	//把内容写入到父级标签里面
              	this.parentNode.innerHTML = this.value;
				v = this.value;
				$.get('/nodeedit',{id:id,v:v},function(data){
					if(data == 1){
						layer.msg('修改成功!',{icon:1,time:1000});
					}else{
						layer.msg('修改失败!',{icon:2,time:1000});
					}
				})
              }
			  
          }
     	}
     }
	
/*
	参数解释：
	title	标题
	url		请求的url
	id		需要操作的数据id
	w		弹出层宽度（缺省调默认值）
	h		弹出层高度（缺省调默认值）
*/
/*管理员-权限-添加*/
function admin_permission_add(title,url,w,h){
	layer_show(title,url,w,h);
}
/*管理员-权限-删除*/
function admin_permission_del(obj,id){
	layer.confirm('权限删除须谨慎，确认要删除吗？',function(index){
		$.get('/nodedel',{id:id},function(data){
 			if(data == 1){
				$(obj).parents("tr").remove();
				layer.msg('已删除!',{icon:1,time:1000});
			}else{
				layer.msg('删除失败!',{icon:1,time:1000});
			}
		});
		
	});
}
</script>
@endsection
@section('title','权限管理')
@section('banner','权限管理')