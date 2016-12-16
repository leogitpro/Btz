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


    public function __construct(AuthenticationService $authServcie, SessionManager $sessionManager, $config)
    {
        $this->authService = $authServcie;
        $this->sessionManager = $sessionManager;
        $this->config = $config;
    }


    /**
     * Pefforms a login attempt.
     *
     * @param string $email
     * @param string $passwd
     */
    public function login($email, $passwd)
    {
        // Check if user has already logged in. If so, do not allow to log in twice.
        if(null != $this->authService->getIdentity()) {
            throw new \Exception('Already logged in');
        }

        // Authencation with login/password
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPasswd($passwd);
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