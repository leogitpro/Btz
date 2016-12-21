<?php
/**
 * Plugin for controller get hostname and protocol
 *
 * User: leo
 */


namespace Application\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class HostPlugin extends AbstractPlugin
{


    /**
     * Get the host with protocol
     *
     * @return string
     */
    public function getHost() {
        $hostname = $this->hostname();
        if (empty($hostname)) {
            return '';
        }
        return 'http' . ($this->isHttps() ? 's' : '') . '://' . $hostname;
    }


    /**
    function __invoke($protocol = null)
    {
        if (null === $protocol) {
            return $this;
        }

        $hostname = $this->hostname();
        if (empty($hostname)) {
            return '';
        }

        if (!$protocol) {
            return $hostname;
        }

        return 'http' . ($this->isHttps() ? 's' : '') . '://' . $hostname;
    }
    //*/


    /**
     * Check current protocol is https
     *
     * @return bool
     */
    public function isHttps()
    {

        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https') === 0) {
            return true;
        } else {
            if(isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'on') === 0 || $_SERVER['HTTPS'] == 1)) {
                return true;
            }
        }

        return false;
    }


    /**
     * Get current hostname
     *
     * @return string
     */
    public function hostname()
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            return $_SERVER['HTTP_X_FORWARDED_HOST'];
        } else {
            if(isset($_SERVER['HTTP_HOST'])) {
                return $_SERVER['HTTP_HOST'];
            }
        }

        return '';
    }

}