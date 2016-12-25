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
     * @param $url
     * @return bool
     */
    public function get($url)
    {
        if (!function_exists("fsockopen")) {
            $this->getController()->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . "Need open fsockopen function");
            return false;
        }

        $urlInfo = parse_url($url);

        $host = $urlInfo['host'];
        $port = empty($urlInfo['port']) ? 80 : $urlInfo['port'];
        $path = $urlInfo['path'];

        $errno = 0;
        $errstr = '';

        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            $this->getController()->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . "fsockopen failure open host: " . $host . ':' . $port . PHP_EOL . $errno . ':' . $errstr);
            return false;
        }

        $end = "\r\n";
        $out = "GET " . $path . " HTTP/1.1" . $end ;
        $out .= "Host: " . $host . $end;
        $out .= "Connection: Close" . $end . $end;

        fwrite($fp, $out);
        fclose($fp);

        $this->getController()->getLoggerPlugin()->debug(__METHOD__ . PHP_EOL . 'Async get requested:' . $url);

        return true;
    }


    /**
     * Simple async post request
     *
     * @param $url
     * @param array $params
     * @return bool
     */
    public function post($url, $params = array())
    {

        if (!function_exists("fsockopen")) {
            $this->getController()->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . "Need open fsockopen function");
            return false;
        }

        $urlInfo = parse_url($url);

        //$scheme = empty($urlInfo['scheme']) ? 'http' : $urlInfo['scheme'];
        $host = $urlInfo['host'];
        $port = empty($urlInfo['port']) ? 80 : $urlInfo['port'];
        $path = $urlInfo['path'];
        $query = empty($params) ? '' : http_build_query($params);

        foreach($params as $k => $v) {
            $this->getController()->getLoggerPlugin()->debug("post data key:" . $k . ' => ' . $v);
        }

        $errno = 0;
        $errstr = '';

        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            $this->getController()->getLoggerPlugin()->err(__METHOD__ . PHP_EOL . "fsockopen failure open host: " . $host . ':' . $port . PHP_EOL . $errno . ':' . $errstr);
            return false;
        }

        $end = "\r\n";
        $out = "POST " . $path . " HTTP/1.1" . $end ;
        $out .= "Host: " . $host . $end;
        $out .= "Content-length: " . strlen($query) . $end;
        $out .= "Content-type: application/x-www-form-urlencoded" . $end;
        $out .= "Connection: Close" . $end . $end;
        $out .= $query;

        fwrite($fp, $out);
        fclose($fp);

        $this->getController()->getLoggerPlugin()->debug(__METHOD__ . PHP_EOL . 'Async post requested:' . $url);

        return true;
    }



    function __invoke($url = null, $params = array())
    {
        if (null == $url) {
            return $this;
        }

        if (empty($params)) {
            return $this->get($url);
        }

        return $this->post($url, $params);
    }

}