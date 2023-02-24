<?php

namespace app\yankx_forum\controller;

// use think\facade\Config;

class SziitStu
{
    const SCHOOL = '深圳信息职业技术学院';
    public static $stuDep = '计算机学院';
    public $stuName;
    public $stuId;


    function __construct($name,$id)
    {
        $this ->$stuName = $name;
        $this ->$stuId = $id;
    }
    public function vocation($month=1|2)
    {
        if($month=1)
        {
            echo "深圳信息职业技术学院的学生放寒假啦";
        }else{
            echo "深圳信息职业技术学院的学生放暑假啦";
        }
    }
}

$stuA = new Stu('张三',2003010344);
$stuA -> vocation(1);
echo $stuA ->stuName;
echo $stuA ->stuId;
echo Stu::SCHOOL;