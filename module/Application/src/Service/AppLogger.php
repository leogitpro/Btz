<?php
/**
 * AppLogger.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */

namespace Application\Service;



use Traversable;
use Zend\Log\Exception\InvalidArgumentException;
use Zend\Log\Exception\RuntimeException;
use Zend\Log\Logger;
use Zend\Log\LoggerInterface;


class AppLogger implements LoggerInterface
{
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
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function emerg($message, $extra = [])
    {
        $this->getLogger()->emerg($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function alert($message, $extra = [])
    {
        $this->getLogger()->alert($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function crit($message, $extra = [])
    {
        $this->getLogger()->crit($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function err($message, $extra = [])
    {
        $this->getLogger()->err($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function warn($message, $extra = [])
    {
        $this->getLogger()->warn($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function notice($message, $extra = [])
    {
        $this->getLogger()->notice($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function info($message, $extra = [])
    {
        $this->getLogger()->info($message, $extra);
    }

    /**
     * @param string $message
     * @param array|Traversable $extra
     * @return LoggerInterface
     */
    public function debug($message, $extra = [])
    {
        $this->getLogger()->debug($message, $extra);
    }


    /**
     * Add a message as a log entry
     *
     * @param  int $priority
     * @param  mixed $message
     * @param  array|Traversable $extra
     * @return AppLogger
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