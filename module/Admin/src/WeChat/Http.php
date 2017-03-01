<?php
/**
 * Http.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\WeChat;


use Admin\WeChat\Exception\NetworkRequestException;
use Curl\Curl;


class Http
{

    /**
     * Send Http GET Request
     *
     * @param string $url
     * @return string
     * @throws NetworkRequestException
     */
    public static function Get($url)
    {
        $curl = new Curl();
        $curl->get($url);
        if ($curl->error) {
            throw new NetworkRequestException($curl->error_message, $curl->error_code);
        } else {
            return $curl->response;
        }
    }


    /**
     * Send Http POST Request
     *
     * @param string $url
     * @param string|array $data
     * @return string
     * @throws NetworkRequestException
     */
    public static function Post($url, $data)
    {
        $curl = new Curl();
        $curl->post($url, $data);
        if ($curl->error) {
            throw new NetworkRequestException($curl->error_message, $curl->error_code);
        } else {
            return $curl->response;
        }
    }

}