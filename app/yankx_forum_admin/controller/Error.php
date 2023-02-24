<?php
declare (strict_types = 1);

namespace app\yankx_forum_admin\controller\controller;

use app\BaseController;


class Error extends BaseController
{
    /**
     * miss路由指向的方法
     */
    public function miss()
    {
        //提示，跳转
        $this->error('你访问的地址不存在！');
    }

    
}
