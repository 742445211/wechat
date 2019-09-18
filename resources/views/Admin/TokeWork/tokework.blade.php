@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<div class="Hui-article">
    <style>
        tbody tr td:nth-child(4)>div{
            display: inline-block;
        }
    </style>
        <article class="cl pd-20">
            <div class="cl pd-5 bg-1 bk-gray"> <span class="l">
            <!--<a class="btn btn-primary radius" href="/signExport?type=sign" title="全部导出"><i class="Hui-iconfont">&#xe644;</i>导出</a>-->
        </span> <span class="r">共有群：<strong>{{$num}}</strong> 个</span> </div>
            <div class="mt-10">
            <table class="table table-border table-bordered table-hover table-bg">
                <thead>
                    <tr>
                        <th scope="col" colspan="6">我的工作群</th>
                    </tr>
                    <tr class="text-c">
                        <th>公告内容</th>
                        <th>成员数</th>
                        <th>今日签到</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $v)
                    <tr class="text-c">
                        <td>{{$v['title']}}</td>
                        <td>{{$v['num']}}</td>
                        <td>{{$v['sign']}}</td>
                        <td>
                            <div>
                                <form action="/workdetail#miao" method="post">
                                    <input type="hidden" name="groupid" value="{{$v['groupid']}}">
                                    {{ csrf_field() }}
                                    <input class="btn btn-success" type="submit" value="进入群聊">
                                </form>
                            </div>
                            <div onclick="dissolution($(this))">
                                <input type="hidden" name="groupid" value="{{$v['groupid']}}">
                                {{ csrf_field() }}
                                <input class="btn btn-danger" type="button" value="解散群组">
                            </div>
                            <div>
                                <a title="查看群公告" href="/gg/{{$v['groupid']}}" class="btn btn-primary ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe715;</i></a>
                            </div>
                            <div>
                                <a class="btn btn-primary radius" href="/signExport/{{$v['groupid']}}" title="导出详情"><i class="Hui-iconfont">&#xe644;</i>导出</a>
                            </div>
                            <div>
                                <a class="btn btn-primary radius" href="javascript:;" onclick="sign($(this))" data-status="{{$v['issign']}}" data-group="{{$v['groupid']}}"><i class="Hui-iconfont">&#xe644;</i>
                                    @if($v['issign'] == 0)
                                    开启签到
                                    @elseif($v['issign'] == 1)
                                    关闭签到
                                    @elseif($v['issign'] == 2)
                                    开启签退
                                    @elseif($v['issign'] == 3)
                                    关闭签退
                                    @endif
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </article>
    </div>
        <script type="text/javascript">
            ws = new WebSocket("wss://www.xiaoshetong.cn:8282");
                // 服务端主动推送消息时会触发这里的onmessage
                ws.onmessage = function(e){
                    // json数据转换成js对象
                    var data = eval("("+e.data+")");
                    var type = data.type || '';
                    console.log(data);
                    switch(type){
                        // Events.php中返回的init类型的消息，将client_id发给后台进行uid绑定
                        case 'init':
                            // 利用jquery发起ajax请求，将client_id发给后端进行uid绑定
                            $.get('/bindid',{client_id:data.client_id},function(data){
                                console.log(data);
                            })
                            break;
                        case 'say':
                            alert('有新的群消息');
                        // 当mvc框架调用GatewayClient发消息时直接alert出来
                        default :
                            console.log('心跳中');
                    }
                };

                function dissolution(res){
                    var index = parent.layer.getFrameIndex(window.name);
                    var groupid = res.find('input[name="groupid"]').val();
                    var token = res.find('input[name="_token"]').val();
                    var time = new Date().getTime();
                    $.get('/delwork',{groupid:groupid,token:token,time:time},function(data){
                        if(data.code == '1'){
                            parent.layer.msg(data.result,{time:1000});  //提示
                            setTimeout('parent.location.reload()',500); //刷新父页面
                            parent.layer.close(index);   //关闭当前页面
                        }else{
                            parent.layer.msg(data.result);  //提示
                            parent.layer.close(index);   //关闭当前页面
                        }
                    },'json')
                }

                function sign(e) {
                    var index = parent.layer.getFrameIndex(window.name);
                    var groupid = e.attr('data-group')
                    var status = parseInt(e.attr('data-status'))
                    $.get('/isSign',{groupid:groupid,status:(status+1)},function(data){
                        if(data.code == '1'){
                            parent.layer.msg('已开启签到，请注意关闭',{time:0});  //提示
                            setTimeout('parent.location.reload()',1000); //刷新父页面
                            parent.layer.close(index);   //关闭当前页面
                        }else if(data.code == '2'){
                            parent.layer.msg('已关闭签到',{time:0});  //提示
                            setTimeout('parent.location.reload()',1000); //刷新父页面
                            parent.layer.close(index);   //关闭当前页面
                        }else if(data.code == '3'){
                            parent.layer.msg('已开启签退，请注意关闭',{time:0});  //提示
                            setTimeout('parent.location.reload()',1000); //刷新父页面
                            parent.layer.close(index);   //关闭当前页面
                        }else if(data.code == '0'){
                            parent.layer.msg('已关闭签退',{time:0});  //提示
                            setTimeout('parent.location.reload()',1000); //刷新父页面
                            parent.layer.close(index);   //关闭当前页面
                        }
                    },'json')
                }
        </script>
@endsection
@section('title','工作群列表')
@section('banner','工作群列表')

