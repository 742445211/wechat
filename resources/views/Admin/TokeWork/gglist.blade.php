@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="Hui-article">
        <article class="cl pd-20">
            <div class="cl pd-5 bg-1 bk-gray"> <span class="l">
            <a class="btn btn-primary radius" href="javascript:;" onclick="gg_add('添加公告','/addgg/{{$grouptitle -> group_id}}','700','600')"><i class="Hui-iconfont">&#xe600;</i>添加公告</a> 
        </span> <span class="r">共有公告：<strong>{{$num}}</strong> 条</span> </div>
            <div class="mt-10">
            <table class="table table-border table-bordered table-hover table-bg" id="mytab">
                <thead>
                    <tr>
                        <th scope="col" colspan="6">当前群 ：<font color="#1db682">  {{$grouptitle -> group_title}}</font> 　　双击公告内容可以进行修改</th>
                    </tr>
                    <tr class="text-c">
                        <th>发布时间</th>
                        <th>公告内容</th>
                        <th>通知状态(短信)</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                  @if($num)
                    @foreach($data as $v)
                    <tr class="text-c">
                        <td  style="display:none">{{$v -> id}}</td>
                        <td><?php echo date('Y-m-d H:i',$v -> addtime);?></td>
                        <td>{{$v -> content}}</td>
                        @if($v -> status == '0')
                        <td class="td-status"><span class="label label-danger radius">未通知</span></td>
                        @else
                        <td class="td-status"><span class="label label-success radius">已通知</span></td>
                        @endif
                        <td class="td-manage">
                            @if($v -> status == '0')
                            <a style="text-decoration:none" onClick="send_sms(this,{{$v -> groupid}})" href="javascript:;" title="短信通知"><i class="Hui-iconfont">&#xe68a;</i></a>
                            @else
                            OK
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @else
                    <tr class="text-c">
                      <td colspan="4">暂无公告</td>
                    </tr>
                    @endif
                </tbody>
            </table>
            </div>
        </article>
        </div>
        <script type="text/javascript">
//发送短信
function send_sms(obj,id){
    layer.confirm('确认要发送短信通知群成员吗？',function(index){
        $.get('/send_sms',{groupid:id},function(data){
            console.log(data);
            if(data.code == '0'){
                $(obj).parents("tr").find(".td-manage").prepend('OK');
                $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已通知</span>');
                $(obj).remove();
                layer.msg(data.oksms+"条信息发送成功!"+','+ data.result+"条信息发送失败！",{icon: 6,time:2500});
            }else{
                layer.msg(data.oksms+"条信息发送成功!"+','+ data.result+"条信息发送失败！",{icon: 5,time:2500});
            }
        },'json')
        
    });
}
    //获取table的对象
   var tabs = document.getElementById('mytab');
     //遍历行
     for(var i=2;i<tabs.rows.length;i++){
        //遍历单元格 tabs.rows[i].cells.length
        for(m=2;m<3;m++){       //m=2每行从第几个单元格开始   <3  从第几个结束
          //给每一个单元格绑定双击事件
          tabs.rows[i].cells[m].ondblclick = function(){
              //获取双击的那和单元格的id
              id=this.previousSibling.previousSibling.previousSibling.previousSibling.innerHTML;
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
                $.get('/editgg',{id:id,v:v},function(data){
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
//添加群公告
function gg_add(title,url,w,h){
    layer_show(title,url,w,h);
}
        </script>
@endsection
@section('title','工作群列表')
@section('banner','工作群列表')