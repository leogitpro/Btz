<?php
/**
 * AuthManager
 *
 * User: leo
 */

namespace User\Service;


use Zend\Log\Logger;
use Zend\Session\SessionManager;
use Zend\Authentication\Result;

class AuthManager
{

    /**
     * @var AuthService
     */
    private $authService;


    /**
     * @var SessionManager
     */
    private $sessionManager;


    /**
     * @var Logger
     */
    private $logger;


    /**
     * Public actions list
     *
     * @var array
     */
    private $config;


    public function __construct(AuthService $authService, SessionManager $sessionManager, Logger $logger, $config)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->logger = $logger;
        $this->config = $config;
    }


    /**
     * @param string $email
     * @param string $password
     * @param int $remember_me
     * @return \Zend\Authentication\Result
     * @throws \Exception
     */
    public function login($email, $password, $remember_me)
    {
        // Check if user has already logged in. If so, do not allow to log in twice.
        if(null != $this->authService->getIdentity()) {
            $this->logger->err(__METHOD__ . PHP_EOL . 'User['. $email .'] has login. no need login again');
            throw new \Exception('Already logged in');
        }

        // Authentication with login/password
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

        if ($result->getCode() == Result::SUCCESS && $remember_me) {
            $this->sessionManager->rememberMe(60*60*24*30); // Session cookie lifetime to One month
        }

        return $result;
    }


    /**
     * Performs user logout
     */
    public function logout()
    {
        // Allow to log out only when user is logged in.
        if (null != $this->authService->getIdentity()) {
            // Remove identity from session
            $this->authService->clearIdentity();
            $this->logger->debug(__METHOD__ . PHP_EOL . 'User logout success');
        }
    }


    /**
     * Access control for controller and action.
     * access by global configuration key: access_filter.
     * Default forbid any access for unauthenticated user
     *
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function access($controller, $action)
    {
        if (!isset($this->config['controllers'])) { // Forbid any access if no access_filter configuration.
            return false;
        }

        $identified = $this->authService->hasIdentity(); // Current auth status

        if (!isset($this->config['controllers'][$controller])) {
            // The controller has no configuration, only authenticated user can access
            return $identified;
        }

        $actions = $this->config['controllers'][$controller]; // The listed actions
        if (is_array($actions) && (in_array('*', $actions) || in_array($action, $actions))) {
            return true;
        }

        return false;
    }


}