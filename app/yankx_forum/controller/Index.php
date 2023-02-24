<?php
declare (strict_types = 1);

namespace app\yankx_forum\controller;
//2003010344
// use think\Request;

use app\BaseController;
use think\facade\View;
//引用跳转扩展
    use \liliuwei\think\Jump;
use think\facade\Db;
use think\facade\Route;
use think\facade\Session;

class Index extends BaseController
{
    public function index($sid=0)
    {
        $re=$this->showSec();
        // dump($re);

        if($sid==0){
            // 查询所有版块
            // 视图渲染
            $sName='全部';
            $mes=Db::view('mes','mid,mtitle,munick,mcreateat')
                ->view('user','uimg','mes.munick=user.unick')
                ->order('mcreateat','desc')
                ->paginate(10);
        }else{
            // 查询指定版块帖子
            foreach ($re as $key => $value) {
            # code...
            if($value['sid']==$sid){
                $sName=$value['sname'];
                break;
            }
            }
        // dump($sName);
        // 视图渲染
        $mes=Db::view('mes','mid,mtitle,munick,mcreateat')
        ->view('user','uimg','mes.munick=user.unick')
        ->where('msid',$sid)
        ->order('mcreateat','desc')
        ->paginate(10);
        }   
        // dump($mes);
        $page=$mes->render();
        return view('',['mes'=>$mes,'sec'=>$re,'sName'=>$sName,'page'=>$page]);
        
    }

    /*渲染板块帖子列表 */
    public function view($sid=0)
    {
        $re=$this->showSec();
        // dump($re);
        // 获取sid值
        // $sid=$this->request->param('sid');
        // dump($sid);

        if($sid==0){
            // 查询所有版块
            // 视图渲染
            $sName='全部';
            $mes=Db::view('mes','mid,mtitle,munick,mcreateat')
                ->view('user','uimg','mes.munick=user.unick')
                ->order('mcreateat','desc')
                ->paginate(10);
        }else{
            // 查询指定版块帖子
            foreach ($re as $key => $value) {
            # code...
            if($value['sid']==$sid){
                $sName=$value['sname'];
                break;
            }
            }
        // dump($sName);
        // 视图渲染
        $mes=Db::view('mes','mid,mtitle,munick,mcreateat')
        ->view('user','uimg','mes.munick=user.unick')
        ->where('msid',$sid)
        ->order('mcreateat','desc')
        ->paginate(5);
        }   
        // dump($mes);
        $page=$mes->render();
        return view('',['mes'=>$mes,'sec'=>$re,'sName'=>$sName,'page'=>$page]);
    }
    public function post()
    {
        //判断用户是否登录
        $this->check();
        //合法用户才能访问发表帖子
        $uName=Session::get('uNick');
        dump($uName);
        $re1= Db::name('user')
        ->field('unick,upower')
        ->where('unick',$uName)
        ->find();
        dump($re1);
        if($re1['upower']=='正常登录，不能发言')
        {
            // 该用户禁止登录，禁止发言
            $this->error('您的账号禁止发言，请联系管理员解决问题：admin@sziit.edu.cn!');
        }
        else{
            $re=$this->showSec();
        $sName='';
        // 获取sid值
        $sid=$this->request->param('sid');
        foreach ($re as $key => $value) {
            # code...
            if($value['sid']==$sid){
                $sName=$value['sname'];
                break;
            }
            }
        }
        return view('',['sName'=>$sName]);
    }

    /*2003010344
    * 渲染板块帖子列表
    */
    public function doPost()
    {
        // 判断是否已经登录
        $this->check();

    //     $re1= Db::name('user')
    //     ->field('upower')
    //     ->find();
    // dump($re1);
    //     if($re1['upower']=='正常登录，不能发言')
    //     {
    //         // 该用户禁止登录，禁止发言
    //         $this->error('您的账号禁止发言，请联系管理员解决问题：admin@sziit.edu.cn!');
    //     }

        // 获取sid值
        $sid=$this->request->param('sid');

        //获取表单信息
        $mTitle=$this->request->post('mtitle','','trim,htmlspecialchars');
        $mContent=$this->request->post('mcontent','','htmlspecialchars');
        // 准备帖子信息
        $data=[
            'mtitle'    =>$mTitle,
            'mcontent'  =>$mContent,
            'munick'    =>Session::get('uNick'),
            'msid'      =>$sid,
        ];
        // 添加数据的链式操作
        $post=Db::name('mes')->insert($data);
        dump($post);
        // $post=1表示添加数据成功，反之则添加失败
        if($post==1)
        {
            #发帖成功
            // $this->success('发帖成功','Index/view');
            $this->success('发帖成功',(string)Route::buildUrl('Index/view',['sid'=>$sid]));
            #发帖失败
            // $this->error('发帖失败','Index/post');
            $this->error('发帖失败',(string)Route::buildUrl('Index/post',['sid'=>$sid]));
        }
    }

    public function detail()
    {
        // 获取mid值
        $mid=$this->request->param('mid');
        // dump($mid);
        // 视图查询帖子详情
        // 表一mes表二user表三section
        $mes=Db::view('mes','mtitle,mcontent,munick,mcreateat')
        ->view('user','uimg','mes.munick=user.unick')
        ->view('section','sname','mes.msid=section.sid')
        ->where('mid',$mid)
        ->find();
        // dump($mes);

        // 视图查询回复详细信息
        $res=Db::view('res','rcontent,runick,rcreateat')
        ->view('user','uimg','user.unick=res.runick')
        ->view('mes','mid','mes.mid=res.rmid')
        ->where('rmid',$mid)
        ->select();
        // dump($res);

        return view('',['mes'=>$mes,'res'=>$res]);
    }

    
    /* 2003010344
    回复帖子例子
    */ 
    public function doRes()
    {
        $this->check();

        // 获取mid值
        $mid=$this->request->param('mid');

        //获取表单信息
        $rContent=$this->request->post('rcontent','','htmlspecialchars');
        // 准备回复的信息
        $data=[
            'rcontent'  =>$rContent,
            'runick'    =>Session::get('uNick'),
            'rmid'      =>$mid,
        ];
        // 
        $dores=Db::name('res')->insert($data);
        if($dores==1)
        {
            #回复成功
            // $this->success('回复成功','Index/detail');
            $this->success('回复成功',(string)Route::buildUrl('Index/detail',['mid'=>$mid]));
            // (string)Route::buildUrl('Index/doRes',['mid'=>$mid])
            #回复失败
            // $this->error('回复失败','Index/doRes');
            $this->error('回复失败',(string)Route::buildUrl('Index/doRes',['mid'=>$mid]));
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
                    $this->success('密码修改失败，请稍后再试！','Index/changepa');
                }
        }
        }
    }

    
    public function contact()
    {
        return view();
    }
}
