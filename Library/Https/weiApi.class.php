<?php
/* Created by User: soma Worker: 陈鸿扬 Date: 2016/6/2 Time: 11:21 */
namespace Https;
use Commons\tool as tool;
use Https\authApi;

class weiApi extends authApi
{
    function __construct(){
        new parent;

    }

    //获取用户详细信息
    static function usrInfo($openid = '',$access_token = ''){
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
        $data = self::http($url);
        if ($data){
            $data = json_decode($data);
        }
        return $data;
    }

    //用户在关注了公众号之后获取其nickname、headimgurl等信息
    static function subscribe($openid = '',$globeAccessToken=""){
        //return $globl_access_token;
        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$globeAccessToken."&openid=".$openid."&lang=zh_CN";
        $data = self::http($url);
        if($data){
            $data = json_decode($data);
        }
        return $data;
    }

    //组装用户信息
    static function usrDataMake($data,$data_g){

        //if(!isset($data->nickname)){ exit("api userInfo fail"); }

        $info=new \stdClass();
        $info->subscribe= isset($data_g->subscribe)?$data_g->subscribe:'0';

        $nick= isset($data->nickname)?$data->nickname:'TA';
        $unified = emoji_softbank_to_unified($nick);
        $nickname=emoji_unified_to_html($unified);
        $nickname=tool::filter_mark( strip_tags($nickname) );
        $nickname=mb_substr($nickname,0,20,'utf-8');
        $info->nickname =$nickname;

        $info->sex = isset($data->sex)?$data->sex:'';
        $info->language = isset($data->language)?$data->language:'';
        $info->city= isset($data->city)?$data->city:'';
        $info->province = isset($data->province)?$data->province:'';
        $info->country = isset($data->country)?$data->country:'';
        $info->headimgurl = isset($data->headimgurl)?$data->headimgurl:'/Upload/avatar/20161111/avatar_nomal.png';
        $info->time = time();
        $info->ip = tool::get_ip();

        return $info;


    }


    //获限图片文件并保存在服务器
    static function getMedia($media_id = "",$openid = "",$globeAccessToken=""){
        if(!$openid){ exit('!$openid'); }
        if(!$media_id){ exit('!$media_id'); }
        if(!$globeAccessToken ){ exit('!$globeAccessToken'); }

        $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=$globeAccessToken&media_id=$media_id";
        $data = self::http($url);

        //exit($data);

        if($data){

            if(json_decode($data)){

                exit('fail');

            }else{

                $filename = $openid.'_'.date("YmdHis",TIME).".jpg";
                $fpath = PUBLIC_FILE."/Photos/".$filename;
                $rs = @file_put_contents($fpath,$data);

                if($rs && @chmod($fpath,0660)){
                    return ($filename);
                }

            }
        }
        return $data;
    }



}