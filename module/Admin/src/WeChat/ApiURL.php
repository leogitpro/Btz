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
     * 接口地址: 创建个性化菜单
     *
     * @param string $access_token
     * @return string
     */
    public static function GetMenuCreateConditionalUrl($access_token)
    {
        $path = 'cgi-bin/menu/addconditional?access_token=' . (string)$access_token;
        return self::GetHost() . $path;
    }


    /**
     * 接口地址: 删除个性化菜单
     *
     * @param string $access_token
     * @return string
     */
    public static function GetMenuDeleteConditionalUrl($access_token)
    {
        $path = 'cgi-bin/menu/delconditional?access_token=' . (string)$access_token;
        return self::GetHost() . $path;
    }


    /**
     * 接口地址: 创建自定义菜单
     *
     * @param string $access_token
     * @return string
     */
    public static function GetMenuCreateDefaultUrl($access_token)
    {
        $path = 'cgi-bin/menu/create?access_token=' . (string)$access_token;
        return self::GetHost() . $path;
    }


    /**
     * 接口地址: 删除自定义菜单
     *
     * @param string $access_token
     * @return string
     */
    public static function GetMenuDeleteDefaultUrl($access_token)
    {
        $path = 'cgi-bin/menu/delete?access_token=' . (string)$access_token;
        return self::GetHost() . $path;
    }


    /**
     * 接口地址: 用户标签
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
     * 接口地址: 微信生成二维码
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
     * 接口地址: 微信服务器 IP 地址
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
     * 接口地址: AccessToken
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