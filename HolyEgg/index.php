<?php
/* Created by User: soma Worker: 陈鸿扬  Date: 16/7/28  Time: 09:27 */

require_once('../Common/app.php');

////数据库二次修改

//线上
define('DBUSER','root');
define('DBPASSWD','password');
define('DBNAME','act');
define('DBHOST','127.0.0.1');
//线下
/*define('DBUSER','root');
define('DBPASSWD','root');
define('DBNAME','act');
define('DBHOST','127.0.0.1');*/

////HolyEgg
//PREFIX
define('PREFIX','holy_egg_');
//all databases name
define('USR',PREFIX.'users');
define('USR_INFO',PREFIX.'users_info');
define('USR_GET',PREFIX.'users_get');
define('USR_HELP',PREFIX.'users_help');
//\\

//\\数据库二次修改


////微信测试地址开关//wxsefve
//DEBUG_PREFIX
define('DEBUG_URL','pub');

switch(DEBUG_URL){
    case 'pub' :

        //http_base
        define('HTTP_BASE','http://act.host.com');

        //redirect_uri
        define('OAUTH2_URI','http://api.host.com/weixin/OAuth2');
        //access_token
        define('ACCESS_TOKEN','http://api.host.com/weixin/access_token');

        break;
    case 'test':

        //http_base
        define('HTTP_BASE','http://act.host.test');

        //redirect_uri
        define('OAUTH2_URI','http://act.host.test/weixin/OAuth2');
        //access_token
        define('ACCESS_TOKEN','http://act.host.test/weixin/access_token');

        break;
}
//\\微信测试地址开关


////框架设置
define('ROOT_PROJECT','HolyEgg');//当前项目主目录 controler.class.php , model.class.php 调用
define('ROOT_CONTROLER','Controler');//二级目录//控制器目录//controler.class.php 调用
define('ROOT_MODELER','Modeler');//二级目录//数据模型目录//model.class.php 调用

$controler=new \controlers\controler();
$controler->uri('Path_Info');//开始侦听url路由参数,加载 controler 虚拟页面
//\\框架设置


