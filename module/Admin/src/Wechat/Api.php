<?php
/**
 * Api.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat;


class Api
{

    const SCHEMA = 'https';
    const DOMAIN = 'api.weixin.qq.com';


    /**
     * 获取微信服务器 IP 地址的 API 地址
     *
     * @param string $access_token
     * @return string
     */
    public static function GetWechatCallbackHosts($access_token)
    {
        $path = 'cgi-bin/getcallbackip?access_token=' . $access_token ;
        return self::GetHost() . $path;
    }


    /**
     * 获取微信基础 Access Token API 地址
     *
     * @param string $appid
     * @param string $appsecret
     * @param string $grant_type
     * @return string
     */
    public static function GetAccessTokenUrl($appid, $appsecret, $grant_type = 'client_credential')
    {
        $path = 'cgi-bin/token?grant_type=' . $grant_type . '&appid=' . $appid . '&secret=' . $appsecret;
        return self::GetHost() . $path;
    }


    /**
     * 微信 API 基础域名
     *
     * @return string
     */
    private static function GetHost()
    {
        return self::SCHEMA . '://' . self::DOMAIN . '/';
    }

}