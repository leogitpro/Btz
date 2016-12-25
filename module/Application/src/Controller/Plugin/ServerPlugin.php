<?php
/**
 * Server information access plugin
 *
 * User: leo
 */

namespace Application\Controller\Plugin;


use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ServerPlugin extends AbstractPlugin
{

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
     * Get the current server domain
     *
     * @param bool $scheme
     * @param bool $port
     * @return string
     */
    public function domain($scheme = true, $port = true) {

        $hostname = $this->hostname();
        if (empty($hostname)) {
            return '';
        }

        if ($port) {
            $_port = (int)$_SERVER['SERVER_PORT'];
            if ($_port && 80 != $_port) {
                $hostname .= ':' . $_port;
            }
        }

        if (!$scheme) {
            return $hostname;
        }

        return 'http' . ($this->isHttps() ? 's' : '') . '://' . $hostname;
    }

}