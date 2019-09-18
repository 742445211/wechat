@extends('Admin.PublicViews.AdminPublic.publicviews')
@section('admin')
<link rel="stylesheet" type="text/css" href="/index/lib/Hui-iconfont/1.0.8/iconfont.css" />
</head>
    <link rel="stylesheet" href="http://cache.amap.com/lbs/static/main1119.css"/>
    <script type="text/javascript"
            src="http://webapi.amap.com/maps?v=1.3&key=71a87b943a791c63d6eaec2f28f60a31&plugin=AMap.Autocomplete"></script>
    <script type="text/javascript" src="http://cache.amap.com/lbs/static/addToolbar.js"></script>
<article class="cl pd-20">
   <div id="container"></div>
<div id="myPageTop">
    <table>
        <tr>
           
            <td class="column2">
                <label>左击获取经纬度：</label>
            </td>
        </tr>
        <tr>
            
            <td class="column2">
                <input type="text" readonly="true" id="lnglat">
            </td>
        </tr>
    </table>
</div>
    <form method="get" action="/workedit/{{$id}}" class="form form-horizontal" enctype="multipart/form-data">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">地址：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" id="address" name="address" placeholder="点击地图选取地址,如不准确可修改" style="width:400px">
                <input type="hidden" name="map" id="map" value="">
                <input type="hidden" name="addresslite" id="addresslite" value="">
                <input type="hidden" name="id" value="{{$id}}">>
            </div>
        </div>
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
	var map = new AMap.Map("container", {
        resizeEnable: true
    });
    //为地图注册click事件获取鼠标点击出的经纬度坐标
    var clickEventListener = map.on('click', function(e) {
        document.getElementById("lnglat").value = e.lnglat.getLng() + ',' + e.lnglat.getLat();
		var address = e.lnglat.getLng() + ',' + e.lnglat.getLat();
		$('#map').val(address);
		$.get('/getadd',{address:address},function(data){
			if(data.regeocode.formatted_address){
				$('#address').val(data.regeocode.formatted_address);   // 详细地址
				//市加区
				$('#addresslite').val(data.regeocode.addressComponent.city+'/'+data.regeocode.addressComponent.district);
			}else{
				$('#address').val('获取失败!');
			}
		})
    });
    var auto = new AMap.Autocomplete({
        input: "tipinput"
    });
    AMap.event.addListener(auto, "select", select);//注册监听，当选中某条记录时会触发
    function select(e) {
        if (e.poi && e.poi.location) {
            map.setZoom(15);
            map.setCenter(e.poi.location);
        }
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
@section('title','地址选取')
@section('banner','地址选取')