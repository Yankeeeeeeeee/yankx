<?php

include './DepStu.php';
include './Sziit.php';
//2003010344

//实例化类SziitStu，调用vocation方法
$stuA = new \school\stu\SziitStu('郑庆燕',2003010350);
$stuA->testId();
$stuA -> vocation(2);

//实例化类DepStu，调用study方法，vocation方法
$stuB = new DepStu('颜可欣',2003010344);
$stuB->testId();
$stuB -> vocation(1);
$stuB ->study("计算机应用技术专业");