<?php
/**
 * Simple async request plugin
 * Need logger plugin support
 *
 * The response script need notice follow settings
 * ignore_user_abort(true);
 * set_time_limit(0);
 *
 * If web server is nginx, the fastcgi need setting ignore_user_abort on
 *
 * User: leo
 */

namespace Application\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class AsyncRequestPlugin extends AbstractPlugin
{

    function __invoke($url, $params = array())
    {

        if (!function_exists("fsockopen")) {
            $this->getController()->logger()->err(__METHOD__ . PHP_EOL . "Need open fsockopen function");
            return false;
            //throw new \Exception('Need fsockopen support.');
        }

        $urlInfo = parse_url($url);

        //$scheme = empty($urlInfo['scheme']) ? 'http' : $urlInfo['scheme'];
        $host = $urlInfo['host'];
        $port = empty($urlInfo['port']) ? 80 : $urlInfo['port'];
        $path = $urlInfo['path'];
        $query = empty($params) ? '' : http_build_query($params);

        $errno = 0;
        $errstr = '';

        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            $this->getController()->logger()->err(__METHOD__ . PHP_EOL . "fsockopen failure open host: " . $host . ':' . $port . PHP_EOL . $errno . ':' . $errstr);
            return false;
        }

        $end = "\r\n";

        $out = "POST " . $path . " HTTP/1.1" . $end ;
        $out .= "Host:" . $host . $end;
        $out .= "Content-length: " . strlen($query) . $end;
        $out .= "Content-type: application/x-www-form-urlencoded" . $end;
        $out .= "Connection: close" . $end . $end;
        $out .= $query;

        fputs($fp, $out);
        fclose($fp);

        $this->getController()->logger()->debug(__METHOD__ . PHP_EOL . 'Async request:' . $url);

        return true;
    }

}