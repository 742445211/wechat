<?php

use App\Facades\ReturnJson;
use App\Model\Workers;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/**
 * 企业端接口
 */


////登陆接口
//Route::get('/recruit/login','Recruit\Login\LoginController@login');
////获取头像
//Route::get('/recruit/wechatlogin','Recruit\Login\LoginController@wechatlogin');
//
//
////分组
//Route::group(['prefix'=>'recruit','middleware'=>'wechat'], function (){
//    //发布职位
//    Route::post('release','Recruit\Release\ReleaseController@release');
//    //查看已发布职位
//    Route::get('showrelease','Recruit\Release\ReleaseController@index');
//
//    //提交认证信息
//    Route::post('setIdentity','Recruit\Audit\AuditController@setIdentity');
//
//
//    //群聊
//    //获取当前用户的ID
//    Route::get('getid','Recruit\Group\GroupController@index');
//    //获取当前用户所有群的最后一条消息
//    Route::get('getlastmsg','Recruit\Group\GroupController@getLastMsg');
//    //把用户ID和client_id绑定在一起
//    Route::get('bindid','Recruit\Group\GroupController@idBind');
//    //获取群消息记录和群资料
//    Route::get('getoldmsg','Recruit\Group\GroupController@getOldMsg');
//    //发送群聊消息
//    Route::get('sendmsg','Recruit\Group\GroupController@sendMsg');
//    //获取群详情数据
//    Route::get('getgroupinfo','Recruit\Group\GroupController@getGroupInfo');
//    //获取群公告
//    Route::get('getgg','Recruit\Group\GroupController@getGG');
//    //获取最新公告
//    Route::get('newgg','Recruit\Group\GroupController@newGG');
//    //开启关闭签到签退功能
//    Route::get('issign','Recruit\Group\GroupController@isSign');
//    //解散群
//    Route::get('delgropu','Recruit\Group\GroupController@delwork');
//    //添加公告
//    Route::post('addgg','Recruit\Group\GroupController@addGG');
//    //修改公告
//    Route::post('editgg','Recruit\Group\GroupController@editGG');
//
//
//    //报名详情
//    //查看所有报名
//    Route::get('detail','Recruit\Detail\DetailController@index');
//
//
//    //薪资相关
//    //当前用户上架中工作的薪资相关资料
//    Route::get('price','Recruit\Price\PriceController@index');
//    //查询签到
//    Route::get('sign','Recruit\Price\PriceController@Sign');
//
//
//    //测试
//    Route::post('base','Recruit\Release\ReleaseController@test');
//});



/**
 * 管理工具接口
 */
    //招聘端
    Route::group(['prefix'=>'recruit'],function (){
        //判断用户是否注册过
        Route::get('isFirst','Recruit\Manage\ManageController@isFirst');
        //注册接口
        Route::post('register','Recruit\Manage\ManageController@register');
        //接受b端用户注册时上传的图片
        Route::post('uploadImage','Recruit\Manage\ManageController@uploadImage');
        //上传b端formId
        Route::get('setFormID','ZhaoXian\FormId\FormIdController@setFormID');
        //获取B端用户个人信息
        Route::get('getMyDetails','Recruit\Manage\ManageController@getMyDetails');
        //个人认证成为公司
        Route::post('editUserInfo','Recruit\Manage\ManageController@editUserInfo');
        //b端用户修改手机号
        Route::post('editPhone','Recruit\Manage\ManageController@editPhone');


        //招聘端获取用户发布的工作信息
        Route::get('getMyWork','Recruit\Manage\ManageController@getMyWork');
        //招聘端添加工作
        Route::post('addJob','Recruit\Manage\ManageController@addJob');
        //更换工作头像
        Route::post('changeHeader','ZhaoXian\Works\WorkController@changeHeader');
        //上传用人公司logo
        Route::post('uploadLogo','ZhaoXian\Works\WorkController@uploadLogo');
        //下架工作
        Route::put('delWork','ZhaoXian\Works\WorkController@delete');
        //修改工作内容
        Route::post('editWork','ZhaoXian\Works\WorkController@edit');
        //下架工作，标示工作开始并停止找人
        Route::post('atWork','Recruit\Manage\ManageController@atWork');
        //获取工作详情
        Route::get('workDetail','ZhaoXian\Works\WorkController@workDetail');
        //修改工作详情
        Route::post('editDescribe','ZhaoXian\Works\WorkController@editDescribe');
        //获取某B端用户的在招工作
        Route::get('getRecruitWork','ZhaoXian\Works\WorkController@getRecruitWork');
        //获取全部有意向（已报名及待面试）的员工
        Route::get('allIntentionWorkers','Recruit\Manage\ManageController@allIntentionWorkers');


        //招聘端查看岗位详情,获取在职员工
        Route::get('workDetails','Recruit\Manage\ManageController@workDetails');
        //获取待面试员工
        Route::get('toBeAudited','Recruit\Manage\ManageController@toBeAudited');
        //获取待审核员工
        Route::get('enrolment','Recruit\Manage\ManageController@enrolment');
        //获取已离职员工
        Route::get('workQuit','Recruit\Manage\ManageController@workQuit');
        //查询收藏本工作的员工
        Route::get('getCollectionWorker','ZhaoXian\Collection\CollectionController@getCollectionWorker');
        //查看员工用户信息
        Route::get('workerDetails','Recruit\Manage\ManageController@workerDetails');
        //获取员工教育经历（填写的简历）
        Route::get('getEducational','ZhaoXian\WorkEducational\WorkEducationalController@get');
        //获取员工工作经历（填写的简历）
        Route::get('getExperience','ZhaoXian\WorkExperience\WorkExperienceController@getWorkExperience');
        //移除员工
        Route::put('removeWorker','Recruit\Manage\ManageController@removeWorker');
        //获取工作群二维码(从用户端获取)
        Route::get('getCode','Inter\Manage\ManageController@getCode');
        //获取员工签到记录
        Route::get('getWorkSign','ZhaoXian\Sign\SignController@getWorkSign');
        //获取签到详情,根据月份
        Route::get('getWorkSignByMonth','ZhaoXian\Sign\SignController@getWorkSignByMonth');
        //获取签到详情，根据员工
        Route::get('getWorkSignByWorker','ZhaoXian\Sign\SignController@getWorkSignByWorker');
        //通知面试
        Route::post('notifyInterview','Recruit\Manage\ManageController@notifyInterview');
        //获取面试信息
        Route::get('getInterviewDetail','ZhaoXian\Interview\InterviewController@getInterviewDetail');
        //面试不通过
        Route::post('refuse','Recruit\Manage\ManageController@refuse');



        //前台上传图片
        Route::post('uploadWorkImage','ZhaoXian\WorkImage\WorkImageController@upload');
        //添加工作图片
        Route::post('addWorkImage','ZhaoXian\WorkImage\WorkImageController@addImage');
        //删除工作图片
        Route::delete('delWorkImage','ZhaoXian\WorkImage\WorkImageController@delete');
        //修改工作图片（一次只能改一张）
        Route::post('editWorkImage','ZhaoXian\WorkImage\WorkImageController@edit');
        //获取工作图片
        Route::get('getWorkImage','ZhaoXian\WorkImage\WorkImageController@get');

        //发布公告
        Route::post('release','ZhaoXian\Notice\NoticeController@release');
        //删除公告
        Route::put('delNotice','ZhaoXian\Notice\NoticeController@delete');
        //修改公告
        Route::post('editNotice','ZhaoXian\Notice\NoticeController@edit');
        //查看公告
        Route::get('getNotice','ZhaoXian\Notice\NoticeController@get');
        //公告标示已读
        Route::get('recordsRead','ZhaoXian\Notice\NoticeController@recordsRead');
        //获取某公告的已读名单
        Route::get('getReadRecord','ZhaoXian\Notice\NoticeController@getReadRecord');

        //添加店铺
        Route::post('addStore','ZhaoXian\Store\StoreController@addStore');
        //修改店铺信息
        Route::put('editStore','ZhaoXian\Store\StoreController@editStore');
        //删除店铺信息
        Route::delete('deleteStore','ZhaoXian\Store\StoreController@deleteStore');
        //获取店铺详情
        Route::get('getStore','ZhaoXian\Store\StoreController@getStore');
        //添加店铺图片
        Route::post('addStoreImage','ZhaoXian\Store\StoreController@addImage');
        //删除图片
        Route::delete('delStoreImage','ZhaoXian\Store\StoreController@delImage');
        //修改图片
        Route::post('editStoreImage','ZhaoXian\Store\StoreController@editImage');


        //添加动态
        Route::post('addDynamic','ZhaoXian\Dynamic\DynamicController@create');
        //删除动态
        Route::delete('deleteDynamic','ZhaoXian\Dynamic\DynamicController@delete');
        //修改动态
        Route::put('editDynamic','ZhaoXian\Dynamic\DynamicController@edit');
        //获取动态
        Route::get('getDynamic','ZhaoXian\Dynamic\DynamicController@getDynamic');
        //添加动态图
        Route::post('addDynamicImage','ZhaoXian\Dynamic\DynamicController@addImage');
        //删除动态图
        Route::delete('deleteDynamicImage','ZhaoXian\Dynamic\DynamicController@delImage');
        //修改动态图
        Route::post('editDynamicImage','ZhaoXian\Dynamic\DynamicController@editImage');
        //上传动态图片
        Route::post('uploadDynamicImage','ZhaoXian\Dynamic\DynamicController@uploadDynamicImage');
    });

/**
 * 员工端接口
 */
    Route::group(['prefix'=>'inter'],function (){
        //判断用户是否注册过
        Route::get('isFirst','Inter\Manage\ManageController@isFirst');
        //用户注册
        Route::post('register','Inter\Manage\ManageController@register');
        //获取个人详细信息
        Route::get('getMyDetail','Inter\Manage\ManageController@getMyDetail');
        //修改信息
        Route::post('editDetail','Inter\Manage\ManageController@edit');
        //员工添加工作经历
        Route::post('addExperience','ZhaoXian\WorkExperience\WorkExperienceController@create');
        //员工修改工作经历
        Route::put('editExperience','ZhaoXian\WorkExperience\WorkExperienceController@edit');
        //删除工作经历
        Route::delete('deleteExperience','ZhaoXian\WorkExperience\WorkExperienceController@delete');
        //员工添加教育经历
        Route::post('addEducational','ZhaoXian\WorkEducational\WorkEducationalController@create');
        //员工修改教育经历
        Route::put('editEducational','ZhaoXian\WorkEducational\WorkEducationalController@edit');
        //员工删除教育经历
        Route::delete('deleteEducational','ZhaoXian\WorkEducational\WorkEducationalController@delete');
        //获取员工教育经历（填写的简历）
        Route::get('getEducational','ZhaoXian\WorkEducational\WorkEducationalController@get');
        //获取员工工作经历（填写的简历）
        Route::get('getExperience','ZhaoXian\WorkExperience\WorkExperienceController@getWorkExperience');
        //获取员工银行卡号及开户行
        Route::get('getBank','Inter\Manage\ManageController@getBank');
        //员工添加银行卡号及开户行
        Route::post('addBank','Inter\Manage\ManageController@addBank');


        //员工收藏工作
        Route::post('collection','ZhaoXian\Collection\CollectionController@collection');
        //员工取消收藏
        Route::delete('cancel','ZhaoXian\Collection\CollectionController@cancel');
        //员工获取自己收藏的工作
        Route::get('getCollectionWork','ZhaoXian\Collection\CollectionController@getCollectionWork');
        //员工报名
        Route::post('joinWork','Inter\Manage\ManageController@joinWork');
        //判断员工报名状态
        Route::get('isJoin','Inter\Manage\ManageController@isJoin');


        //获取工作记录
        Route::get('workRecord','Inter\Manage\ManageController@workRecord');
        //获取工作信息
        Route::get('workDetail','Inter\Manage\ManageController@workDetail');
        //绑定工作
        Route::put('bindJob','Inter\Manage\ManageController@bindJob');
        //员工记录打卡接口
        Route::post('sign','ZhaoXian\Sign\SignController@sign');
        //获取打卡记录
        Route::get('getWorkerSign','ZhaoXian\Sign\SignController@getWorkerSign');
        //获取员工某月在某工作上的天数
        Route::get('getWorkerSignByMonth','ZhaoXian\Sign\SignController@getWorkerSignByMonth');
        //获取待面试工作
        Route::get('getMyToBeAuditedWork','Inter\Manage\ManageController@getMyToBeAuditedWork');
        //获取面试信息
        Route::get('getInterviewDetail','ZhaoXian\Interview\InterviewController@getInterviewDetail');


        //用户反馈接口
        Route::post('feedback','Inter\Manage\ManageController@feedback');

        //发布评论
        Route::post('comment','ZhaoXian\Comment\CommentController@comment');
        //查看员工发布的评论
        Route::get('getWorkerComment','ZhaoXian\Comment\CommentController@getWorkerComment');
        //查看某工作的评论
        Route::get('getWorkComment','ZhaoXian\Comment\CommentController@getWorkComment');
        //删除评论
        Route::put('delComment','ZhaoXian\Comment\CommentController@delete');


//        //群测试
//        Route::get('bindMyGroup','ZhaoXian\Msg\MsgController@bindMyGroup');
//        //获取历史消息
//        Route::get('getGroupMsg','ZhaoXian\Msg\MsgController@getGroupMsg');
//        //
//        Route::get('getUnreadMsg','ZhaoXian\Msg\MsgController@getUnreadMsg');
//        //
//        Route::post('sendMsg','ZhaoXian\Msg\MsgController@sendMsg');



        //获取某市的所有区的工作数
        Route::get('getCityNumber','ZhaoXian\WorkGeo\WorkGeoController@getCityNumber');
        //获取地图渲染区域的街道工作数
        Route::get('getStreetNumber','ZhaoXian\WorkGeo\WorkGeoController@getStreetNumber');
        //获取该街道的工作列表
        Route::get('getStreetWork','ZhaoXian\WorkGeo\WorkGeoController@getStreetWork');

        //获取发布中工作列表，带模糊搜索
        Route::get('workList','ZhaoXian\Works\WorkController@get');

        //获取招聘者发布过的工作
        Route::get('getWorkByRecruiter','Inter\Manage\ManageController@getWorkByRecruiter');


        /**
         * C端部分聊天接口
         */
        //获取消息记录，聊天记录下拉
        Route::get('getGroupMsg','ZhaoXian\Msg\MsgController@getGroupMsg');
    });


/**
 * 平台端接口
 */
    Route::group(['prefix' => 'platform'],function (){
        //新增工作分类
        Route::post('addCate','ZhaoXian\Cate\CateController@create');
        //删除工作分类
        Route::delete('deleteCate','ZhaoXian\Cate\CateController@delete');
        //修改工作分类
        Route::put('editCate','ZhaoXian\Cate\CateController@edit');

        //添加轮播图
        Route::post('addBanner','ZhaoXian\Banner\BannerController@create');
        //下架轮播图
        Route::put('delBanner','ZhaoXian\Banner\BannerController@delete');
    });



    /**
     * 三端公用
     */
    //获取工人当前工作的签到信息
    Route::get('getWorkerSign','ZhaoXian\Sign\SignController@getWorkerSign');
    //获取某工作的全部签到信息
    Route::get('getWorkSign','ZhaoXian\Sign\SignController@getWorkSign');
//    //获取员工工作经历（填写的简历）
//    Route::get('getExperience','ZhaoXian\WorkExperience\WorkExperienceController@getWorkExperience');
//    //获取员工教育经历（填写的简历）
//    Route::get('getEducational','ZhaoXian\WorkEducational\WorkEducationalController@get');
    //获取工作分类
    Route::get('getCate','ZhaoXian\Cate\CateController@get');


    //获取使用中的轮播图
    Route::get('getBanner','ZhaoXian\Banner\BannerController@get');




    //上传图片
    Route::post('upload','Admin\Images\ImageController@upload');


    /**
     * 群聊
     */

    //为用户绑定群id
    Route::get('bindMyGroup','ZhaoXian\Msg\MsgController@bindMyGroup');
    //获取消息记录，聊天记录下拉
    Route::get('getGroupMsg','ZhaoXian\Msg\MsgController@getGroupMsg');
    //记录用户在某群浏览的最后一条信息
    Route::get('recordUnreadId','ZhaoXian\Msg\MsgController@recordUnreadId');
    //获取未读群消息
    Route::get('getUnreadMsg','ZhaoXian\Msg\MsgController@getUnreadMsg');
    //获取用户未读消息数
    Route::get('getUnreadNumber','ZhaoXian\Msg\MsgController@getUnreadNumber');
    //获取群最后一条消息
    Route::get('getLastMsg','ZhaoXian\Msg\MsgController@getLastMsg');
    //发送消息
    Route::get('sendMsg','ZhaoXian\Msg\MsgController@sendMsg');
    //发送图片
    Route::get('sendImage','ZhaoXian\Msg\MsgController@sendImage');
    //解散群
    Route::get('unGroup','ZhaoXian\Msg\MsgController@unGroup');
    //踢人
    Route::get('leaveGroup','ZhaoXian\Msg\MsgController@leaveGroup');



    //为某员工分组
    Route::post('setGrouping','ZhaoXian\Msg\MsgController@setGrouping');
    //获取员工分组
    Route::get('getGrouping','ZhaoXian\Msg\MsgController@getGrouping');
    //删除某员工的分组记录
    Route::delete('delGrouping','ZhaoXian\Msg\MsgController@delGrouping');
    //修改某员工的分组
    Route::put('editGrouping','ZhaoXian\Msg\MsgController@editGrouping');
    //添加群分组
    Route::post('addGroupingName','ZhaoXian\Msg\MsgController@addGroupingName');
    //获取群的分组名
    Route::get('getGroupingName','ZhaoXian\Msg\MsgController@getGroupingName');
    //修改群的分组名
    Route::put('editGroupingName','ZhaoXian\Msg\MsgController@editGroupingName');
    //删除群分组
    Route::delete('delGroupingName','ZhaoXian\Msg\MsgController@delGroupingName');


/**
 * 私聊
 */

    //私聊发送消息
    Route::get('sendToPrivate','ZhaoXian\Msg\MsgController@sendToPrivate');
    //记录当前用户浏览的最后一条消息id
    Route::get('setLastMsgId','ZhaoXian\Msg\MsgController@setLastMsgId');
    //获取用户未读消息
    Route::get('getPrivateMsg','ZhaoXian\Msg\MsgController@getPrivateMsg');
    //获取未读消息数
    Route::get('getPrivateMsgNumber','ZhaoXian\Msg\MsgController@getPrivateMsgNumber');
    //获取私聊中的最后一条信息
    Route::get('getLastPrivateMsg','ZhaoXian\Msg\MsgController@getLastPrivateMsg');
    //标记消息为已读
    Route::get('tabRead','ZhaoXian\Msg\MsgController@tabRead');
    //获取更多消息
    Route::get('getMorePrivateMsg','ZhaoXian\Msg\MsgController@getMorePrivateMsg');



    Route::get('addgeo',function (){
        $location = \App\Facades\SendSms::curl_get('https://apis.map.qq.com/ws/district/v1/getchildren?id=510100&key=6UCBZ-BVSKU-NWOVZ-2C7QF-3RG35-UJFS4');
        $redis = \Illuminate\Support\Facades\Redis::connection('geo');
        foreach ($location->result[0] as $value){
             $a = app('pinyin') -> sentence(substr($value -> fullname,0,strlen($value -> fullname)-3));
             $a = str_replace(' ','',$a);
             $redis -> geoadd('chengdu',$value -> location -> lng,$value -> location -> lat,$a);
             $redis -> set($a,0);
        }
    });

    Route::get('png',function (){
        $img = new ImageManager();
        return $img -> make(base_path('public') . '/upload_auth/2019-08-12/4L8NDAX79BH34KR7lc6.jpg')->response();
    });

    Route::get('test',function (){
        $res = \App\Facades\SendSms::wxOcrIdCard('https://www.xiaoshetong.cn/upload_auth/2019-09-11/smN4LWoOLqvztly7cXG.jpg',510525199308210035,'王启枫');
        if($res) return ReturnJson::json('err',9,'身份证照与输入信息不匹配');
    });


    Route::get('getformid','ZhaoXian\FormId\FormIdController@getFormID');
