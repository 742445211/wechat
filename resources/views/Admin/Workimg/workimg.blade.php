<!--_meta 作为公共模版分离出去-->
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="Bookmark" href="favicon.ico" >
    <link rel="Shortcut Icon" href="favicon.ico" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="lib/html5.js"></script>
    <script type="text/javascript" src="lib/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="/index/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />

    <link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/style.css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="http://lib.h-ui.net/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script><![endif]-->
    <!--/meta 作为公共模版分离出去-->

    <title>新增图片</title>
    <link href="/index/lib/webuploader/0.1.5/webuploader.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="/index/layui/css/layui.css">
</head>
<body>
<div class="page-container">
    <form class="form form-horizontal" id="form-article-add" enctype =“multipart/form-data”>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">类型名：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="@if(isset($data)){{$data['name']}}@endif" placeholder="输入该图片所属的类型" id="name" name="name">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">介绍：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="@if(isset($data)){{$data['intro']}}@endif" placeholder="输入该图片所属类型的介绍" id="intro" name="intro">
            </div>
        </div>

        <div class="row cl"  @if($pid == 0)style="display: none"@endif>
            <label class="form-label col-xs-4 col-sm-2">选择父级分类：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <select name="cate" style="width:400px;height:30px" id="select">
                    <option value="0">请选择</option>
                    @foreach($post as $val)
                        <option value="{{$val->id}}"@if($val->id==$pid)selected="selected"@endif>{{$val->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">图片上传：</label>
            <div class="formControls col-xs-8 col-sm-9">
            <div class="img">
                @if(isset($data))<img width="100" class="picture-thumb" src="{{$data['imgpath']}}">@endif
            </div>
            <button type="button" class="layui-btn" id="test1">
                上传图片
            </button>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <?php
                $adminid = session('userid');
                ?>
                @if($has)
                    <input type="hidden" name="adminid" id="adminid" value="update" data-id="{{$id}}">
                @else
                    <input type="hidden" name="adminid" id="adminid" value="insert">
                @endif
                <button id="btns" class="btn btn-secondary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
                <button id="xiugai" class="btn btn-secondary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> 修改</button>
            </div>
        </div>
    </form>
</div>
</div>

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/index/layui/layui.all.js"></script>

<script>
    var imgpath = ''
    layui.use('upload', function(){
        var upload = layui.upload;
        //执行实例
        var uploadInst = upload.render({
            elem: '#test1' //绑定元素
            ,auto:false
            ,bindAction: '#btns'
            ,headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            ,url: '/uploadimg' //上传接口
            ,choose: function(obj){
                obj.preview(function (index,file,result) {
                    var html = '<img width="100" class="picture-thumb" src="'+ result +'">';
                    $('.img').html(html)
                })
            }
            ,done: function(res){
                imgpath = res.img
                addimg(res.token)
            }
            ,error: function(){
                //请求异常回调
            }
        });
    });

    function addimg(token) {
        var name = $('#name').val();
        var intro = $('#intro').val();
        var type = $('#adminid').val()
        var id = $('#adminid').attr('data-id')
        var pid = $("#select").find("option:selected").val()
        var level = {{$level + 1}}
        $.ajax({
            type:'post',
            url:'/addworkimg',
            headers: {
                'X-CSRF-TOKEN': token
            },
            data:{
                name:name,intro:intro,img:imgpath,type:type,id:id,pid:pid,level:level
            },
            contentType : "application/x-www-form-urlencoded",
            dataType : "json",
            success(res){
                if(res.code == 0){
                    layer.msg('上传成功')
                }else if(res.code == 1){
                    layer.msg('更新成功')
                }else{
                    layer.msg('上传失败')
                }
            }
        });

    }
    $('#xiugai').click(function () {
        var name = $('#name').val();
        var intro = $('#intro').val();
        var type = $('#adminid').val()
        var id = $('#adminid').attr('data-id');
        var pid = $("#select").find("option:selected").val();
        var level = {{$level}};
        var imgpath = $('.img img').attr('src')
        console.log(imgpath)
        $.ajax({
            type:'post',
            url:'/addworkimg',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data:{
                name:name,intro:intro,img:imgpath,type:type,id:id,pid:pid,level:level
            },
            contentType : "application/x-www-form-urlencoded",
            dataType : "json",
            success(res){
                if(res.code == 0){
                    layer.msg('上传成功')
                }else if(res.code == 1){
                    layer.msg('更新成功')
                }else{
                    layer.msg('上传失败')
                }
            }
        });
    })

</script>
</body>
</html>
