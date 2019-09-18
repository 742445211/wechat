<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//	//登录页面加载
//	Route::get('/logins','Admin\Login\LoginController@index');
//	//处理登录
//	Route::post('/dologin','Admin\Login\LoginController@dologin');
//	//退出登录
//	Route::get('/outlogin','Admin\Login\LoginController@outlogin');
//
//	Route::get('/phpinfo',function (){
//	    echo phpinfo();
//    });
//
//	Route::get('/test',function (){
//	    $data = \App\HomeUser::get();
//	    return $data;
//    });
//
//    Route::get('/buffer/{workId}','Inter\Manage\ManageController@getQRCode');
//
//	Route::group(['middleware'=>'login'],function(){
//
//		//加载后台首页   此处开始加入session中间件
//		Route::get('/admin','Admin\Index\IndexController@index');
//		//用户列表
//		Route::resource('/userlist','Admin\User\UserController');
//		//更改用户账号状态
//		Route::get('/userstatus','Admin\User\UserController@status');
//		//删除用户
//		Route::get('/userdel','Admin\User\UserController@del');
//		//查看删除的用户
//		Route::get('/deluser','Admin\User\UserController@deluser');
//		//撤回删除的用户
//		Route::get('/userdelback','Admin\User\UserController@userdelback');
//		//彻底删除用户
//		Route::get('/userdeldel','Admin\User\UserController@userdeldel');
//		//查看详细信息
//		Route::get('/usershow/{id}/{user?}','Admin\User\UserController@usershow');
//		//进入短信群发界面
//		Route::get('/sendsms','Admin\User\UserController@sendsms');
//		//搜索群发用户
//		Route::get('/smsss','Admin\User\UserController@smsss');
//
//		//首页轮播图
//		Route::get('/lunbo','Admin\User\UserController@lunbo');
//		//添加轮播图
//		Route::get('/addlunbo','Admin\User\UserController@addlunbo');
//		//轮播图上传到系统目录
//		Route::post('/getimgs','Admin\User\UserController@getimgs');
//		//点击保存最终提交
//		Route::get('/upimgres','Admin\User\UserController@upimgres');
//		//轮播图状态的修改
//		Route::get('/setstatus','Admin\User\UserController@setstatus');
//		//轮播图ajaxj修改
//		Route::get('/delajax','Admin\User\UserController@delajax');
//		//关于信息展示
//		Route::get('/getinfo','Admin\User\UserController@getinfo');
//		//添加关于信息页面
//		Route::get('/addinfo','Admin\User\UserController@addinfo');
//		//关于状态
//		Route::get('/infostatus','Admin\User\UserController@infostatus');
//		//ajax删除关于条目
//		Route::get('/delinfo','Admin\User\UserController@delinfo');
//		//处理添加
//		Route::get('/addok','Admin\User\UserController@addok');
//      	//头像的增删改查    查看
//      	Route::get('/header','Admin\User\UserController@header');
//      	//添加头像
//      	Route::get('/addheader','Admin\User\UserController@addheader');
//      	//处理头像添加
//      	Route::post('/upheader','Admin\User\UserController@upheader');
//      	//处理类型图片添加修改
//        Route::post('/addworkimg','Admin\User\UserController@addWorkImg');
//        //上传类型图
//        Route::get('/getworkimg','Admin\User\UserController@getWorkImg');
//        //查看类型图
//        Route::get('/showworkimg','Admin\User\UserController@showWorkImg');
//        //上传图片
//        Route::post('/uploadimg','Admin\User\UserController@uploadImg');
//        //修改图片状态
//        Route::get('changeimg','Admin\User\UserController@changImg');
//        //推荐
//        Route::get('recommend','Admin\User\UserController@recommend');
//
//
//		//收款类型
//		Route::get('/moneytype','Admin\Type\TypeController@index');
//		//收款方式状态
//		Route::get('/moneystatus','Admin\Type\TypeController@moneystatus');
//		//删除类型
//		Route::get('/moneydel','Admin\Type\TypeController@moneydel');
//		//类型修改
//		Route::get('/typeedit','Admin\Type\TypeController@update');
//		//类型添加
//		Route::get('/typeadd','Admin\Type\TypeController@typeadd');
//		//处理类型添加
//		Route::get('/dotypeadd','Admin\Type\TypeController@dotypeadd');
//
//
//		//兼职列表
//		Route::resource('/worklist','Admin\Work\WorkController');
//		//职位状态管理
//		Route::get('/workstatus','Admin\Work\WorkController@workstatus');
//		//职位ajax删除
//		Route::get('/workdel','Admin\Work\WorkController@workdel');
//		//发布兼职信息处理
//		Route::post('workadd','Admin\Work\WorkController@workadd');
//		//ajax获取地理坐标
//		Route::get('/getadd','Admin\Work\WorkController@getadd');
//		//获取地址
//		Route::get('/getmap','Admin\Work\WorkController@getmap');
//		//职位编辑
//		Route::get('/workedit/{id}','Admin\Work\WorkController@workedit');
//		//职位修改确认
//		Route::post('/workup','Admin\Work\WorkController@workup');
//		//修改时的地图调用
//		Route::get('/editmap/{id?}','Admin\Work\WorkController@editmap');
//		//查看兼职展示图
//        Route::get('jobimg/{id}','Admin\Work\WorkController@jobImage');
//        //删除展示图
//        Route::get('delimg','Admin\Work\WorkController@delImg');
//        //新增展示图
//        Route::get('addjobimg','Admin\Work\WorkController@addJobImg');
//        //下拉选框
//        Route::get('/select','Admin\Work\WorkController@select');
//
//
//        //添加推荐工作图片
//        Route::post('/addrecimg','Admin\Work\WorkController@addRecImg');
//        //添加修改页面
//        Route::get('/showrec','Admin\Work\WorkController@showRecImg');
//
//		//报名管理
//		Route::get('/cplt/{status}','Admin\Cplt\CpltController@index');
//		//报名状态审核管理
//		Route::get('/cpltstatus','Admin\Cplt\CpltController@cpltstatus');
//		//面试状态通过拒绝
//		Route::get('/cpltms','Admin\Cplt\CpltController@cpltms');
//		//报名信息删除
//		Route::get('/cpltdel','Admin\Cplt\CpltController@cpltdel');
//		//单条招聘的报名人员详情
//		Route::get('/cplts/{id}','Admin\Cplt\CpltController@cplts');
//		//报名已通过
//		Route::get('/cpltok','Admin\Cplt\CpltController@cpltok');
//		//报名未通过
//		Route::get('/cpltno','Admin\Cplt\CpltController@cpltno');
//		//待处理
//		Route::get('/cpltready','Admin\Cplt\CpltController@cpltready');
//
//		//杂项管理
//
//		//结算周期管理
//		Route::get('/times','Admin\Za\ZaController@times');
//		//编辑结算周期
//		Route::get('/timesedit','Admin\Za\ZaController@edit');
//		//更改结算周期状态
//		Route::get('/timesstatus','Admin\Za\ZaController@status');
//		//删除结算周期标签
//		Route::get('/timesdel','Admin\Za\ZaController@delete');
//		//增加结算周期
//		Route::get('/timesadd','Admin\Za\ZaController@create');
//		//处理添加
//		Route::get('/dotimes','Admin\Za\ZaController@docreate');
//
//
//		//热门标签管理
//		Route::get('/hots','Admin\Za\ZaController@hots');
//		//编辑热门标签
//		Route::get('/hotsedit','Admin\Za\ZaController@hotsedit');
//		//更改结算周期状态
//		Route::get('/hotsstatus','Admin\Za\ZaController@hotsstatus');
//		//删除结算周期标签
//		Route::get('/hotsdel','Admin\Za\ZaController@hotsdelete');
//		//增加结算周期
//		Route::get('/hotsadd','Admin\Za\ZaController@hotscreate');
//		//处理添加
//		Route::get('/dohots','Admin\Za\ZaController@dohotscreate');
//
//
//		//兼职类型管理
//		Route::get('/types','Admin\Za\ZaController@types');
//		//编辑结算周期
//		Route::get('/typesedit','Admin\Za\ZaController@typesedit');
//		//更改结算周期状态
//		Route::get('/typesstatus','Admin\Za\ZaController@typesstatus');
//		//删除结算周期标签
//		Route::get('/typesdel','Admin\Za\ZaController@typesdelete');
//		//增加结算周期
//		Route::get('/typesadd','Admin\Za\ZaController@typescreate');
//		//处理添加
//		Route::get('/dotypes','Admin\Za\ZaController@dotypescreate');
//
//		//兼职分类标签管理
//		Route::get('/catelist','Admin\Cate\CateController@index');
//		//分类标签删除
//		Route::get('/catedel','Admin\Cate\CateController@del');
//		//分类标签状态更改
//		Route::get('/catesta','Admin\Cate\CateController@sta');
//		//分类标签编辑
//		Route::get('/cateedit','Admin\Cate\CateController@edit');
//		//添加分类标签
//		Route::get('/cateadd','Admin\Cate\CateController@add');
//		//处理添加分类标签
//		Route::get('/cateup','Admin\Cate\CateController@update');
//		//分类详细信息
//		Route::get('/cate','Admin\Cate\CateController@cate');
//
//		//投诉建议
//		Route::get('/tousu','Admin\Tousu\TousuController@index');
//		//更改投诉状态
//		Route::get('/tousuok','Admin\Tousu\TousuController@tousuok');
//		//意见反馈
//		Route::get('/yijian','Admin\Tousu\TousuController@yijian');
//		//意见反馈以及投诉删除
//		Route::get('/del','Admin\Tousu\TousuController@del');
//
//		//工作群
//		Route::get('/tokework','Admin\TokeWork\TokeWorkController@index');
//		//绑定管理员的id和群
//		Route::get('/bindid','Admin\TokeWork\TokeWorkController@bindId');
//		//进入群聊天
//		Route::post('/workdetail','Admin\TokeWork\TokeWorkController@workdetail');
//		//进入群聊天ajax请求
//		Route::get('/godetail','Admin\TokeWork\TokeWorkController@godetail');
//		//解散群
//		Route::get('/delwork','Admin\TokeWork\TokeWorkController@delwork');
//		//私聊
//		Route::get('/salary','Admin\TokeWork\TokeWorkController@salary');
//		//开启关闭签到签退
//        Route::get('/isSign','Admin\TokeWork\TokeWorkController@isSign');
//
//		//群公告管理
//		Route::get('/gg/{id}','Admin\TokeWork\TokeWorkController@gg');
//		//短信发送公告通知群成员
//		Route::get('/send_sms','Admin\TokeWork\TokeWorkController@send_sms');
//		//添加群公告页面
//		Route::get('/addgg/{id}','Admin\TokeWork\TokeWorkController@addgg');
//		//处理添加群公告
//		Route::get('/doaddgg','Admin\TokeWork\TokeWorkController@doaddgg');
//		//修改公告
//		Route::get('/editgg','Admin\TokeWork\TokeWorkController@editgg');
//
//		//管理员列表
//		Route::resource('/adminuser','Admin\AdminUser\AdminUserController');
//		//管理员状态更改
//		Route::get('/adminusersta','Admin\AdminUser\AdminUserController@sta');
//		//管理员删除
//		Route::get('/index/del','Admin\AdminUser\AdminUserController@del');
//		//管理员修改
//		Route::get('adminuseredit','Admin\AdminUser\AdminUserController@adminuseredit');
//		//管理员添加
//		Route::get('/adduser','Admin\AdminUser\AdminUserController@adduser');
//
//		//角色管理
//		Route::resource('/userrole','Admin\Role\RoleController');
//		//角色状态更改
//		Route::get('/rolestatus','Admin\Role\RoleController@sta');
//		//角色删除
//		Route::get('/roledel','Admin\Role\RoleController@del');
//		//角色编辑
//		Route::get('/roleedit','Admin\Role\RoleController@roleedit');
//
//		//节点管理
//		Route::resource('/node','Admin\Node\NodeController');
//		//节点删除
//		Route::get('/nodedel','Admin\Node\NodeController@nodedel');
//		//节点编辑
//		Route::get('/nodeedit','Admin\Node\NodeController@nodeedit');
//
//		//工资管理
//		Route::get('/price','Admin\Price\PriceController@index');
//		//每个兼职的详细工资
//		Route::post('/workprice','Admin\Price\PriceController@workPrice');
//		//单个用户的工资状态改为发工资
//		Route::get('/putprice','Admin\Price\PriceController@putPrice');
//		//查看单个用户的所有签到和工资详情
//		Route::post('/userprice','Admin\Price\PriceController@userPrice');
//		//ajax给单个用户单天发工资
//		Route::get('/dayprice','Admin\Price\PriceController@dayPrice');
//
//		//用户结算账户管理
//		Route::get('/account','Admin\Price\PriceController@account');
//
//        //导出excel表
//        //到处当前用户签到表
//        Route::get('/signExport/{id}','Admin\Excel\ExcelController@signExport');
//
//        //审核企业端用户认证
//        Route::get('/audit/{status}','Admin\Audit\AuditController@authentication');
//        //查看企业端用户详情
//        Route::get('/auditShow/{id}','Admin\Audit\AuditController@show');
//        //搜索用户
//        Route::get('/audit/index/{status}','Admin\Audit\AuditController@index');
//        //审核
//        Route::get('/auditstatus','Admin\Audit\AuditController@status');
//        //未过审
//        Route::get('/auditno','Admin\Audit\AuditController@no');
//	});
//
//
//	/*
//
//				接口
//
//	*/
//
//	//搜索
//	Route::get('/wechat/search','Inter\Index\IndexController@search');
//
//	//获取短信验证码接口
//	Route::get('/wechat/getCode','Inter\Logins\LoginsController@getCode');
//	//登录接口
//	Route::get('/wechat/login','Inter\Logins\LoginsController@logins');
//
//	//首页数据加载
//	Route::get('/wechat/index','Inter\Index\IndexController@index');
//	//首页授权登录接口
//	Route::get('/wechat/sqlogin','Inter\Logins\LoginsController@login');
//	//获取加密信息
//    Route::get('/wechat/phone','Inter\Logins\LoginsController@getPhone');
//	//获取到用户信息并登陆
//	Route::get('/wechat/wechatlogin','Inter\Logins\LoginsController@wechatlogin');
//	//获取用户头像和用户名
//	Route::get('/wechat/getuserinfo','Inter\Logins\LoginsController@getuserinfo');
//	//获取搜索框前面的地址
//	Route::get('/wechat/getMap','Inter\Index\IndexController@getMap');
//	//选取地址获取四川省所有地址
//	Route::get('/wechat/getAdd','Inter\Index\IndexController@getAdd');
//	//单挑兼职数据详情
//	Route::get('/wechat/detail','Inter\Index\IndexController@detail');
//	//获取工作轮播
//    Route::get('/wechat/jobimage','Inter\Index\IndexController@jobImage');
//	//获取首页筛选信息
//	Route::get('/wechat/getselectinfo','Inter\Index\IndexController@getSelectInfo');
//	//首页的筛选项目的搜索
//	Route::get('/wechat/getselect','Inter\Index\IndexController@getSelect');
//	//首页的四个banner获取分类数据
//	Route::get('/wechat/getcates','Inter\Index\IndexController@getCates');
//	//获取三级分类
//    Route::get('/wechat/getworkcate','Inter\Index\IndexController@getWorkCate');
//    //获取三级分类中的工作
//    Route::get('/wechat/getwork','Inter\Index\IndexController@getWork');
//	//首页的五个分类
//    Route::get('wechat/getposition','Inter\Index\IndexController@getPosition');
//    //首页推荐
//    Route::get('wechat/getrecommend','Inter\Index\IndexController@getRecommend');
//    //tab
//    Route::get('wechat/gettab','Inter\Index\IndexController@getTab');
//
//
//	//收藏  、  取消收藏接口
//	Route::get('/wechat/love','Inter\Detail\DetailController@love');
//
//	Route::get('/wechat/qxlove','Inter\About\AboutController@qxLove');
//	//报名列表接口
//	Route::get('/wechat/getcplt','Inter\About\AboutController@getCplt');
//	Route::get('/wechat/qxcplt','Inter\About\AboutController@qxCplt');
//	//详情页报名按钮
//	Route::get('/wechat/gocplt','Inter\About\AboutController@goCplt');
//	//兼职轨迹详情页
//	Route::get('/wechat/getguiji','Inter\About\AboutController@getGuiji');
//	//详情页收藏按钮
//	Route::get('/wechat/golove','Inter\Index\IndexController@goLove');
//	//个人中心我的收藏
//	Route::get('/wechat/getlove','Inter\About\AboutController@getLove');
//	//投诉列表数据
//	Route::get('/wechat/tousulist','Inter\About\AboutController@getTousuList');
//	//投诉内容提交
//	Route::get('/wechat/uptousu','Inter\About\AboutController@upTousu');
//	//意见反馈内容提交
//	Route::get('/wechat/yijian','Inter\About\AboutController@yiJian');
//
//
//	//获取用户的简历信息
//	Route::get('/wechat/getuserinfo','Inter\About\AboutController@getUserInfo');
//	//获取简历里面的所有标签模板
//	ROute::get('/wechat/getbiaoqian','Inter\About\AboutController@getBiaoQian');
//	//编辑简历信息上传
//	Route::get('/wechat/jianliup','Inter\About\AboutController@jianliup');
//	//获取电话号码
//    Route::get('/wechat/getphone','Inter\About\AboutController@getPhone');
//
//	//聊天接口
//	//获取当前用户的id
//	Route::get('/wechat/toke','Inter\Toke\TokeController@index');
//	//绑定当前用户的id和client_id
//	Route::get('/wechat/idbind','Inter\Toke\TokeController@idbind');
//	//获取最群最后一条消息
//	Route::get('/wechat/getlastmsg','Inter\Toke\TokeController@getLastMsg');
//	//把消息存入数据库
//	Route::get('/wechat/putmsg','Inter\Toke\TokeController@putMsg');
//	//进入群聊详情拿到当前群聊天记录和群资料
//	Route::get('/wechat/getoldmsg','Inter\Toke\TokeController@getOldMsg');
//	//发送群聊消息
//	Route::get('/wechat/sendmsg','Inter\Toke\TokeController@sendMsg');
//	//获取群详细信息
//	Route::get('/wechat/getgroupinfo','Inter\Toke\TokeController@getGroupInfo');
//	//获取当前用户的消息列表
//	Route::get('/wechat/getmsglist','Inter\Toke\TokeController@getMsgList');
//	//sleep同步
//	Route::get('/wechat/sleep','Inter\Toke\TokeController@sleep');
//	//获取群公告
//	Route::get('wechat/getgg','Inter\Toke\TokeController@getGG');
//	//获取最新公告
//    Route::get('wechat/newgg','Inter\Toke\TokeController@newGG');
//
//	//判断是否已经签到
//	Route::get('/wechat/issign','Inter\Sign\SignController@isSign');
//	//进行签到
//	Route::get('/wechat/gosign','Inter\Sign\SignController@goSign');
//	//进行签退
//	Route::get('/wechat/signout','Inter\Sign\SignController@signOut');
//	//判断是否已经签到和签退
//	Route::get('/wechat/issignout','Inter\Sign\SignController@isSignOut');
//	//我的工资
//	Route::get('/wechat/getprice','Inter\Sign\SignController@getPrice');
//	//获取当前用户的结算账户
//	Route::get('/wechat/getaccount','Inter\Sign\SignController@getAccount');
//	//更新当前用户的收款账户
//	Route::get('/wechat/upaccount','Inter\Sign\SignController@upAccount');
//	//获取单个兼职的详细工资信息
//	Route::get('/wechat/getdetailprice','Inter\Sign\SignController@getDetailPrice');
//	//获取每天的兼职详情工资
//	Route::get('/wechat/getdayprice','Inter\Sign\SignController@getDayPrice');
//
//	//加载轮播图
//	Route::get('/wechat/getlunbo','Inter\Index\IndexController@getLunBo');
//	//加载关于页面的问号信息
//	Route::get('/wechat/getinfomsg','Inter\Index\IndexController@getinfomsg');
//



