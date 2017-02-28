<?php
/**
 * Http.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\Wechat;


use Application\Service\AppLogger;
use Curl\Curl;

class Http
{
    /**
     * @var AppLogger
     */
    private $logger;

    public function __construct(AppLogger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param string $url
     * @return bool|string
     */
    public function get($url)
    {
        $curl = new Curl();
        $curl->get($url);
        if ($curl->error) {
            $this->logger->err('Http error:[ ' . $curl->error_code . ' ]' . $curl->error_message);
            return false;
        } else {
            $this->logger->debug('Http response:' . PHP_EOL . $curl->response);
            return $curl->response;
        }
    }


    /**
     * @param string $url
     * @param string|array $data
     * @return bool|string
     */
    public function post($url, $data)
    {
        $curl = new Curl();
        $curl->post($url, $data);
        if ($curl->error) {
            $this->logger->err('Http error:[ ' . $curl->error_code . ' ]' . $curl->error_message);
            return false;
        } else {
            $this->logger->debug('Http response:' . PHP_EOL . $curl->response);
            return $curl->response;
        }
    }

}