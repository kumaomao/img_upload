<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/27
 * Time: 9:55
 */
return [
    //阿里云OSS配置
    'aliyun' => [
        'KeyId'      => '',  //您的Access Key ID
        'KeySecret'  => '',  //您的Access Key Secret
        'Endpoint'   => 'oss-cn-beijing.aliyuncs.com',  //阿里云oss 外网地址endpoint
        'Bucket'     => '',  //Bucket名称
    ],
    //百度图床
    'baidu_img'=>[
        'url'=>'http://graph.baidu.com/upload?tn=pc&from=pc&image_source=PC_UPLOAD_IMAGE_FILE&range={%22page_from%22:%20%22shituIndex%22}&extUiData%5bisLogoShow%5d=1&uptime='.time(),
    ],
    'sina_img'=>[
        'loginUrl'=>'https://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.15)&_=1403138799543',
        'uploadUrl'=>'http://picupload.service.weibo.com/interface/pic_upload.php?mime=image%2Fjpeg&data=base64&url=0&markpos=1&logo=&nick=0&marks=1&app=miniblog',
        'pathImg'=>'&cb=http://weibo.com/aj/static/upimgback.html?_wv=5&callback=STK_ijax_'.time(),
        'username'=>base64_encode(''),
        'password'=>''
    ],
    'local'=>[
        'path'=>'/static/upload'
    ]

];