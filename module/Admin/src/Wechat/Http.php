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


    public function get($url)
    {
        $curl = new Curl();
        $curl->get($url);
        if ($curl->error) {
            $this->logger->err('Http error:[ ' . $curl->error_code . ' ]' . $curl->error_message);
            return false;
        } else {
            $this->logger->info('Http response:' . PHP_EOL . $curl->response);
            return $curl->response;
        }
    }


    public function post($url, $data)
    {
        //todo
    }

}