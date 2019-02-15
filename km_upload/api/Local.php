<?php
/**
 * Created by PhpStorm.
 * User: kumaomao
 * Date: 2019/1/25
 * Time: 15:09
 */

namespace km_upload\api;


use app\lib\exception\ParameterException;

class Local implements Upload
{
    private static $path;

    public static function init($Oss)
    {
        self::$path = $Oss['path'];
    }

    public static function uploadFile($Path, $object=false, $bucket=false)
    {
            $info = $Path->move( '../public'. self::$path);
            if($info){
                // 成功上传后 获取上传信息
                $imgurl =   self::$path."/".$info->getSaveName();

            }else{
                // 上传失败获取错误信息

                throw new ParameterException([
                    'msg'=>$Path->getError()
                ]);
            }
        return $imgurl;
    }

}