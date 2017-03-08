<?php
/**
 * ApiURL.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat;


class ApiURL
{

    const SCHEMA = 'https';
    const DOMAIN = 'api.weixin.qq.com';

    /**
     * 微信 API 基础域名
     *
     * @return string
     */
    private static function GetHost()
    {
        return self::SCHEMA . '://' . self::DOMAIN . '/';
    }


    /**
     *
     * @param string $access_token
     * @return string
     */
    public static function GetTagUrl($access_token)
    {
        $path = 'cgi-bin/tags/get?access_token=' . (string)$access_token;
        return self::GetHost() . $path;
    }


    /**
     * 获取微信生成二维码接口地址
     *
     * @param string $access_token
     * @return string
     */
    public static function GetCreateQrCodeUrl($access_token)
    {
        $path = 'cgi-bin/qrcode/create?access_token=' . (string)$access_token;
        return self::GetHost() . $path;
    }


    /**
     * 获取微信服务器 IP 地址的 API 地址
     *
     * @param string $access_token
     * @return string
     */
    public static function GetWechatCallbackHosts($access_token)
    {
        $path = 'cgi-bin/getcallbackip?access_token=' . (string)$access_token ;
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
        $path = 'cgi-bin/token?grant_type=' . (string)$grant_type . '&appid=' . (string)$appid . '&secret=' . (string)$appsecret;
        return self::GetHost() . $path;
    }

}