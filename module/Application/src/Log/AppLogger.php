<?php

namespace Application\Log;


use Traversable;
use Zend\Log\LoggerInterface;
use Zend\Log\Logger;

class AppLogger implements LoggerInterface
{

    /**
     * @var Zend\Log\Logger
     */
    private $logger;


    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * Get the logger instance
     *
     * @return Zend\Log\Logger|Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }


    /**
     * emerg: 0
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function emerg($message, $extra = [])
    {
        $this->getLogger()->emerg($message, $extra);
    }

    /**
     * alert: 1
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function alert($message, $extra = [])
    {
        $this->getLogger()->alert($message, $extra);
    }

    /**
     * crit: 2
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function crit($message, $extra = [])
    {
        $this->getLogger()->crit($message, $extra);
    }

    /**
     * err: 3
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function err($message, $extra = [])
    {
        $this->getLogger()->err($message, $extra);
    }

    /**
     * warn: 4
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function warn($message, $extra = [])
    {
        $this->getLogger()->warn($message, $extra);
    }

    /**
     * notice: 5
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function notice($message, $extra = [])
    {
        $this->getLogger()->notice($message, $extra);
    }

    /**
     * info: 6
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function info($message, $extra = [])
    {
        $this->getLogger()->info($message, $extra);
    }

    /**
     * debug: 7
     *
     * @param string $message
     * @param array|Traversable $extra
     * @return mixed
     */
    public function debug($message, $extra = [])
    {
        $this->getLogger()->debug($message, $extra);
    }


    /**
     * @param string $name
     * @param array $arguments
     */
    function __call($name, $arguments)
    {
        if (method_exists($this->getLogger(), $name)) {
            call_user_func_array(array($this->getLogger(), $name), $arguments);
        }
    }

    /**
     * need more testing...
     *
     * @param string $name
     * @param array $arguments
     */
    public static function __callStatic($name, $arguments)
    {
        forward_static_call_array(array(Logger::class, $name), $arguments);
    }


}