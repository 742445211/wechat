@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<style>
  .icon{
    float:left;
    display:none;
  }
</style>
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />
</head>
<!--<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />-->
<!--<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/style.css" />   -->
<article class="cl pd-20">
    <form method="post" action="/workadd" class="form form-horizontal" enctype="multipart/form-data">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">工作地点：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" name="address" id="address" value="{{isset($address)?$address:''}}" style="width:400px" readonly placeholder="点击设置地址获取地址"> <a href="http://www.xiaoshetong.cn/getmap" class="btn btn-success radius">设置地址</a>
                <input type="hidden" name="map" id="map" value="{{isset($map)?$map:''}}">
                <input type="hidden" name="addresslite" id="addresslite" value="{{isset($addresslite)?$addresslite:''}}">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">标题：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{old('title')}}" name="title" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">图标：</label>
            <div class="formControls col-xs-8 col-sm-9">
              	<div style="width:415px">
                    <input type="file" name="file">
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">内容：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="content" class="textarea"  placeholder="说说招聘要求等..150字" onKeyUp="textarealength(this,100)" style="width:400px">{{old('content')}}</textarea>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">需要人数：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{old('number')}}" name="number" style="width:400px">
            </div>
        </div>
            <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">薪资：</label>
            <div class="formControls col-xs-8 col-sm-9" >
                <input type="text" class="input-text" value="{{old('price')}}" name="price" style="width:300px">
                &nbsp;/ &nbsp;
                <select name="days" style="width:70px;height:30px">
                    @foreach($times as $key => $val)
                        <option value="{{$val->id}}">{{$val->type}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">开始时间：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="date" class="input-text" name="startdate" style="width:400px">
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">结束时间：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="date" class="input-text" name="enddate" style="width:400px">
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">热门标签：</label>
            <div class="formControls col-xs-8 col-sm-9">
        <div class="">
          @foreach($hots as $keys=>$value)
                <input type="checkbox" id="checkbox-{{$keys}}" name="hots[]" value="{{$value->id}}">
                <label for="checkbox-{{$keys}}" style="margin-left: 5px;">{{$value->type}}</label>
          @endforeach
        </div>
            </div>
        </div>
        
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">类型标签：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="">
                  @foreach($types as $k=>$v)
                        <input type="checkbox" id="checkbox{{$k}}" name="types[]" value="{{$v->id}}">
                        <label for="checkbox{{$k}}" style="margin-left: 5px;">{{$v->type}}</label>
                  @endforeach
                </div>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">兼职分类：</label>
            <div class="formControls col-xs-8 col-sm-9" >
                <div id="select">
                    <select name="cate0" id="level0" style="width:130px;height:30px" onchange="select(this,0)">
                        @foreach($cates as $val)
                            <option value="{{$val['id']}}">{{$val['name']}}</option>
                        @endforeach
                    </select>
                    <select name="cate1" id="level1" style="width:130px;height:30px" onchange="select(this,1)"></select>
                    <select name="post_id" id="level2" style="width:130px;height:30px" onchange="select(this,2)"></select>
                </div>
            </div>
        </div>
        
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">联系人：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{old('contacts')}}" name="contacts" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">手机：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{old('phone')}}" name="phone" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">公司：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{old('group')}}" name="group" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">公司简介：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{old('group')}}" name="grouplite" style="width:400px">
            </div>
        </div>
        {{csrf_field()}}
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
            </div>
        </div>
    </form>
</article>
<script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script> 
 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/jquery.validate.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/validate-methods.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/messages_zh.js"></script> 
{{--<script type="text/javascript" src="/index/static/h-ui/js/H-ui.js"></script>--}}
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script>
{{--<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script>--}}
<script type="text/javascript" src="/index/layui/layui.all.js"></script>

<script type="text/javascript">
  	function ok(obj,id)
    {
      	var all = $(obj);
      	$("input[type='radio']").attr('checked',false);
      	$("input[type='radio']").next().css('opacity','1');
        $('#' + id ).prop('checked',true);
     	all.css('opacity','0.3');
    }
    function tijiao(id,level){
        $.ajax({
            type:'get',
            url:'/select',
            data:{id:id},
            success:function (res) {
                if(res.result != ''){
                    var select = ''
                    $.each(res.result,function () {
                        //console.log(this)
                        select += '<option value="'+ this.id +'">'+ this.name +'</option>'
                    })

                    var id = '#level' + (level+1)
                    console.log($(id))
                    $(id).html(select)
                }else{
                    $('#level' + (level+2)).html('')
                    $('#level' + (level+1)).html('')
                }
            }
        })
    }
    function select(obj,level)
    {
  	    var id = $(obj).val()
        tijiao(id,level)
    }

    select($('#level0'),0)
    $(function(){
    $('.skin-minimal input').iCheck({
        checkboxClass: 'icheckbox-blue',
        radioClass: 'iradio-blue',
        increaseArea: '20%'
    });
});
</script> 
@endsection
@section('title','发布招聘')
@section('banner','发布招聘')