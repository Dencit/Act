<?php
/* Created by User: soma Worker: 陈鸿扬 Date: 16/7/28  Time: 09:37 */

namespace Controlers;

use Commons\date;
use \Commons\tool as tool;//工具类
use \Commons\jump as jump;
use \Controlers\urlRoute as urlRoute;
use \Controlers\urlSerial as I;

use \Modelers\model as model;//model装载器
use \Https\weiApi as weiApi;

use Commons\probability as probability;//概率工具组


class indexAx extends baseControler {

    private static $uid;
    private static $MS;

    function __construct(){

        //判断用户身份
        $glob_usr=$this->homeDescAx();
        self::$uid=$glob_usr->uid;

        self::$MS = model::init('indexAx');//一个 controler 对应一个 同名 modeler
        //model::set();//加载 index modeler 可同时传参给 构造函数

    }

    function testPost(){

        $result=json_encode($_POST);
        tool::jsonResult($result,'0','post ok !');

    }

    function testPostList(){

        $result=json_encode($_POST);
        tool::jsonResult($result,'0','post list ok !');

    }

    function testGet(){

        $data=new \stdClass();
        $data->list1='testGetA';
        $data->list2='testGetB';
        $data->list3='list four';
        $data->list5='testGetE';

        tool::jsonResult($data,'0','get ok !');
    }


    function testGetList(){

        $data=[];
        $data[0]=new \stdClass();
        $data[0]->list1='testGetList A1';
        $data[0]->list2='testGetList B1';
        $data[0]->list3='list four';
        $data[0]->list5='testGetList E1';
        $data[1]=new \stdClass();
        $data[1]->list1='testGetList A2';
        $data[1]->list2='testGetList B2';
        $data[1]->list3='list three';
        $data[1]->list5='testGetList E2';
        $data[2]=new \stdClass();
        $data[2]->list1='testGetList A3';
        $data[2]->list2='testGetList B3';
        $data[2]->list3='list four';
        $data[2]->list5='testGetList E3';

        tool::jsonResult($data,'0','get list ok !');
    }


    function usrState(){

        //$this->homeDesc()方法里 统一做身份验证，这里默认为通过。

        $result=new \stdClass();//初始化返回载体

        ////用户信息
        $select='uid,name,sex,photo';
        $USR_INFO=self::$MS->rowSelect(USR_INFO,$select,'uid/'.self::$uid);
        //var_dump($USR_INFO);//
        if(!$USR_INFO){
            session_regenerate_id(true);
            tool::mk_session(array('glob_usr'),1);
            tool::jsonResult('','-1','无此用户!请清除微信缓存。','/HolyEgg/?/weixin/index/');
        }
        else{
            $result->uid=$USR_INFO->uid;
            $result->name=$USR_INFO->name;
            $result->photo=$USR_INFO->photo;
        }
        //\\

        ////用户已选状态
        $select='get_id,egg_id,time,ip';
        $USR_GET=self::$MS->rowSelect(USR_GET,$select,'uid/'.self::$uid);
        //var_dump($USR_GET);//
        if(!$USR_GET){
            $result->get_id=0;
            tool::jsonResult($result,'0');
        }else{
            $result->get_id=$USR_GET->get_id;
            $result->choosedEggId=$USR_GET->egg_id;
            $result->time=$USR_GET->time;
            $result->ip=$USR_GET->ip;
            //tool::jsonResult($result,'1','已经许过愿望,可继续分享');
            tool::jsonResult($result,'1');
        }
        //\\

    }


    function usrChooseEgg(){

        $result=new \stdClass();//初始化返回载体

        $nick=tool::is_Post('name');
        $choosedEggId=tool::is_Post('choosedEggId');
        //var_dump($choosedEggId);exit;//
        if(empty($choosedEggId)){ tool::jsonResult('','-1','未选择蛋！'); }

        $select='uid,get_id,egg_id';
        $where='uid/'.self::$uid;
        $order['time']='desc';
        $USR_GET=self::$MS->rowSelect(USR_GET,$select,$where,$order);

        $result->uid=self::$uid;
        $result->nick=$nick;
        $result->egg_id=$choosedEggId;
        $result->time=TIME;
        $result->ip=tool::ip();

        if(!$USR_GET){
            $rowAddCheck=self::$MS->rowAddCheck(USR_GET,$select,$where,$result,$order);
            if($rowAddCheck!="insertFail"||$rowAddCheck!="updateFail"){

                $USR_GET=self::$MS->rowSelect(USR_GET,$select,$where,$order);
                $result->get_id=$USR_GET->get_id;
            }

            tool::jsonResult($result,'0','');
        };

        $result->egg_id=$USR_GET->egg_id;
        $result->get_id=$USR_GET->get_id;
        tool::jsonResult($result,'1','已经许过愿望,可继续分享');//已经选过就 跳过提交。

    }



    function shareState(){

        $result=new \stdClass();
        $result->he=new \stdClass();
        $result->me=new \stdClass();

        $sid=tool::is_Post('sid');
        $get_id=tool::is_Post('get_id');
        //var_dump($sid);var_dump($get_id);
        if($sid==''||$get_id==''){ tool::jsonResult('','-1','','/HolyEgg/?/weixin/index/'); }


        ////分享者参与记录
        $select='uid,get_id,nick';
        $where='get_id/'.$get_id.',uid/'.$sid;
        $USR_GET=self::$MS->rowSelect(USR_GET,$select,$where,'time desc');
        //var_dump($USR_GET);//
        if(!$USR_GET){ tool::jsonResult('','-1','无此用户数据！','/HolyEgg/?/weixin/index/'); }
        //\\

        ////分享者个人信息
        $select='uid,photo';
        $where='uid/'.$sid;
        $USR_INFO=self::$MS->rowSelect(USR_INFO,$select,$where);
        //var_dump($USR_INFO);//
        if(!$USR_GET){ tool::jsonResult('','-1','无此用户数据！','/HolyEgg/?/weixin/index/'); }
        $result->he->sid=$USR_GET->uid;
        $result->he->name=$USR_GET->nick;
        $result->he->photo=$USR_INFO->photo;
        $result->he->get_id=$USR_GET->get_id;
        //\\


        ////支持者个人信息
        $select='uid,name,photo';
        $where='uid/'.self::$uid;
        $USR_INFO=self::$MS->rowSelect(USR_INFO,$select,$where);
        //var_dump($USR_INFO);//
        if(!$USR_INFO){ tool::jsonResult('','-1','无此用户数据！','/HolyEgg/?/weixin/index/'); }
        $result->me->uid=$USR_INFO->uid;
        $result->me->name=$USR_INFO->name;
        $result->me->photo=$USR_INFO->photo;
        //\\

        ////支持者已猜状态
        $select='f_guest';
        $where='uid/'.$sid.',fid/'.self::$uid;
        $USR_HELP=self::$MS->resultSelect(USR_HELP,$select,$where,'time desc');
        if($USR_HELP){ tool::jsonResult($result,'1'); }
        //\\
        if($sid==self::$uid){ tool::jsonResult($result,'1'); }//屏蔽自己选自己

        tool::jsonResult($result,'0');

    }

    function guestEgg(){

        $result=new \stdClass();
        $insertData=new \stdClass();

        //var_dump($_POST);//exit;

        $sid=tool::is_Post('sid');
        $name=tool::is_Post('name');
        $get_id=tool::is_Post('get_id');
        $guestEggId=tool::is_Post('guestEggId');//猜测egg_id
        $myuid=tool::is_Post('myuid');
        $myname=tool::is_Post('myname');

        if($sid==$myuid){ tool::jsonResult('','-1','不能猜自己的蛋！'); }

        $select='uid,fid';
        $where='uid/'.$sid.',fid/'.$myuid;
        $USR_HELP=self::$MS->rowSelect(USR_HELP,$select,$where);
        //if($USR_HELP){ tool::jsonResult('','-1','你已经猜过了！'); }

        $select='egg_id';
        $where='uid/'.$sid.',get_id/'.$get_id;
        $USR_GET=self::$MS->rowSelect(USR_GET,$select,$where,'time desc');
        if(!$USR_GET){ tool::jsonResult('','-1','无此用户数据！','/HolyEgg/?/weixin/index/'); }

        $insertData->uid=$sid;
        $insertData->u_nick=$name;
        $insertData->fid=$myuid;
        $insertData->f_nick=$myname;
        $insertData->time=TIME;
        $insertData->ip=tool::ip();
        $select='uid';
        $where='uid/'.$sid.',fid/'.$myuid;

        //var_dump($insertData);exit;//

        if($guestEggId!=$USR_GET->egg_id){

            $insertData->f_guest='false';
            self::$MS->rowAddCheck(USR_HELP,$select,$where,$insertData);

            //var_dump($insertData);exit;//

            $result->egg_yes_id=$USR_GET->egg_id;
            $result->bool=false;
            tool::jsonResult($result,'0','真可惜,猜错了!');
        }


        $insertData->f_guest='true';
        self::$MS->rowAddCheck(USR_HELP,$select,$where,$insertData);

        //var_dump($insertData);exit;//

        $result->egg_yes_id=$USR_GET->egg_id;
        $result->bool=true;
        tool::jsonResult($result,'0','恭喜你,猜对了!');


    }


    function friendGuest(){

        //var_dump($_POST);exit;//

        $sid=tool::is_Post('sid');
        $name=tool::is_Post('name');
        $get_id=tool::is_Post('get_id');

        $result=new \stdClass();
        $result->list=array();

        $select='u_nick,fid,f_nick,f_guest';
        $where='uid/'.$sid;
        $USR_HELP=self::$MS->resultSelect(USR_HELP,$select,$where,'time desc');
        if($USR_HELP){

            foreach($USR_HELP as $n=>$obj){

                $result->list[$n]=new \stdClass();
                $result->list[$n]->helper=$obj->f_nick;
                switch($obj->f_guest){
                    case 'true':
                        $rnd=rand(0,1);
                        $yes[0]='猜对了, 果然是你最懂我,快带我实现愿望吧！';$yes[1]='猜对了, 这么懂我,是不是留意我很久啦！';
                        $result->list[$n]->bool=$yes[$rnd];;
                        break;
                    case 'false':
                        $rnd=rand(0,1);
                        $no[0]='猜错了, 还不快请我吃饭了解我内心';$no[1]='猜错了, 友谊小船说翻就翻,来几份礼物补补船';
                        $result->list[$n]->bool=$no[$rnd];
                        break;
                }

                $select='name,photo';
                $where='uid/'.$obj->fid;
                $USR_INFO=self::$MS->rowSelect(USR_INFO,$select,$where);
                if($USR_INFO){
                    $result->list[$n]->helper=$USR_INFO->name.': ';
                    $result->list[$n]->helperAvata=$USR_INFO->photo;
                }

            }

        }

        ////正确答案
        $select='egg_id';
        $where='uid/'.$sid.',get_id/'.$get_id;
        $USR_GET=self::$MS->rowSelect(USR_GET,$select,$where);
        if(!$USR_GET){  }
        $result->egg_id=$USR_GET->egg_id;
        //\\


        //var_dump($result);//
        tool::jsonResult($result,'0','','');


    }



    function usrLottery(){

        $uid=self::$uid;

        //$probability=new probability();

        $MS=self::$MS;
        $index=$MS->index($uid);
        //var_dump($index);exit;//

            $rnd=rand(1,1000);
            $fileGet=probability::fileGetVal(CACHE.'/probability.php');
            $setInt=probability::setInterval($fileGet);
            //print_r($setInt);
            $item=probability::getSign($rnd,$setInt);
            //print_r($item);exit;

            if($index->gift!=0 ){
                $jsonPost['rnd']=$rnd;
                $jsonPost['interval']=$item['inv'];
                $jsonPost['item']=$item['s'];
                tool::jsonResult($jsonPost,'-1','haveGift');
            }


            $jsonPost['rnd']=$rnd;
            $jsonPost['interval']=$item['inv'];
            $jsonPost['item']=$item['s'];
            tool::jsonResult($jsonPost,'-1','emptyGift');

    }



} 