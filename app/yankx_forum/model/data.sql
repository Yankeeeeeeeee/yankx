--创建数据库
create database yankx_forum
        default charset utf8
        collate utf8_general_ci;
-- 2003010344
--使用数据库
use yankx_forum;

--创建用户表
create table yankx_user
(
    unick varchar(10) primary key,
    upa char(32),
    uemail varchar(30),
    utel varchar(15),
    uimg char(46) default 'me.png'
);
--修改user表的字段uimg，长度改为46
ALTER table yankx_user modify uimg char(46) default 'me.png';
ALTER table yankx_user add column upower varchar(15);

--创建测试用户
insert into yankx_user(unick,upa,uemail,upower)
        values
        ('tom',md5('123456'),'tom@sziit.edu.cn','正常登录，正常发言');
insert into yankx_user(unick,upa,uemail,upower)
        values
        ('jack',md5('123456'),'jack@sziit.edu.cn','正常登录，正常发言');
insert into yankx_user(unick,upa,uemail,upower)
        values
        ('yankx',md5('123456'),'yankx@sziit.edu.cn','正常登录，正常发言');

--创建用户
create user 'yankx'@localhost identified by '87654321';
--给用户授权
grant select,update,insert on yankx_forum.* to 'yankx'@localhost;

--由于在MySQL 8.0.11中，caching_sha2_password是默认的身份验证插件，而不是以往的mysql_native_password
use mysql;
ALTER USER 'yankx'@localhost IDENTIFIED WITH mysql_native_password BY '87654321';
FLUSH PRIVILEGES;

use yankx_forum;
--创建原贴表
create table yankx_mes
(
        mid int auto_increment primary key not null,
        mtitle varchar(30) not null,
        mcontent text not null,
        munick varchar(10) not null,
        mcreateat timestamp default CURRENT_TIMESTAMP,
        msid int not null
);
ALTER table yankx_mes modify mcreateat timestamp default CURRENT_TIMESTAMP;

--添加帖子测试数据
insert into yankx_mes
        (mtitle,mcontent,munick,msid)
        values
        ('新帖子01','新帖子01内容','tom',1);

insert into yankx_mes
        (mtitle,mcontent,munick,msid)
        values
        ('新帖子02','新帖子02内容','jack',1);

insert into yankx_mes
        (mtitle,mcontent,munick,msid)
        values
        ('新帖子03','新帖子03内容','jack',1);

insert into yankx_mes
        (mtitle,mcontent,munick,msid)
        values
        ('新帖子04','新帖子04内容','yankx',2);

--创建回复表
create table yankx_res
(
        rid int auto_increment primary key not null,
        rcontent text,
        runick varchar(10) not null,
        rcreateat timestamp default CURRENT_TIMESTAMP,
        rmid int not null
);

--添加回复
insert into yankx_res
        (rcontent,runick,rmid)
        values
        ('新帖子01的回复','tom',1);

insert into yankx_res
        (rcontent,runick,rmid)
        values
        ('新帖子01的回复','jack',1);

--创建板块表
create table yankx_section
(
        sid int auto_increment primary key not null,
        sname varchar(20) not null,
        sremark varchar(50) not null
);

--添加板块
insert into yankx_section
        (sname,sremark)
        values
        ('求助交流','求助交流'),
        ('LearnKu专区','LearnKu专区'),
        ('技术合作','技术合作');

insert into yankx_section
        (sname,sremark)
        values
        ('招聘求职','招聘求职'),
        ('项目合作','项目合作'),
        ('PHP专区','PHP专区'),
        ('前端开发','前端开发');
