<?php
//引用命名空间
class DepStu extends \school\stu\SziitStu{
//公开属性
public $stuMajor;

//定义方法study()
public function study($major)
{
    $this->stuMajor=$major;
    echo $this->stuMajor.'，你的专业'.$this->stuMajor.'学制3年，共需修123学分';
}

}