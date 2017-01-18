<?php
/* Created by User: soma Worker: 陈鸿扬 Date: 16/8/1  Time: 15:42 */

namespace Controlers;

use \Commons\tool as tool;//工具类
use \Commons\jump as jump;
use \Controlers\urlSerial as I;

use \Modelers\model as model;//model装载器
use \Https\weiApi as weiApi;


class weixin extends baseControler {

    function __construct(){

        model::init('weixin');//一个 controler 对应一个 同名 modeler
        //model::set();//加载 index modeler 可同时传参给 构造函数

    }

    function test()//伪装验证
    {
        $MS=model::set();

        //Home/?/weixin/testInit/u=[PASS_WORD]/ts=1/uid=1/
        $u=tool::isSetRe( I::have('u') );
        $ts=tool::isSetRe( I::have('ts') );
        $uid=tool::isSetRe( I::have('uid') );//序列引用

        $hint['noData']=" 没有 access_token 或找不到当前'uid'. 如果没有 access_token请在微信登录一次非测试页，再回来用PC访问该测试页. ";
        $hint['noDataEn']="no token for this ' uid ' or no found this 'uid'. if no token, please Login on WeiXinApp then Comeback to Visit on PC";

        if($u=='[PASS_WORD]' && $ts!='' && $uid!='')
        {

            switch($ts){
                default:
                    break;
                case 0 :
                    tool::mk_session(array('glob_usr'),1);
                    session_unset();session_destroy();//
                    tool::jsonExit(array("state"=>'noDebug',"SESSION"=>'unset'));
                    break;
                case 1 :
                    $whereArray['uid']=$uid;
                    $user=$MS->rowSelect(USR,'*',$whereArray);
                    //var_dump($user);exit;//

                    if($user){

                        $glob_usr=new \stdClass();
                        $glob_usr->sign=session_id();//设置服务器的sign
                        $glob_usr->uid = $user->uid;
                        $glob_usr->openid= $user->openid;
                        $glob_usr->access_token= $user->access_token;
                        tool::mk_session( array('glob_usr' => $glob_usr) );
                        //var_dump( tool::get_session('glob_usr') );exit;//

                        jump::head("./zyqb/index.html");//成功跳转index

                    }else{

                        $orderArray['uid']='ASC';
                        //$orderArray['limit']='0,5';//
                        $selectArray=array('uid','nickname');
                        $usrTest=$MS->resultSelect(USR,$selectArray,'-',$orderArray);

                        //var_dump($usrTest);exit;//

                        echo "| UID || NICK_NAME |"."<br/>";
                        foreach($usrTest as $k=>$v){
                            echo "| ".$usrTest[$k]->uid." || ".$usrTest[$k]->nickname." |"."<br/>";
                        }
                        echo $hint['noData']."<br/>";
                        tool::jsonExit($hint['noDataEn']);

                    }

                    //tool::jsonExit(array("state"=>'debug'));
                    break;

            }

        }


    }

    function index(){

        $sid=tool::isSetRe( I::have('sid') );//侦听url中的sid序列单元
        $get=tool::isSetRe( I::have('get') );
        //var_dump($sid);exit;//

        $glob_usr=tool::get_session('glob_usr');//var_dump($glob_usr);//exit;//
        $openid=$glob_usr->openid;
        $access_token=$glob_usr->access_token;
        //var_dump($openid);var_dump($access_token);exit;//

        //$openid=true;$access_token=true;//
        if(!$openid||!$access_token)//没有$openid和$access_token的情况//snsapi_userinfo
        {
            if($sid){ weiApi::usrAuth("http://act.host.com/HolyEgg/?/weixin/auth/sid-".$sid."/get-".$get."/","snsapi_base");exit; }

            weiApi::usrAuth("http://act.host.com/HolyEgg/?/weixin/auth/","snsapi_base");exit;
        }


        if($sid!=''&&$get!=''){
            //如果用户访问自己的分享链接 就跳到首页。
            /*
            $uid_get=$_SESSION[PREFIX.'uid']; if($sid_get==$uid_get){ jump::head("./zyqb/index.html"); }
            */
            jump::head("./zyqb/share.html#share=&sid=".$sid."&get=".$get);exit;
        }

        jump::head("./zyqb/index.html");
    }

    function auth(){

        //var_dump($_GET);exit;//

        $openid = tool::isSetRe($_GET['openid']);
        $access_token = tool::isSetRe($_GET['access_token']);
        //var_dump($openid);var_dump(access_token);exit;//

        if (!$openid || !$access_token){ exit('fail to get openid or access_token.'); }

        $MS=model::set();

        ////准备 user 表 入库数据
        $data = weiApi::usrInfo($openid,$access_token);
        //var_dump($data);exit;//

        $globeAccessToken = weiApi::globeAccessToken();
        $data_g = weiApi::subscribe($openid, $globeAccessToken);
        //var_dump($data);echo"<br/>";var_dump($data_g);exit;//
        $usrData = weiApi::usrDataMake($data, $data_g);//获取用户数据
        $usrData->openid = $openid;
        $usrData->access_token = $access_token;
        //print_r($usrData);exit;//
        $whereArray['openid'] = $openid;
        //\\

        ////检查式新增微信用户数据
        $rowAddCheck=$MS->rowAddCheck(USR,'uid',$whereArray,$usrData);
        //var_dump($rowAddCheck);exit;//
        //\\

        ////准备 user_info 表 入库数据
        $user = $MS->rowSelect(USR, 'uid', $whereArray);//筛选
        //var_dump($user);exit;//
        $infoData=array();
        if($user){
            $infoData['uid']=$user->uid;
            $infoData['photo']=$usrData->headimgurl;
            $infoData['name']=$usrData->nickname;
            $infoData['sex']=$usrData->sex;
            $infoData['type']='1';
            $infoData['device']='0';
        }
        //\\

        switch($rowAddCheck){
            case 'insertOk'://用户无登记的情况

                //新建用户数据
                $userInsert = $MS->rowInsert(USR_INFO,$infoData);
                if (!$userInsert) { exit ('$userInsert fail!'); }

                break;
            case 'updateOk'://用户有登记的情况

                //更新微信用户资料
                unset($whereArray);
                $whereArray['uid']=$infoData['uid'];
                $MS->rowAddCheck(USR_INFO,'uid',$whereArray,$infoData);//检测到数据重复 会跳过更新

                break;
        }



        $glob_usr=new \stdClass();
        $glob_usr->sign=session_id();//设置服务器的sign
        $glob_usr->uid = $user->uid;
        $glob_usr->openid= $openid;
        $glob_usr->access_token= $access_token;
        tool::mk_session( array('glob_usr' => $glob_usr) );
        //var_dump( tool::get_session('glob_usr') );exit;//

        $sid=tool::isSetRe( I::have('sid') );
        $get=tool::isSetRe( I::have('get') );
        if($sid!=''&&$get!=''){ jump::head("./?/weixin/index/sid-".$sid."/get-".$get."/");exit; }

        jump::head("./?/weixin/index");

    }



} 