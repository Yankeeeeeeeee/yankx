<?php

namespace school\stu;

// use think\facade\Config;
//2003010344
class SziitStu
{
    const SCHOOL = '深圳信息职业技术学院';
    // public static $stuDep = '计算机学院';
    public $stuName;
    public $stuId;
    public $month;


    function __construct($name,$id)
    {
        $this ->stuName = $name;
        $this ->stuId = $id;
    }
    public function testId()
    {
        echo $this->stuId;
    }
    public function vocation($month=1|2)
    {
        if($month==1)
        {
            echo self::SCHOOL."的学生放寒假啦<br/>";
        }else{
            echo self::SCHOOL."的学生放暑假啦</br>";
        }
    }
}

// $stuA = new \app\school\controller\Stu('张三',2003010344);
// $stuA -> vocation(1);
// echo $stuA ->stuName;
// echo $stuA ->stuId;
// echo Stu::SCHOOL;