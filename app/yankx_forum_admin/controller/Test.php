<?php

namespace app\yankx_forum_admin\controller;

use think\facade\Config;
use think\facade\Db;

class Test
{
    public function showConfig()
    {
        //获取前台完整的配置文件内容
        $config=Config::get('database');
        echo('后台的数据库配置文件的内容为:');
        dump($config);

        //默认连接方案名字
        $defaultname = Config::get('database.default');
        echo('后台的默认连接方案为:');
        dump($defaultname);

        //默认用户名
        $username = Config::get('database.connections.yankx.username');
        echo '默认使用的数据库用户是 '; 
        dump($username);
    }

    
}