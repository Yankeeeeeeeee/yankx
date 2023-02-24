<?php
declare (strict_types = 1);

namespace app\yankx_forum_admin\controller;

use app\BaseController;
use think\facade\Db;
use think\facade\Session;
use think\Request;

class Admin extends BaseController
{
    use \liliuwei\think\Jump;
    /**登录页面*/
    public function aLogin()
    {
        return view();
    }
    // 登录处理
    public function aDoLogin()
    {
        //获取Input的用户名密码
    $aName=trim($this->request->param('anick'));
    $aPa=md5(trim($this->request->param('apa')));

    $re= Db::name('admin')
        ->where('anick',$aName)
        ->where('apa',$aPa)
        ->find();
    if($re == null){
        //登录验证失败
        $this ->error('登录失败','Admin/aLogin');
    }else{
        //登录验证成功// echo '成功';
        //发张凭证
        //session类
        Session::set('aNick',$aName);
        // //session助手函数
        // session('uNick',$uName);
        $this -> success('登录成功','Deal/mesdeal');
    }
        return view();
    }

    // 修改密码页面
    public function aPa()
    {
        // 判断是否已经登录
        $this->checkb();
        return view();
    }

    //修改管理员密码 
    public function aDoPa()
    {
        // 判断是否已经登录
        $this->checkb();
        // 获取输入的用户名、旧密码及新密码
        $aName=trim($this->request->param('anick'));
        $oPa=md5(trim($this->request->param('opa')));
        $nPa=md5(trim($this->request->param('npa')));
        $nPa1=md5(trim($this->request->param('npa1')));
        // 判断新旧密码是否一致
        if($oPa==$nPa){
            $this->error('新旧密码不能一样！','Admin/aPa');
            exit();
        }
        $re=Db::name('admin')
        ->where('anick',Session::get('aNick'))
        ->where('apa',$oPa)
        ->find();
        // dump($re);

        // 新旧密码不一致
        if(!$re){
            // 旧密码输入错误
            $this->error('旧密码输入有误！','Admin/aPa');
            exit();
        }else{
            // 旧密码输入正确
            // 判断两次输入的新密码是否一致
            if($nPa!=$nPa1){
            $this->error('两次输入的密码不一致','Admin/aPa');
            }else{
                // 两次输入的新密码一致
                $re=Db::name('admin')
                ->where('anick',Session::get('aNick'))
                ->update(['apa'=> $nPa]);
                if($re==1){
                    $this->success('密码修改成功，请重新登陆！','Admin/alogout');
                }else{
                    $this->success('密码修改失败，请稍后再试！','Admin/apa');
                }
        }
        }
        
        // return view();
    }

    // 用户注销
    public function aLogOut()
    {
        // 判断用户是否登录
        $this->checkb();
        // 清除用户的session信息
        // session类
        Session::delete('aNick');
        // Session::clear();
        // session助手函数
        // session('aNick',null);
        // session(null);
        $this->success('注销成功','Admin/alogin');
    }

}
