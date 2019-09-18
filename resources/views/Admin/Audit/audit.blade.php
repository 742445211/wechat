@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
    <!--/_menu 作为公共模版分离出去-->

    <link rel="stylesheet" href="/page/bootstrap.css">
    <div class="Hui-article">
        <article class="cl pd-20">
            <form action="/audit/index/{{$status}}" method="get">
                <div class="text-c"> 按注册日期范围：
                    <input type="text" autocomplete="off" onfocus="WdatePicker({maxDate:'#F{$dp.$D(\'datemax\')||\'%y-%M-%d\'}'})" id="datemin" class="input-text Wdate" style="width:120px;" name="starttime" value="<?php echo isset($starttime)?$starttime:''?>">
                    -
                    <input type="text" autocomplete="off" onfocus="WdatePicker({minDate:'#F{$dp.$D(\'datemin\')}',maxDate:'%y-%M-%d'})" id="datemax" class="input-text Wdate" style="width:120px;" name="endtime" value="<?php echo isset($endtime)?$endtime:''?>">
                    <input type="text" class="input-text" style="width:250px" placeholder="输入用户名称" name="keyword" value="<?php echo isset($keyword)?$keyword:''?>">
                    <button type="submit" class="btn btn-success radius"><i class="Hui-iconfont">&#xe665;</i> 搜用户</button>
                </div>
            </form>
            <div class="cl pd-5 bg-1 bk-gray mt-20"> <span class="l">
			{{--<form action="/sendsms" method="get">
				<input type="hidden" name="checked" value="">
				<input type="submit" class="btn btn-success radius" value="群发短信">
			</form>--}}
                  @if($status == 0)
                  <a class="btn btn-success radius" href="/audit/1">查看已审核</a>
                  </span> <span class="r">共有未审核用户数：<strong>{{$count}}</strong> 位</span> </div>
                  @elseif($status == 1)
                  <a class="btn btn-success radius" href="/audit/0">查看未审核</a>
                  </span> <span class="r">共有已审核用户数：<strong>{{$count}}</strong> 位</span> </div>
                  @endif
            <div class="mt-20">
                <table class="table table-border table-bordered table-hover table-bg table-sort">
                    <thead>
                    <tr class="text-c">
                        <!-- <th width="80">ID</th> -->
                        <th>用户名</th>
                        <th>手机</th>
                        <th>注册时间</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($count == 0)
                        <tr class="text-c">
                            <td colspan="7">暂无数据!</td>
                        </tr>
                    @else
                        @foreach($all as $val)
                            <tr class="text-c">
                            <!-- <td>{{$val->id}}</td> -->
                                <td><u style="cursor:pointer" class="text-primary" onclick="member_show('个人信息','/auditShow/{{$val->id}}','360','400')">{{$val->username}}</u></td>
                                <td>{{$val->phone}}</td>
                                <td class="" style="text-align:center">
                                    {{$val->created_at}}
                                </td>
                                @if($val -> status == 1)
                                    <td class="td-status"><span class="label label-success radius">已通过</span></td>
                                    <td class="td-manage"><a style="text-decoration:none" onClick="member_stop(this,{{$val->id}})" href="javascript:;" title="禁用"><i class="Hui-iconfont">&#xe631;</i></a>
                                @elseif($val->status == 0)
                                    <td class="td-status"><span class="label radius">待审核</span></td>
                                    <td class="td-manage">
                                        <a style="text-decoration:none" onClick="member_start(this,{{$val->id}})" href="javascript:;" title="通过"><i class="Hui-iconfont">&#xe615;</i></a>
                                        <a style="text-decoration:none" onClick="member_no(this,{{$val->id}})" href="javascript:;" title="不通过"><i class="Hui-iconfont">&#xe6dd;</i></a>
                                    <!-- <a title="删除" href="javascript:;" onclick="member_del(this,{{$val->id}})" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a> -->
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            <div style="float:right">{!! $all -> appends($request) -> render() !!}</div>
        </article>
    </div>

    <script type="text/javascript" src="/index/lib/My97DatePicker/4.8/WdatePicker.js"></script>
    <script type="text/javascript" src="/index/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="/index/lib/laypage/1.2/laypage.js"></script>
    <script type="text/javascript">
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
        /*用户-查看*/
        function member_show(title,url,id,w,h){
            layer_show(title,url,w,h);
        }
        /*用户-停用*/
        function member_stop(obj,id){
            layer.confirm('确认要禁用吗？',function(index){
                $.get('/auditstatus',{id:id,status:0},function(data){
                    if(data == 0){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_start(this,'+ id +')" href="javascript:;" title="通过"><i class="Hui-iconfont">&#xe6e1;</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-defaunt radius">已禁用</span>');
                        $(obj).remove();
                        layer.msg('已禁用!',{icon: 5,time:1000});
                    }
                });
            });
        }

        /*用户-启用*/
        function member_start(obj,id){
            layer.confirm('确认要通过吗？',function(index){
                $.get('/auditstatus',{id:id,status:1},function(data){
                    if(data == 1){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="member_stop(this,'+ id +')" href="javascript:;" title="禁用"><i class="Hui-iconfont">&#xe631;</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已通过</span>');
                        $(obj).remove();
                        layer.msg('已通过!',{icon: 6,time:1000});
                    }
                });
            });
        }
        /*用户-编辑*/
        function member_edit(title,url,id,w,h){
            layer_show(title,url,w,h);
        }
        /*密码-修改*/
        function change_password(title,url,id,w,h){
            layer_show(title,url,w,h);
        }
        /*用户-删除*/
        function member_del(obj,id){
            layer.confirm('确认要删除吗？',function(index){
                $.get('/userdel',{id:id},function(data){
                    if(data == 1){
                        $(obj).parents("tr").remove();
                        layer.msg('已删除!',{icon:1,time:1000});
                    }else{
                        layer.msg('删除失败!',{icon:2,time:1000});
                    }
                });

            });
        }

        //审核不通过
        function member_no(obj,id) {
            layer.confirm('确认要不通过吗？',function(index){
                $.get('/auditno',{id:id},function(data){
                    if(data == 1){
                        $(obj).parents("tr").remove();
                        layer.msg('未通过!',{icon: 5,time:1000});
                    }
                });
            });
        }
    </script>
@endsection
@section('title','企业用户列表')
@section('banner','企业用户列表')