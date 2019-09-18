
<style type="text/css">
	bady{
		margin:0;
		padding:0;

	}
	.top{
		position: fixed;
		width:99%;
		height:50px;
		/*border:1px solid red;*/
		background-color: #1db682;
		color:white;
	}
	.bom{
		width:99%;
		/*border:1px solid red;*/
		display: flex;
		flex-direction: column-reverse;
	}
	.me{
		height:100px;
		width:52%;
		/*border:1px solid green;*/
		margin-bottom: 7px;
		float:right;
		margin-left:48%;

	}
	.headers{
		width:90px;
		height:90px;
		float:right;
		margin-right:10px;

	}
	.usernames{
		float:right;
		margin-right:10px;
	}
	.msgs{
		float:right;
		margin-right:10px;
	}
	.times{
		float:right;
		margin-right:10px;
	}
	.you{
		height:100px;
		width:52%;
		/*border:1px solid green;*/
		margin-bottom: 7px;
		float:left;
	}
	.header{
		width:90px;
		height:90px;
		float:left;
		margin-right:10px;
	}
	.you span{
		float:left;
		margin-right:10px;
	}
	#sendmsg{
		position:fixed;
		bottom: 10px;
		left:40%;
	}
	.btns{
		position:fixed;
		bottom: 10px;
		left:61%;
		height:35px;

	}
</style>


						<div class="top">
			            	<span class="title">群名称：{{$workmsg['groupInfo']['group_title']}}</span><br>
			            	<span>在线人数：{{$workmsg['onlineNum']}}人</span>
			            	<span style="float:right"><a href="/tokework">返回群列表</a></span>
		            	</div>
		            	<div style="height:55px;"></div>
		            	<hr>
		            	<div class="bom">
		            		@foreach($workmsg['oldMsg'] as $val)
		            		
		            		@if($val['userid'] == session('userid'))
		            		<div class="me">
		            			<img class="headers" src="{{$val['header']}}">
		            			<span class="usernames">{{$val['username']}}</span><br>
		            			<span class="msgs" style="color:green">{{$val['msg']}}</span><br>
		            			<span class="times">{{$val['sendtime']}}</span>
		            		</div>
		                  @else
                            <div class="you">
                                <img class="header" src="{{$val['header']}}">
                                <span class="username">{{$val['username']}}</span><br>
                                <span class="msg" style="color:green">{{$val['msg']}}</span><br>
                                <span>{{$val['sendtime']}}</span>
                            </div>
		            		@endif
		            		@endforeach
		            		<input type="hidden" id="groupid" value="{{$groupid}}">
		            		
					<textarea id="sendmsg" cols='45' placeholder="输入要发送的消息"></textarea>
		            <button class="btn btn-success btns"  onclick="sendMsg()">发送</button>
		            	</div>
		            	<div style="height:60px"></div>
		            	<a name="miao"></a>
		            

        <script type="text/javascript" src="/index/lib/jquery/1.9.1/jquery.min.js"></script> 
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
                            location.reload();
                        // 当mvc框架调用GatewayClient发消息时直接alert出来
                        default :
                            console.log('心跳中');
                    }
                };
        	 function sendMsg(){
        		msg = $('#sendmsg').val();
        		groupid = $('#groupid').val();
        		//发送消息  发送到后端进行发送
        		$.get('/godetail',{msg:msg,groupid:groupid},function(data){
        	 		//console.log(data);
        	 		location.reload();
        		 },'json')
        	}

        </script>
