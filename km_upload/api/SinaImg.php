<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/27
 * Time: 15:13
 */

namespace km_upload\api;
use km_upload\Upload as _Upload;
use think\facade\Cache;

//新浪图床


class SinaImg implements Upload
{
    private static $Sina;
    private static $cookie;
    public static function init($Oss)
    {
        self::$Sina = $Oss;

        $cookie = Cache::get('sina_cookie');
        if(!$cookie){
            $cookie = self::SinaLogin();
        }
        self::$cookie = $cookie;
    }

    public static function uploadFile($Path,$object=false, $bucket=false)
    {
        $isUrl = preg_match('/http:\/\/[\w.]+[\w\/]*[\w.]*\??[\w=&\+\%]*/is',$Path)?true:false;
        $url = self::$Sina['uploadUrl'];
        if($isUrl){
            $url.=self::$Sina['pathImg'];
            // POST 文件
            if (class_exists('CURLFile')) {     // php 5.5
                $post['pic1'] = new \CURLFile(realpath($Path));
            } else {
                $post['pic1'] = '@'.realpath($Path);
            }
        }else{
            $post['b64_data'] = base64_encode(file_get_contents($Path));
        }

        $result = _Upload::curl_request($url,$post,['Cookie:'.self::$cookie]);
        $result =  self::getImgUrl($result);
        if(!$result){
            //失败后从新登陆一次
            self::SinaLogin();
        }
        return $result;
    }


    private static function getImgUrl($str){
        $pieces = explode('</script>', $str);
        $json_str = $pieces[1];
        $res=json_decode($json_str, true);
        return isset($res['data']['pics']['pic_1']['pid'])?'https://ws3.sinaimg.cn/large/'.$res['data']['pics']['pic_1']['pid'].'.jpg':false;
        //图片显示大小
       // array('large', 'mw1024', 'mw690', 'bmiddle', 'small', 'thumb180', 'thumbnail', 'square');
    }




    private static function SinaLogin(){
        $url = self::$Sina['loginUrl'];
        $post = [
            'entry'=>'sso',
            'gateway'=>'1',
            'from'=>'null',
            'savestate'=>'303',
            'useticket'=>'0',
            'pagerefer'=>'',
            'vsnf'=>'1',
            'su'=>self::$Sina['username'],
            'service'=>'sso',
            'sp'=>self::$Sina['password'],
            'sr'=>'1920*1080',
            'encoding'=>'UTF-8',
            'cdult'=>'3',
            'domain'=>'sina.com.cn',
            'prelt'=>'0',
            'returntype'=>'TEXT'
        ];
        $result = _Upload::curl_request($url,$post,[],[],true);
        $cookie = $result['cookie'][2];
        $cookie= str_replace('Set-Cookie: ',"",$cookie);
        //设置缓存
        Cache::set('sina_cookie',$cookie,432000);
        return $cookie;
    }

}