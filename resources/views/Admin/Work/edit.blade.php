@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />
</head>
<!--<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/skin/default/skin.css" id="skin" />-->
<!--<link rel="stylesheet" type="text/css" href="/index/static/h-ui.admin/css/style.css" />   -->
<article class="cl pd-20">
    <form method="post" action="/workup" class="form form-horizontal" enctype="multipart/form-data">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">工作地点：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" name="address" id="address"

                 value="<?php if($address){
                        echo $address;
                    }else{
                            echo $list -> address;
                    }?>"

                 style="width:400px" readonly placeholder="点击设置地址获取地址"> <a href="http://www.xiaoshetong.cn/editmap/{{$list -> id}}" class="btn btn-success radius">设置地址</a>
                <input type="hidden" name="map" id="map" value="<?php 
                        if($map){
                            echo $map;
                        }else{
                            echo $list -> map;
                        }
                ?>">
                <input type="hidden" name="addresslite" id="addresslite" value="<?php 
                    if($addresslite){
                        echo $addresslite;
                    }else{
                        echo $list -> addresslite;
                    }
                ?>">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">标题：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$list -> title}}" name="title" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">内容：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="content" class="textarea"  placeholder="说说招聘要求等..150字" onKeyUp="textarealength(this,100)" style="width:400px">{{$list -> content}}</textarea>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">需要人数：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$list -> number}}" name="number" style="width:400px">
            </div>
        </div>
            <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">薪资：</label>
            <div class="formControls col-xs-8 col-sm-9" >
                <input type="text" class="input-text" value="{{$list -> price}}" name="price" style="width:300px">
                &nbsp;/ &nbsp;
                <?php 
                    $times = DB::table('time') -> where('adminid','=',session('userid')) -> get();
                ?>
                <select name="days" style="width:70px;height:30px">
                    @foreach($times as $key => $val)
                        <option value="{{$val->id}}" @if($val -> id == $list -> days) selected @endif >{{$val->type}}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">开始时间：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="date" class="input-text" value="{{$list -> startdate}}" name="startdate" style="width:400px">
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">结束时间：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="date" class="input-text" value="{{$list -> enddate}}" name="enddate" style="width:400px">
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">热门标签：</label>
            <div class="formControls col-xs-8 col-sm-9">
        <div class="skin-minimal">
            <?php 
                $hots = DB::table('hots') -> where('adminid','=',session('userid')) -> get();
                $hotsArr = explode(',',rtrim($list -> hots,','));
            ?>
          @foreach($hots as $keys=>$value)
                <input type="checkbox" id="checkbox-{{$keys}}" name="hots[]"
                    <?php 
                            if(in_Array($value->id,$hotsArr)){ ?>
                                checked 
                          <?php  } ?>
                value="{{$value->id}}">

                <label for="checkbox-{{$keys}}">{{$value->type}}</label>
          @endforeach
        </div>
            </div>
        </div>
        
        <div class="row cl">
            <?php 
                $types = DB::table('types') -> where('adminid','=',session('userid')) -> get();
                $typesArr = explode(',',rtrim($list -> types,','));
            ?>
            <label class="form-label col-xs-4 col-sm-3">类型标签：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="skin-minimal">
                  @foreach($types as $k=>$v)
                        <input type="checkbox" id="checkbox{{$k}}" name="types[]" @if(in_Array($v->id,$typesArr)) checked @endif value="{{$v->id}}">
                        <label for="checkbox{{$k}}">{{$v->type}}</label>
                  @endforeach
                </div>
            </div>
        </div>
        
        <div class="row cl">
            <?php 
                $cate1 = DB::table('position') -> where('level',0) -> get();
                $cate2 = DB::table('position') -> where('level',1) -> get();
                $cate  = DB::table('position') -> where('level',2) -> get();
                $pid1  = DB::table('position') -> where('id',$list->post_id) -> value('pid');
                $pid2  = DB::table('position') -> where('id',$pid1) -> value('pid');
            ?>
            <label class="form-label col-xs-4 col-sm-3">兼职分类：</label>
            <div class="formControls col-xs-8 col-sm-9" >
                <select name="cate1" style="width:130px;height:30px">
                    @foreach($cate1 as $val)
                        <option value="{{$val->id}}" @if($pid2 == $val->id) selected @endif>{{$val->name}}</option>
                    @endforeach
                </select>
                <select name="cate2" style="width:130px;height:30px">
                    @foreach($cate2 as $val)
                        <option value="{{$val->id}}" @if($pid1 == $val->id) selected @endif>{{$val->name}}</option>
                    @endforeach
                </select>
                <select name="cate" style="width:130px;height:30px">
                    @foreach($cate as $val)
                        <option value="{{$val->id}}" @if($list->post_id == $val->id) selected @endif>{{$val->name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
       
        

        
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">联系人：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$list -> contacts}}" name="contacts" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">手机：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$list -> phone}}" name="phone" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">公司：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$list -> groupinfo}}" name="group" style="width:400px">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">公司简介：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="{{$list -> grouplite}}" name="grouplite" style="width:400px">
            </div>
        </div>
        <input type="hidden" name="id" value="{{$id}}">
        {{csrf_field()}}
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
            </div>
        </div>
    </form>
</article>
<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/jquery.validate.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/validate-methods.js"></script> 
<script type="text/javascript" src="/index/lib/jquery.validation/1.14.0/messages_zh.js"></script> 
<script type="text/javascript" src="/index/static/h-ui.admin/js/H-ui.admin.page.js"></script> 
<script type="text/javascript">
	
    if(session('adderr')){
        alert('请填写完整信息!');
    }
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