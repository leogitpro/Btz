<?php
/**
 * Simple async request plugin
 * Need logger plugin support
 *
 * The response script need notice follow settings
 * ignore_user_abort(true);
 * set_time_limit(0);
 *
 * In Controller usage:
 * $this->getAsyncRequestPlugin()->get($url);
 * $this->getAsyncRequestPlugin($url);
 * $this->getAsyncRequestPlugin()->post($url, $post);
 * $this->getAsyncRequestPlugin($url, $post);
 *
 * If web server is nginx, the fastcgi need setting ignore_user_abort on
 *
 * User: leo
 */

namespace Application\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class AsyncRequestPlugin extends AbstractPlugin
{

    /**
     * Simple async get request
     *
     * @param string $url
     * @param null $cookie
     * @return bool
     */
    public function get($url, $cookie = null)
    {
        return $this->request($url, 'GET', [], $cookie);
    }



    /**
     * Simple async post request
     *
     * @param string $url
     * @param array $params
     * @param string $cookie
     * @return bool
     */
    public function post($url, $params = array(), $cookie = null)
    {
        return $this->request($url, 'POST', $params, $cookie);

    }


    public function __invoke($url = null, $params = array(), $cookie = null)
    {
        if (null == $url) {
            return $this;
        }

        if (empty($params)) {
            return $this->get($url, $cookie);
        }

        return $this->post($url, $params, $cookie);
    }


    /**
     * Quick send a request
     *
     * @param string $url
     * @param string $method
     * @param array $params
     * @param null $cookie
     * @return bool
     */
    private function request($url, $method = 'GET', $params = array(), $cookie = null)
    {
        if (!function_exists("fsockopen")) {
            $this->getController()->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . "Need open fsockopen function");
            return false;
        }

        $urlInfo = parse_url($url);

        isset($urlInfo['host']) || $urlInfo['host'] = '';
        isset($urlInfo['path']) || $urlInfo['path'] = '';
        isset($urlInfo['query']) || $urlInfo['query'] = '';
        isset($urlInfo['port']) || $urlInfo['port'] = '';

        $query = empty($urlInfo['query']) ? '' : '?' . $urlInfo['query'];
        $path = empty($urlInfo['path']) ? '/' : $urlInfo['path'] . $query;

        $host = $urlInfo['host'];
        if ('https' == $urlInfo['scheme']) {
            $port = empty($urlInfo['port']) ? 443 : $urlInfo['port'];
            $host = 'ssl://' . $host;
        } else {
            $port = empty($urlInfo['port']) ? 80 : $urlInfo['port'];
        }

        $postFields = '';
        $headers = [];

        if ('GET' == $method) {
            $headers[] = 'GET ' . $path . ' HTTP/1.1';
        } else {
            if (is_array($params) && !empty($params)) {
                $postFields = http_build_query($params);
            }

            $headers[] = 'POST ' . $path . ' HTTP/1.1';
            $headers[] = 'Content-type: application/x-www-form-urlencoded';
            $headers[] = 'Content-Length: ' . strlen($postFields);
            $headers[] = 'Cache-Control: no-cache';
        }

        $headers[] = 'Host: ' . $urlInfo['host'];
        $headers[] = 'Connection: Close';
        $headers[] = 'User-Agent:' . $_SERVER['HTTP_USER_AGENT'];
        $headers[] = 'Accept: */*';

        if (!empty($cookie)) {
            $headers[] = 'Cookie: ' . $cookie;
        }

        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            $this->getController()->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . "fsockopen failure open host: " . $host . ':' . $port . PHP_EOL . $errno . ':' . $errstr);
            return false;
        }

        $end = "\r\n";
        $out = implode($end, $headers) . $end . $end . $postFields;

        fwrite($fp, $out);
        fclose($fp);

        $this->getController()->getLoggerPlugin()->debug(__METHOD__ . PHP_EOL . 'Async requested: ' . $url);

        return true;
    }

}