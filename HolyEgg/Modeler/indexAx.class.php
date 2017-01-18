<?php
/* Created by User: soma Worker: 陈鸿扬 Date: 16/7/30  Time: 12:01 */

namespace Modelers;
use Modelers\baseModel;

class indexAx extends baseModel {


    function __construct(){
        new parent;//可以使用baseModel所有查询方法,包括wpdb的;

    }


    function index($uid_get){

        $whereArray['uid']=$uid_get;

        $select='uid,nickname,headimgurl,sex';
        $USR=self::rowSelect(USR,$select,$whereArray);
        if(!$USR){ exit('$USR fail!'); }

        $select='egg_id';
        $USR_GET=self::rowSelect(USR_GET,$select,$whereArray,'time desc');
        //var_dump($USR_GET);exit;//
        if(!$USR_GET){ exit('$USR_GET fail!'); }

        $select='mobile';
        $USR_INFO=self::rowSelect(USR_INFO,$select,$whereArray);
        if(!$USR_INFO){ exit('$USR_INFO fail!'); }

        $data=new \stdClass();
        $data->uid=$USR->uid;
        $data->nickname=$USR->nickname;
        $data->headimgurl=$USR->headimgurl;
        $data->sex=$USR->sex;
        $data->gift=$USR_GET->egg_id;
        $data->mobile=$USR_INFO->mobile;

        //var_dump($data);exit;

        return $data;
    }


    function share($sid_get){

        $whereArray['uid']=$sid_get;

        $selesct='uid,nickname,headimgurl';
        $USR=self::rowSelect(USR,$selesct,$whereArray);
        if(!$USR){ exit('$USR fail!'); }

        $selesct='mobile';
        $USR_INFO=self::rowSelect(USR_INFO,$selesct,$whereArray);
        if(!$USR_INFO){ exit('$USR fail!'); }

        $data=new \stdClass();
        $data->uid=$USR->uid;
        $data->nickname=$USR->nickname;
        $data->headimgurl=$USR->headimgurl;
        $data->mobile=$USR_INFO->mobile;

        return $data;
    }


} 