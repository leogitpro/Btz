<?php
/**
 * LoggerService.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Logger\Service;

use Traversable;
use Zend\Log\Exception\InvalidArgumentException;
use Zend\Log\Exception\RuntimeException;
use Zend\Log\Logger;
use Zend\Log\LoggerInterface;


class LoggerService implements LoggerInterface
{

    /**
     * @var Logger
     */
    private $_logger;

    public function __construct(Logger $logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->_logger;
    }


    /**
     * @param mixed $data
     * @param int $priority
     */
    public function mixed($data, $priority = Logger::DEBUG)
    {
        $message = null;
        if (is_scalar($data)) {
            $message = $data;
        } else if(is_array($data)) {
            foreach($data as $k => $v) {
                $message .= $k . ':' . (string)$v . PHP_EOL;
            }
        } else if($data instanceof \stdClass) {
            $message = json_encode($data, JSON_UNESCAPED_UNICODE);
        } else {
            $message = gettype($data);
        }

        $this->log($priority, $message);
    }


    /**
     * @param \Exception $e
     * @return LoggerInterface
     */
    public function exception($e)
    {
        $message = $e->getMessage() . PHP_EOL;
        $message .= 'Code: ' . $e->getCode() . PHP_EOL;
        $message .= 'Line: ' . $e->getLine() . PHP_EOL;
        $message .= 'File: ' . $e->getFile();

        return $this->getLogger()->err($message);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function emerg($message, $extra = [])
    {
        return $this->getLogger()->emerg($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function alert($message, $extra = [])
    {
        return $this->getLogger()->alert($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function crit($message, $extra = [])
    {
        return $this->getLogger()->crit($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function err($message, $extra = [])
    {
        return $this->getLogger()->err($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function warn($message, $extra = [])
    {
        return $this->getLogger()->warn($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function notice($message, $extra = [])
    {
        return $this->getLogger()->notice($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function info($message, $extra = [])
    {
        return $this->getLogger()->info($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function debug($message, $extra = [])
    {
        return $this->getLogger()->debug($message, $extra);
    }


    /**
     * Add a message as a log entry
     *
     * @param  int $priority
     * @param  mixed $message
     * @param  array|Traversable $extra
     * @return LoggerService
     */
    public function log($priority, $message, $extra = [])
    {
        try {
            $this->getLogger()->log($priority, $message, $extra);
        } catch (InvalidArgumentException $e) {
            //todo
        } catch (RuntimeException $e) {
            //todo
        } finally {
            //todo
        }

        return $this;
    }


}