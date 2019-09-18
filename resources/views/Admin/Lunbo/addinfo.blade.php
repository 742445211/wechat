<!--_meta 作为公共模版分离出去-->
<!DOCTYPE HTML>
<html>
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit|ie-comp|ie-stand">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
<meta http-equiv="Cache-Control" content="no-siteapp" />
<link rel="Bookmark" href="favicon.ico" >
<link rel="Shortcut Icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui/css/H-ui.min.css" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/H-ui.admin.css" />
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />
<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/style.css" />
<title>增加条目</title>
</head>
<body>
<div class="page-container">
        <div class="row cl">
            <label class="col-xs-4 col-sm-2">条目信息：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="content" class="textarea"  placeholder="如：1、本软件保证兼职信息的准确性和真实性。 (为了更好的显示，请每次只添加一条，多条分次添加)" datatype="*10-100" dragonfly="true" nullmsg="条目不能为空！" value="" id="text" onKeyUp="textarealength(200)" maxlength="200"></textarea>
                <p class="textarea-numberbar"><em class="textarea-length" id="em">0</em>/200</p>
            </div>
        </div>

		<div class="row cl">
			<div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2" style="margin-top:30px;">
				<button onClick="article_save();" id="btns" class="btn btn-secondary radius" type="button"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
			</div>
		</div>
</div>
</div>
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script> 
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script> 
 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/jquery.validate.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/validate-methods.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/messages_zh.js"></script> 
<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script> 
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script> 
<script type="text/javascript">
    function article_save()   //提交
    {
        // $('#btns').attr('disabled','true');
        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
        var content = $('#text').val();
        if(!content.length){
            parent.layer.msg('请填写内容!');  //提示
            return false;
        }
        $.get('/addok',{content:content},function(data){
            if(data == '1'){
                parent.location.reload(); //刷新父页面
                parent.layer.msg('添加成功!');  //提示
                parent.layer.close(index);   //关闭当前页面
            }else if(data == '2'){
                parent.layer.msg('填写内容!!!!!!');  //提示
            }else{
                 parent.layer.msg('添加失败!')  //提示
            }
        })
    }
    function textarealength(length)
    {
        var length = $('#text').val();
        var em = $('#em').html(length.length);
        if(em>=200){
            $('#em').html(200);
        }
    }
    
</script>
</body>
</html>