<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/27
 * Time: 9:54
 */
namespace km_upload\api;

use OSS\OssClient;
use OSS\Core\OssException;
//阿里云对象存储
class AliOss implements Upload
{
    private static $Oss;
    private static $config;

    //初始化
    public static function init($Oss)
    {

        self::$config = $Oss;
        //实例化OSS
        $oss=new OssClient($Oss['KeyId'],$Oss['KeySecret'],$Oss['Endpoint']);
        self::$Oss = $oss;
        return $oss;
    }

    /**
     * @param $object 上传文件名
     * @param $Path 文件本地路径
     * @param bool $bucket 存储空间名称
     */
    public static function uploadFile($Path,$object=false,$bucket=false)
    {
        //try 要执行的代码,如果代码执行过程中某一条语句发生异常,则程序直接跳转到CATCH块中,由$e收集错误信息和显示
        try{
            $ossClient = self::$Oss;

            //uploadFile的上传方法
            if(!$bucket){
                $bucket=self::$config['Bucket'];
            }

            $res=$ossClient->uploadFile($bucket, $object, $Path);
        } catch(OssException $e) {
            //如果出错这里返回报错信息
            return $e->getMessage();
        }
        //否则，完成上传操作
        return $res['info']['url'];
    }


}