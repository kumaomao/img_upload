<?php
/**
 * Created by PhpStorm.
 * User: kumaomao
 * Date: 2019/1/25
 * Time: 14:17
 */
namespace km_upload\api;
interface Upload
{
    public static function init($Oss);
    public static function uploadFile($Path,$object,$bucket);

}