<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--[if lt IE 9]>
    <script type="text/javascript" src="lib/html5.js"></script>
    <script type="text/javascript" src="lib/respond.min.js"></script>
    <![endif]-->
    <link href="/index/static/h-ui/css/H-ui.min.css" rel="stylesheet" type="text/css" />
    <link href="/index/static/h-ui.admin/css/H-ui.admin.css" rel="stylesheet" type="text/css" />
    <link href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/index/layui/css/layui.css">
    <!--[if IE 6]>
    <script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script><![endif]-->
    <title>兼职详情查看</title>
</head>
<body>
<div class="cl pd-20" style=" background-color:#5bacb6">
    <div style="text-align:center">@if(count($data) != 0){{$data[0]['work']['title']}}@endif</div>
</div>

<div class="pd-20">
    <div style="min-height: 200px">
        <table class="table">
            <tbody>
            @if(count($data) == 0)
                <tr class="text-c">
                    <td colspan="10">暂无数据!</td>
                </tr>
            @endif
            @foreach($data as $val)
                <tr>
                    <td><img height="100" src="{{$val->imgpath}}" alt=""></td>
                    <td class="td-manage"><a style="text-decoration:none" onClick="img_delete(this,{{$val->id}})" href="javascript:;" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></a></td>
                </tr>
            @endforeach
            <input type="hidden" data-workid="{{$workid}}">
            </tbody>
        </table>
    </div>
    <div class="layui-upload">
        <button type="button" class="layui-btn layui-btn-normal" id="testList">选择多文件</button>
        <div class="layui-upload-list">
            <table class="layui-table">
                <thead>
                    <tr>
                        <th>文件名</th>
            <th>大小</th>
            <th>状态</th>
            <th>操作</th>
            </tr>
           </thead>
      <tbody id="demoList"></tbody>
        </table>
        </div>
    <button type="button" class="layui-btn" id="btns">开始上传</button>
</div>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script>
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script>
<script type="text/javascript" src="/index/layui/layui.all.js"></script>
<!--/_footer /作为公共模版分离出去-->
<script>
    $("img").click(function(e){
        layer.photos({ photos: {"data": [{"src": e.target.src}]} ,shift: 5});
    });

    function img_delete(obj,id) {
        $.ajax({
            type:'get',
            url:'/delimg',
            data:{id:id},
            success:function (res) {
                if(res.code == 0){
                    layer.msg('删除成功');
                    $(obj).parents('tr').remove();
                }
            }
        })
    }

    var imgpath = [];
    layui.use('upload', function(){
        var upload = layui.upload;
        var demoListView = $('#demoList')
        //执行实例
        var uploadListIns = upload.render({
            elem: '#testList' //绑定元素
            ,auto:false
            ,bindAction: '#btns'
            ,multiple:true
            ,headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            ,url: '/uploadimg' //上传接口
            ,choose: function(obj){
                var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
                //读取本地文件
                obj.preview(function(index, file, result) {
                    var tr = $(['<tr id="upload-' + index + '">'
                        , '<td><img height="100" src="'+ result +'" alt=""></td>'
                        , '<td>' + (file.size / 1014).toFixed(1) + 'kb</td>'
                        , '<td>等待上传</td>'
                        , '<td>'
                        , '<button class="layui-btn layui-btn-mini demo-reload layui-hide">重传</button>'
                        , '<button class="layui-btn layui-btn-mini layui-btn-danger demo-delete">删除</button>'
                        , '</td>'
                        , '</tr>'].join(''));

                    //单个重传
                    tr.find('.demo-reload').on('click', function () {
                        obj.upload(index, file);
                    });

                    //删除
                    tr.find('.demo-delete').on('click', function () {
                        delete files[index]; //删除对应的文件
                        tr.remove();
                        uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                    demoListView.append(tr);
                });
            }
            ,allDone: function(obj){ //当文件全部被提交后，才触发
                console.log(obj.total); //得到总文件数
                console.log(obj.successful); //请求成功的文件数
                console.log(obj.aborted); //请求失败的文件数
                addimg()
            }
            ,done: function(res){
                imgpath.push(res.img);
            }
            ,error: function(){
                //请求异常回调
            }
        });
    });

    function addimg() {
        var id = $('input[type="hidden"]').attr('data-workid')
        $.ajax({
            type:'get',
            url:'/addjobimg',
            data:{
                workid:id,img:imgpath
            },
            success(res){
                if(res.code == 0){
                    layer.msg('上传成功')
                }else if(res.code == 1){
                    layer.msg('更新成功')
                }else{
                    layer.msg('上传失败')
                }
            }
        })
    };
</script>
</body>
</html>