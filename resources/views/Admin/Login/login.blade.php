<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<title>login</title>
<link rel="stylesheet" type="text/css" href="/login/css/normalize.css" />
<link rel="stylesheet" type="text/css" href="/login/css/demo.css" />
<!--必要样式-->
<link rel="stylesheet" type="text/css" href="/login/css/component.css" />
<!--[if IE]>
<script src="js/html5.js"></script>
<![endif]-->
</head>
<body>
		<div class="container demo-1">
			<div class="content">
				<div id="large-header" class="large-header">
					<canvas id="demo-canvas"></canvas>
					<div class="logo_box">
						<h3>校社通后台登录</h3>
						
						<form action="/dologin" method="post">
							<div class="input_outer">
								<span class="u_user"></span>
								<input name="username" value="{{old('username')}}" class="text" style="color: #FFFFFF !important" type="text" placeholder="请输入账户" autocomplete="off">
							</div>
							<div class="input_outer">
								<span class="us_uer"></span>
								<input name="password" class="text" style="color: #FFFFFF !important; position:absolute; z-index:100;" type="password" placeholder="请输入密码" autocomplete="off">
							</div>
							{{csrf_field()}}
							<div class="mb2"><input type="submit" value="登录" class="act-but submit" href="javascript:;" style="color: #FFFFFF;width:330px;border:none;"></div>
						</form>
					</div>
				</div>
			</div>
		</div><!-- /container -->
		<script>
				
	</script>
	<script type="text/javascript" src="/index/lib/layer/2.4/layer.js"></script>
		<script src="/login/js/tweenlite.min.js"></script>
		<script src="/login/js/easepack.min.js"></script>
		<script src="/login/js/raf.js"></script>
		<script src="/login/js/demo-1.js"></script>
  		<script src="/login/js/gt.js"></script>
	<script type="text/javascript"  src="/login/js/su.js"></script>
	 		@if(session('err'))
			<script>
			  alert('{{session('err')}}');
			</script>
			 @endif
  
  			@if(session('err3'))
 			 <script>
               alert('{{session('err3')}}');
   			 </script>
			 @endif
  
  			@if(session('err2'))
 			 <script>
               alert('session('err2')');
   			 </script>
			 @endif
</body>
</html>