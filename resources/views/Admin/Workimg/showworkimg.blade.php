@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
    <div class="Hui-article">
        <article class="cl pd-20">
            <div class="cl pd-5 bg-1 bk-gray mt-20">
                <span class="l">
                    <a class="btn btn-primary radius" onclick="picture_add('添加图片','/getworkimg?pid={{$pid}}')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加分类及展示图</a>
                    @if($status == 1)
{{--                    <a class="btn btn-primary radius" href="/showworkimg?status=0"><i class="Hui-iconfont">&#xe681;</i> 已下架图片</a>--}}
                    @else
                    <a class="btn btn-primary radius" href="/showworkimg?status=1"><i class="Hui-iconfont">&#xe681;</i> 发布中图片</a>
                    @endif
                </span>
                <span class="r">共有数据：<strong>{{$num}}</strong> 条</span>
            </div>
            <div class="mt-20">
                <table class="table table-border table-bordered table-bg table-hover table-sort">
                    <thead>
                    <tr class="text-c">
                        <th>类型</th>
                        <th>展示图</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                    <tbody>

                    @if($num)
                        @foreach($data as $key => $val)
                            <tr class="text-c">
                                <td><a href="/showworkimg?status=1&pid={{$val->id}}">{{$val -> name}}</a></td>
                                <td><img width="100" class="picture-thumb" src="{{$val -> imgpath}}"></td>
                                <td class="td-status">
                                    @if($val -> status == 1)
                                        <span class="label label-success radius">已发布</span>
                                    @else
                                        <span class="label label-defaunt radius">已下架</span>
                                    @endif
                                </td>

                                <td class="td-manage">
                                    @if($pid > 6)
                                        <a style="text-decoration:none" onClick="picture_stop(this,{{$val -> id}})" href="javascript:;"
                                           @if($val -> is_rec == 1)
                                           title="取消推荐"
                                           @else
                                           title="推荐"
                                           @endif
                                        >
                                            <i class="Hui-iconfont">
                                                @if($val -> is_rec == 1)
                                                    &#xe648;
                                                @else
                                                    &#xe649;
                                                @endif
                                            </i></a>
                                    @endif
                                    <a style="text-decoration:none" class="ml-5" onClick="picture_edit('修改图片','/getworkimg?id={{$val->id}}&pid={{$pid}}}')" href="javascript:;" title="修改"><i class="Hui-iconfont">&#xe60c;</i></a>
                                    <a style="text-decoration:none" class="ml-5" onClick="picture_del(this,{{$val -> id}})" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="text-c">
                            <td colspan="4">暂无图片!</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </article>
    </div>

    <!--_footer 作为公共模版分离出去-->
    <script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript">
        /*$('.table-sort').dataTable({
            "aaSorting": [[ 1, "desc" ]],//默认第几个排序
            "bStateSave": true,//状态保存
            "aoColumnDefs": [
                //{"bVisible": false, "aTargets": [ 3 ]} //控制列的隐藏显示
                {"orderable":false,"aTargets":[0,8]}// 制定列不参与排序
            ]
        });*/
        /*图片-添加*/
        function picture_add(title,url){
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }
        /*图片-下架*/
        function picture_stop(obj,id){
            var change = $(obj).attr('title');
            var status = change == '推荐' ? 1 : 0;
            var icon = change == '推荐' ? '&#xe648;' : '&#xe649;'
            var title = change == '推荐' ? '取消推荐' : '推荐'
            layer.confirm('确认要'+ change +'吗？',function(index){
                $.get('/recommend',{id:id,status:status},function(data){
                    if(data.data == '1'){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_stop(this,'+ id +')" href="javascript:;" title="'+ title +'"><i class="Hui-iconfont">'+ icon +'</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
                        $(obj).remove();
                        if(change == '推荐'){
                            layer.msg('推荐成功，请更改展示图!',{icon: 6,time:1000});
                        }else{
                            layer.msg('取消成功!',{icon: 5,time:1000});
                        }
                    }else{
                        layer.msg('操作失败!',{icon: 5,time:1000});
                    }
                })
            });
        }

        /*图片-发布*/
        function picture_start(obj,id){
            layer.confirm('确认要发布吗？',function(index){
                $.get('/changeimg',{id:id,type:'update',status:1},function(data){
                    if(data == '1'){
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="picture_stop(this,'+ id +')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
                        $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已发布</span>');
                        $(obj).remove();
                        layer.msg('已发布!',{icon: 6,time:1000});
                    }else{
                        layer.msg('发布失败!',{icon: 5,time:1000});
                    }
                })

            });
        }
        /*图片-删除*/
        function picture_del(obj,id){
            layer.confirm('确认要删除吗？',function(index){
                $.get('/changeimg',{id:id,type:'delete'},function(data){
                    if(data == 'del'){
                        $(obj).parents("tr").remove();
                        layer.msg('已删除!',{icon:1,time:1000});
                    }
                })
            });
        }
        /*图片修改*/
        function picture_edit(title,url){
            var index = layer.open({
                type: 2,
                title: title,
                content: url
            });
            layer.full(index);
        }

        $(".table tbody tr td img").click(function(e){
            layer.photos({ photos: {"data": [{"src": e.target.src}]} ,shift: 5});
        });
    </script>
@endsection
@section('title','一级分类')
@if($name != '')
    @section('banner','职位类型图>' . $name)
@else
    @section('banner','职位类型图')
@endif