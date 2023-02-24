<?php
declare (strict_types = 1);

namespace app\yankx_forum\controller;
//2003010344
use app\Basecontroller;
use think\facade\Config;
use think\facade\Cookie;
use think\facade\Db;
use think\facade\Filesystem;
use think\facade\Route;
use think\facade\Session;
class User extends Basecontroller
{
    use \liliuwei\think\Jump;
    public function login()
    {
        return view();
    }
    public function reg()
    {
        return view();
    }
    /*2003010344 
    渲染注册页面
    */
    public function doReg()
    {
        //获取注册信息
        //htmlspecialchars过滤
        $uNick=$this->request->post('unick','','trim,htmlspecialchars');
        $uPa=$this->request->post('upa','','trim,md5');
        $uEmail=$this->request->post('uemail','','trim,htmlspecialchars');
        $uTel=$this->request->post('utel','','trim');
        
        //判断主键是否重复
        $user=Db::name('user')
            ->field('unick,uemail,utel')
            ->whereOr('unick',$uNick)
            ->whereOr('uemail',$uEmail)
            ->whereOr('utel',$uTel)
            ->find();

        if($user!==null){
            #注册过（在数据库能够查到匹配的信息）
            if($uNick==$user['unick']){
                #用户名重复
                $this->error('用户名已被注册','user/reg');
            }
            if($uEmail==$user['uemail']){
                #邮箱重复
                $this->error('邮箱已被注册','user/reg');
            }
            if($uTel==$user['utel']){
                #电话号码重复
                $this->error('电话号码已被注册','user/reg');
            }
        }

        //准备注册信息
        $data=[
            'unick'     =>$uNick,
            'upa'       =>$uPa,
            'uemail'       =>$uEmail,
            'utel'       =>$uTel,
        ];
        // 添加数据的链式操作
        $user=Db::name('user')->insert($data);
        // $user=1表示添加数据成功，反之则添加失败
        if($user==1)
        {
            #注册成功
            $this->success('注册成功','User/login');
            #注册失败
            $this->error('注册失败','User/reg');
        }
    }

    public function changePa()
    {
        return view();
    }
    /*2003010344
        修改用户密码
    */
    public function doChangePa()
    {
        // 判断是否已经登录
        $this->check();
        // 获取输入的用户名、旧密码及新密码
        // $aName=trim($this->request->param('anick'));
        $oPa=md5(trim($this->request->param('opa')));
        $nPa=md5(trim($this->request->param('npa')));
        $nPa1=md5(trim($this->request->param('npa1')));
        // 判断新旧密码是否一致
        if($oPa==$nPa){
            $this->error('新旧密码不能一样！');
            exit();
        }
        $re=Db::name('user')
        ->where('unick',Session::get('uNick'))
        ->where('upa',$oPa)
        ->find();
        // dump($re);

        // 新旧密码不一致
        if(!$re){
            // 旧密码输入错误
            $this->error('旧密码输入有误！');
            exit();
        }else{
            // 旧密码输入正确
            // 判断两次输入的新密码是否一致
            if($nPa!=$nPa1){
            $this->error('两次输入的密码不一致');
            }else{
                // 两次输入的新密码一致
                $re=Db::name('user')
                ->where('unick',Session::get('uNick'))
                ->update(['upa'=> $nPa]);
                if($re==1){
                    $this->success('密码修改成功，请重新登陆！','User/logout');
                }else{
                    $this->success('密码修改失败，请稍后再试！','User/changepa');
                }
        }
        }
    }

    /*2003010344 
    渲染登录页面
    */
    public function doLogin()
    {
        //链式查询
        // $re=Db::name('user')
        //     ->where('unick');
        //获取用户名
    $uName=trim($this->request->param('unick'));
    $uPa=md5(trim($this->request->param('upa')));

    $re= Db::name('user')
        ->where('unick',$uName)
        ->where('upa',$uPa)
        ->value('uImg');
        // ->find();
    dump($re);
    if($re == null){
        //登录验证失败
        $this ->error('登录失败','User/logIn');
    }else{
        $re1= Db::name('user')
        ->field('unick,uimg,upower')
        ->where('unick',$uName)
        ->where('upa',$uPa)
        ->find();
    dump($re1);
        if($re1['upower']=='不能登录，不能发言')
        {
            // 该用户禁止登录，禁止发言
            $this->error('您的账号禁止登录，请联系管理员解决问题：admin@sziit.edu.cn!');
        }
        // if($re1['upower']=='正常登录，不能发言')
        // {
        //     // 该用户禁止登录，禁止发言
        //     $this->error('您的账号禁止发言，请联系管理员解决问题：admin@sziit.edu.cn!');
        // }


        //登录验证成功
        // echo '成功';
        //发张凭证
        //session类
        Session::set('uNick',$uName);
        // //session助手函数
        // session('uNick',$uName);

        // 保存头像
        Cookie::set('uImg',$re,24*60*60);

        $this -> success('登录成功','Index/index');
    }
    }

    // 渲染上传头像
    public function me(){
        //判断是否登录
        $this->check();
        return view();
    }

    /*2003010344 
    上传并更新头像
    */
    public function upMe(){
        //判断是否登录
        $this->check();
        //获取文件对象
        $file= $this->request->file('uimg');
        // dump($file);
        
        //放在指定位置，名字也要自己设定
        $saveName = Filesystem ::disk('public')-> putFile('',$file);
        // dump($saveName);

        //完整的文件路径
        $fileName=$this->app->getRootPath().'public/static/yankx_forum/upload/'.$saveName;

        //判断文件是否已经到位
        if(!file_exists($fileName)){
            //文件不存在
            $this->error('文件上传失败，请稍后再试！');
            exit;
        }

        //更新头像
        $change = Db::name('user')
                ->where('unick',Session::get('uNick'))
                ->update(['uimg'=>$saveName]);
        
        //反馈更新结果
        if($change ==1){
            //更新成功
            // 删除旧头像 不能删除默认头像
            if(Cookie::get('uImg') !=='me.png'){
                unlink($this->app->getRootPath().'public/static/yankx_forum/upload/'.Cookie::get('uImg'));
            }

            // 判断保存的头像长度是否大于7，大于7，就是非默认头像，可以删除
            // if(strlen(Cookie::get('uImg')) >7){
            //     unlink($this->app->getRootPath().'public/static/yankx_forum/upload/'.Cookie::get('uImg'));
            // }

            // 更改cookie值
            Cookie::set('uImg',$saveName,24*60*60);

            $this->success('头像上传成功','Index/index');

        }else{
            //更新失败
            // 更新失败，头像冗余，需要删除
            unlink($fileName);
            $this->success('头像上传失败，请稍后再试！');
        }


    }

    // 注销
    public function logOut()
    {
        // 判断用户是否登录
        $this->check();
        // 清除用户的session信息
        // session类
        Session::delete('uNick');

        Cookie::delete('uImg');
        // Session::clear();
        // session助手函数
        // session('uNick',null);
        // session(null);
        $this->success('注销成功','user/login');
    }
}
