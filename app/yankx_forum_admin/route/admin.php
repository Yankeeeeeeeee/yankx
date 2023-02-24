<?php

//路由定义文件

use think\facade\Route;

// 全局变量规则
Route::pattern([
    'sid' => '\d+',
    'mid' => '\d+',
    'rid' => '\d+',
]);

// Admin控制器下的方法
// 管理员登录
Route::rule('/aLogin44','Admin/aLogin');
Route::rule('/aDologin44','Admin/aDologin','post');
// 管理员修改密码
Route::rule('/aPa44','Admin/aPa');
Route::rule('/aDoPa44','Admin/aDoPa','post');
// 帖子管理
Route::rule('/list/[:sid]$','Deal/mesDeal');
Route::rule('/postdetail/[:mid]$','Deal/aDetail');
Route::rule('/mdel/[:mid]$','Deal/mesDel');
Route::rule('/rdel/[:rid]$','Deal/resDel');
// 用户管理
Route::rule('/user','Deal/userDeal');
Route::rule('/userpower/[:unick]$','Deal/userPower');
Route::rule('/usermodi/[:unick]$','Deal/userPowerModi','post');
// 板块管理
Route::rule('/write/[:sid]$','Deal/secDeal');
Route::rule('/writing/[:sid]$','Deal/secInfo');
Route::rule('/modify/[:sid]$','Deal/secInfoModi','post');
Route::rule('/Add$','Deal/secAdd');
Route::rule('/doAdd$','Deal/secDoAdd','post');
Route::rule('/del/[:sid]$','Deal/secDel');


// MISS路由
Route::miss('Error/miss');
// Route::miss('public/miss');
