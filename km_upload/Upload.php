<?php
/**
 * Created by PhpStorm.
 * User: kumaomao
 * Date: 2019/1/25
 * Time: 14:14
 */
namespace km_upload;
use app\lib\exception\ParameterException;
use think\Image;


class Upload
{
    private static $rule=[];
    private static $Oss=[
        'aliyun'=>'\km_upload\api\AliOss',
        'baidu_img'=>'\km_upload\api\BaiDuImg',
        'sina_img'=>'\km_upload\api\SinaImg',
        'local'=>'\km_upload\api\Local'
    ];
    private static $type;
    private static $oss_name;
    private $file_info;

    public function __construct($oss_name=false,$oss_obj=false){
        if(!$oss_name){
            $oss_name='local';
        }
        self::$oss_name=$oss_name;
        if(!$oss_obj){
            $config = include_once dirname(__file__).'/config.php';
            $oss_obj=$config[$oss_name];
        }

        $OssObject = self::$Oss[$oss_name];
        $OssObject::init($oss_obj);
        self::$type = $oss_name;

    }

    public function rule($rule=[]){
        self::$rule=$rule;

        return $this;
    }

    private  function uploadFile($Path,$object=false,$bucket=false){
        $Oss=self::$Oss[self::$type];
        $result = $Oss::uploadFile($Path,$object,$bucket);
        return $result;
    }


    public  function upload($bucket=false){
        $file = request()->file();
        if (!empty($file)) {
            foreach($file as $val) {
                $this->chaeck_uplaod($val);
                if(self::$oss_name=='local'){
                    $url= $this->uploadFile($val);
                    $from=1;
                }else{
                    //获取图片地址
                    $resResult = Image::open($val->getInfo('tmp_name'));
                    //这里是有sha1加密 生成文件名 之后连接上后缀
                    $fileName = sha1(date('YmdHis', time()) . uniqid()) . '.' . $resResult->type();
                    $url= $this->uploadFile($val->getInfo('tmp_name'),$fileName,$bucket);
                    $from = 2;
                }
                $result = $this->file_info;
                $result['url']=$url;
                $result['from']=$from;
                return $result;
            }
        }else{
            return false;
        }
    }

    private function chaeck_uplaod($file){
        //获取文件信息
        $info = $file->getInfo();
        $type = explode('/',$info['type']);
        $new_info = [
            'size'=>$info['size'],
            'extension'=>$type[1],
            'type'=>$type[0]
        ];
        $this->file_info = $new_info;

        //验证数据
        if(self::$rule){
            $rule = self::$rule;
            if(isset($rule['size'])){
                if($info['size']>$rule['size']){
                    throw new ParameterException([
                        'msg'=>'文件过大'
                    ]);
                }
            }
            if(isset($rule['ext'])){
                if (is_string($rule['ext'])) {
                    $rule['ext'] = explode(',', $rule['ext']);
                }
                if (!in_array(strtolower($type[1]), $rule['ext'])) {
                    throw new ParameterException([
                        'msg'=>'文件非法'
                    ]);
                }
            }
            if(isset($rule['type'])){
                if (is_string($rule['type'])) {
                    $rule['ext'] = explode(',', $rule['type']);
                }
                if (!in_array(strtolower($info['type']), $rule['type'])) {
                    throw new ParameterException([
                        'msg'=>'文件非法'
                    ]);
                }
            }

        }


    }

    /**
     * @param string $url post请求地址
     * @param array $params
     * @return mixed
     */
    //参数1：访问的URL，参数2：post数据(不填则为GET)数组，参数3：提交的$cookies,参数4：是否返回$cookies
    public static function curl_request($url,$post='',$headers=[],$cookie='', $returnCookie=0){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        // curl_setopt($curl, CURLOPT_REFERER, "http://XXX");
        if($post) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        }
        if($cookie) {
            curl_setopt($curl, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        if($returnCookie){
            list($header, $body) = explode("\r\n\r\n", $data, 2);
            preg_match_all("/Set\-Cookie:([^;]*);/", $body, $matches);
            // preg_match_all("/SUB=.*?;/", $header, $matches);
            //$info['cookie']  = substr($matches[1][0], 1);
            $info['cookie']  = $matches[0];
            $info['content'] = $body;
            return $info;
        }else{
            return $data;
        }
    }
}