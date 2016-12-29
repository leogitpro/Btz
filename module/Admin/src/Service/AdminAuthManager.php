<?php
/**
 * Admin module auth
 *
 * User: leo
 */

namespace Admin\Service;


use Zend\Log\Logger;
use Zend\Session\SessionManager;

class AdminAuthManager
{

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var AdminAuthService
     */
    private $authService;

    /**
     * @var Logger
     */
    private $logger;


    public function __construct(AdminAuthService $authService, SessionManager $sessionManager, Logger $logger)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
    }



    public function login($account, $password, $remember_me)
    {
        //todo
    }


    /**
     * Performs administrator logout
     */
    public function logout()
    {
        // Allow to log out only when administrator is logged in.
        if (null != $this->authService->getIdentity()) {
            // Remove identity from session
            $this->authService->clearIdentity();
            $this->logger->debug(__METHOD__ . PHP_EOL . 'Cleaned administrator login identity!');
        }
    }


    /**
     * Administrator access control layer
     *
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function access($controller, $action)
    {
        return false;
    }

}