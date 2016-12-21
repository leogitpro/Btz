<?php
/**
 * AuthManager
 *
 * User: leo
 */

namespace User\Service;


use Zend\Authentication\AuthenticationService;
use Zend\Session\SessionManager;

class AuthManager
{

    /**
     * @var AuthenticationService
     */
    private $authService;


    /**
     * @var SessionManager
     */
    private $sessionManager;


    /**
     * @var array
     */
    private $config;


    public function __construct(AuthenticationService $authService, SessionManager $sessionManager, $config)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
    }


    /**
     * @param $email
     * @param $password
     * @return \Zend\Authentication\Result
     * @throws \Exception
     */
    public function login($email, $password)
    {
        // Check if user has already logged in. If so, do not allow to log in twice.
        if(null != $this->authService->getIdentity()) {
            throw new \Exception('Already logged in');
        }

        // Authentication with login/password
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();

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
        }
    }


}