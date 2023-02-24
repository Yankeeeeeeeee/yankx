<?php
declare (strict_types = 1);

namespace app\yankx_forum_admin\controller;

use app\BaseController;
use think\facade\Db;
use think\Request;

class Deal extends BaseController
{
    /**帖子管理*/
    public function mesDeal($sid=0)
    {
        // 判断是否已经登录
        $this->checkb();
        // 查询版块信息
        $re =$this->showSec();

        if($sid==0){
            $sid=$re[0]['sid'];
            $sName=$re[0]['sname'];
        }else{
            // 查询指定版块帖子
            foreach ($re as $key => $value) {
            # code...
            if($value['sid']==$sid){
                $sName=$value['sname'];
                break;
            }
            }
        } 

        // 获取输入的搜索值
        $search=$this->request->param('search_input');
        if($search!='')
        {
            $mes=Db::name('mes')
            ->field('mtitle,mid,mcreateat,munick,mcontent')
            // ->where('msid',$sid)
            // ->where('sbin',1)
            // ->where('mbin',1)
            ->where("mtitle LIKE '%" .$search."%' or mcontent LIKE '%" .$search."%'")
            ->whereor("munick LIKE '%" .$search."%' or mcontent LIKE '%" .$search."%'")
            ->whereor("mcontent LIKE '%" .$search."%' or mcontent LIKE '%" .$search."%'")
            ->order("mcreateat","desc")
            ->paginate([
                "list_rows" =>5,
                'query' =>request()->param()
            ]);
            $sName="";
        }else{
            // 分页显示的查询当前板块的帖子，每页显示10条
            $mes=Db::name('mes')
            ->field('mtitle,mid,mcreateat,munick')
            ->where('msid',$sid)
            // ->where('sbin',1)
            // ->where('mbin',1)
            ->order("mcreateat","desc")
            ->paginate([
                "list_rows" =>5,
                'query' =>request()->param()
            ]);
        }
        // dump($mes);
        $page=$mes->render();
        return view('',['mes'=>$mes,'sec'=>$re,'sName'=>$sName,'page'=>$page]);
    
    }

    // 帖子内容
    public function aDetail()
    {
        // 判断是否已经登录
        $this->checkb();
        // 获取mid值
        $mid=$this->request->param('mid');

        // 视图查询帖子详情
        // 表一mes表二section
        $mes=Db::view('mes','mtitle,mcontent,munick,mcreateat')
        // ->view('user','uimg','mes.munick=user.unick')
        ->view('section','sname','mes.msid=section.sid')
        ->where('mid',$mid)
        ->find();
        // dump($mes);

        // 视图查询回复详细信息
        $res=Db::view('res','rid,rcontent,runick,rcreateat')
        // ->view('user','uimg','user.unick=res.runick')
        ->view('mes','mid','mes.mid=res.rmid')
        ->where('rmid',$mid)
        ->select();
        // dump($res);

        return view('',['mes'=>$mes,'res'=>$res]);
        
    }

    // 删除帖子
    public function mesDel($mid=0)
    {
        // 判断是否已经登录
        $this->checkb();
        
        if($mid==0){
            $this->error('参数有误！');
            exit();
        }
        // 删除帖子
        $mes=Db::name("mes")
        ->where("mid",$mid)
        ->delete();
        // 删除帖子成功，删除帖子回复
        if($mes!=0){
            $res=Db::name('res')
            ->where('rmid',$mid)
            ->delete();
            $this->success("删除帖子成功！");
        }else{
            // 删除失败，提示并跳转
            $this->error("帖子删除失败，请稍后再试！");
        }
        
        return view();
    }

    // 删除回复
    public function resDel($rid)
    {
        // 判断是否已经登录
        $this->checkb();

        if($rid==0){
            $this->error('参数有误！');
            exit();
        }
        // 删除帖子
        $res =Db::name('res')
        ->where('rid',$rid)
        ->delete();
        // 删除成功
        if($res!=0){
            $this->success('回复删除成功！');
        }else{
            $this->error('回复删除失败，请稍后再试！');
        }

        return view();
    }

    // 板块管理
    public function secDeal()
    {
        // 判断是否已经登录
        $this->checkb();
        // 获取输入的搜索值
        $search=$this->request->param('search_input');

        if($search!=''){
            $re=Db::name('section')
            ->whereOr('sname','LIKE','%'.$search.'%')
            ->whereOr('sremark','LIKE','%'.$search.'%')
            ->paginate([
                "list_rows" =>5,
                'query' =>request()->param()
            ]);
        }else{
            $re=Db::name('section')
            ->paginate([
                "list_rows" =>5,
                'query' =>request()->param()
            ]);
        }
        $page=$re->render();
        return view('',['sec'=>$re,'page'=>$page]);
    }

    // 修改板块
    public function secInfo($sid="")
    {
        // 判断是否已经登录
        $this->checkb();

        if($sid=='')
        {
            $this->error('参数有误！');
            exit();
        }
        $re=Db::name('section')
        ->where('sid',$sid)
        ->find();
        // dump($re['sid']);
        return view('',['sec'=>$re]);
    }
    // 执行板块修改
    public function secInfoModi($sid="")
    {
        // 判断是否登录
        $this->checkb();
        
        if($sid=='')
        {
            $this->error('参数有误！','Deal/secDeal');
            exit();
        }
        $re1=Db::name('section')
        ->field('sid')
        ->where('sid',$sid)
        ->find();
        // dump($re1);
        // 获取输入的新板块名称和备注
        $sName=$this->request->param('sname');
        $sRemark=$this->request->param('sremark');

        if($re1!='')
        {
            if($sName!='' && $sRemark!='')
            {
                $data=[
                    'sname'=>$sName,
                    'sremark'=>$sRemark
                ];
            }else if($sName!=''){
                $data=[
                    'sname'=>$sName,
                ];
            }else if($sRemark!=''){
                $data=[
                    'sremark'=>$sRemark,
                ];
            }else{
                $this->error('板块未更新，请检查表单是否填写！');
            }
            $re2=Db::name('section')
            ->where('sid',$sid)
            ->update($data);
            if($re2==1)
            {
                $this->success('板块修改成功！','deal/secDeal');
            }else{
                $this->success('板块修改失败，请稍后再试！');
            }
        }
    }

    //删除版块
    public function secDel($sid=0)
    {
        // 判断是否已经登录
        $this->checkb();
        if($sid== 0)
        {
            $this->error('参数有误！');
            exit();
        }
        // 删除帖子
        $sec=Db::name('section')
        ->where('sid',$sid)
        ->delete();
        // 删除成功
        if($sec!=0)
        {
            // 删除回复
            $mes=Db::name('mes')
            ->field('mid')
            ->where('msid',$sid)
            ->select();
            foreach ($mes as $key => $value) {
                # code...
                Db::name('res')
                ->where('rmid',$value['mid'])
                ->delete();
            }
            // 删除帖子
            Db::name('mes')
            ->where('msid',$sid)
            ->delete();
            $this->success("板块及其下所有帖子、回复都删除成功！");
        }else{
            // 删除失败，并提示跳转
            $this->error("板块删除失败，请稍后再试！");
        }
    }

    // 添加板块
    public function secAdd()
    {
        // 判断是否已经登录
        $this->checkb();
        return view();
    }

    // 执行用户添加板块权限
    public function secDoAdd()
    {
        // 判断是否已经登录
        $this->checkb();
        // 获取输入的新板块名称+新板块备注
        $s_Name=$this->request->param('s_name');
        $s_Remark=$this->request->param('s_remark');
        // 
        $re=Db::name('section')
        ->where('sname',$s_Name)
        ->find();
        // 将输入的值与数据库的值进行比对，如果结果为空，则添加
        if($re!=null)
        {
            // 结果不为空，则返回错误
            $this->error("板块重名！");
            exit();
        }
        // 将输入的值转换成数组
        $data=[
            'sname'=>$s_Name,
            'sremark'=>$s_Remark
        ];
        $re=Db::name('section')->insert($data);
        if($re==1)
        {
            // 添加板块成功
            $this->success("板块：【'$s_Name'】添加成功！",'Deal/secDeal');
        }else{
            // 添加板块失败
            $this->error("板块添加失败，请稍后再试！");
        }
    }

    // 用户管理首页
    public function userDeal()
    {
        // 判断是否已经登录
        $this->checkb();

        // 获取输入的搜索值
        $userSearch=$this->request->param('userSearch');
        if($userSearch!='')
        {
            $re=Db::name('user')
            ->field('unick,uimg,uemail,upower')
            ->whereOr('unick','LIKE','%'.$userSearch.'%')
            ->whereOr('uemail','LIKE','%'.$userSearch.'%')
            ->paginate([
                'list_row' =>10,
                'query'=>request()->param()
            ]);
        }else{
            $re=Db::name('user')
            ->field('unick,uimg,utel,uemail,upower')
            ->paginate(10);
        }
        $page=$re->render();
        return view('',['user'=>$re,'page'=>$page]);
    }

    // 用户修改权限
    public function userPower($unick='')
    {
        // 判断是否已经登录
        $this->checkb();

        if ($unick=='')
        {
            $this->error('参数有误！');
            exit();
        }
        $re=Db::name('user')
        ->field('unick,upower')
        ->where('unick',$unick)
        ->find();
        // dump($re);
        return view('',['user'=>$re]);
    }

    // 执行用户修改权限
    public function userPowerModi($unick='')
    {
        // 判断是否已经登录
        $this->checkb();

        if ($unick=='')
        {
            $this->error('参数有误！');
            exit();
        }
        $re1=Db::name('user')
        ->field('unick')
        ->where('unick',$unick)
        ->find();
        // 获取选择的权限
        // $uPower=$this->request->param('upower');
        $uPower=$_POST['upower'];
        // dump($uPower);
        if($re1!=null)
        {
            $re2=Db::name('user')
            ->where('unick',$unick)
            ->update(['upower'=>$uPower]);
            if($re2==1)
            {
                $this->success('用户【'.$unick.'】的权限修改成功！','deal/userdeal');
            }else{
                $this->error('用户【'.$unick.'】的权限修改失败，请稍后再试！');
            }
        }
    }

}
