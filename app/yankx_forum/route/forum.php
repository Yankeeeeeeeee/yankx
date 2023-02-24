<?php

//路由定义文件

use think\facade\Route;

// 全局变量规则
Route::pattern([
    'sid' => '\d+',
    'mid' => '\d+',
]);


// 1.路由定义
// Route::rule('路由表达式','路由地址','请求类型');
// 2.路由检测
// 理想URL地址：http://www.yankx.com/yankx_forum/yankx
// 查询路由表 /yankx->Index/index
Route::rule('/yankx','Index/index');
// 3.路由解析
// 4.路由调度
// 找到index控制器的index方法

// 论坛版块路由定义
Route::rule('/view44/[:sid]$','Index/view','get');
// Route::get('/list/[:sid]','Index/view');

// dores路由定义
Route::rule('/dores/:mid$','Index/dores','post');
// Route::get('/write/:sid','Index/view');

// detail路由定义
Route::rule('/detail44/:mid$','Index/detail','get');
// Route::get('/detail/[:sid]','Index/view');

// post路由定义
Route::rule('/write/:sid$','Index/post','get');
// Route::get('/write/:sid','Index/view');

// dopost路由定义
// 请求类型：get
// 超级链接带参数
// 请求类型：post
// form method=post
// 表单action携带参数
Route::rule('/writing/:sid$','Index/dopost','post');
// 联系我们
Route::rule('/contact','Index/contact','get');

//找到user控制器的index方法
// 上传头像表单
Route::rule('/me44','user/me','get');
// 上传头像
Route::rule('/upMe','user/upMe','post');
// 登录表单
Route::rule('/login44','user/login','get');
// 登录验证
Route::rule('/doLogin','user/doLogin','post');
// 注册表单
Route::rule('/reg44','user/reg','get');
// 注册
Route::rule('/doReg','user/doReg','post');




// MISS路由
// Route::miss('Error/miss');
// Route::miss('public/miss');