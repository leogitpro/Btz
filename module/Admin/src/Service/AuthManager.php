<?php
/**
 * Admin module auth
 *
 * User: leo
 */

namespace Admin\Service;


use Zend\Authentication\Result;
use Zend\Log\Logger;
use Zend\Session\SessionManager;

class AuthManager
{

    /**
     * @var SessionManager
     */
    private $sessionManager;

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var Logger
     */
    private $logger;


    public function __construct(AuthService $authService, SessionManager $sessionManager, Logger $logger)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
    }


    /**
     * Validate administrator login
     *
     * @param string $email
     * @param string $password
     * @return Result
     * @throws \Exception
     */
    public function login($email, $password)
    {
        // Check if administrator has already logged in. If so, do not allow to log in twice.
        if(null != $this->authService->getIdentity()) {
            $this->logger->err(__METHOD__ . PHP_EOL . 'Administrator['. $email .'] has login. no need login again');
            throw new \Exception('Already logged in');
        }

        // Authentication with login/password
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setAccount($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        return $result;
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