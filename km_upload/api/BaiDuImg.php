<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/27
 * Time: 14:30
 */

namespace km_upload\api;
use km_upload\Upload as _Upload;
//百度图床,利用百度识图实现
class BaiDuImg implements Upload
{
    private static $url;

    public static function init($Oss)
    {
        self::$url = $Oss['url'];
    }

    public static function uploadFile($Path,$object=false, $bucket=false)
    {
        // POST 文件
        if (class_exists('CURLFile')) {     // php 5.5
            $post['image'] = new \CURLFile(realpath($Path));
        } else {
            $post['image'] = '@'.realpath($Path);
        }
       $result =  _Upload::curl_request(self::$url,$post);
        if($result){
            $result=json_decode($result, true);
            //dump($result);exit;
            if(isset($result['data']['sign'])){
                return 'https://image.baidu.com/search/down?tn=download&url=https://graph.baidu.com/resource/'.$result['data']['sign'].'.png';
            }
        }
        return false;

    }
}