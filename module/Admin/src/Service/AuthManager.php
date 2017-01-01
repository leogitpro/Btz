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
     */
    public function login($email, $password)
    {
        if($this->authService->hasIdentity()) {
            $this->logger->err(__METHOD__ . PHP_EOL . 'Member['. $email .'] has login. no need login again');
            return false;
        }

        // Authentication with login/password
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        return $this->authService->authenticate();
    }


    /**
     * Performs administrator logout
     */
    public function logout()
    {
        if ($this->authService->hasIdentity()) {
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