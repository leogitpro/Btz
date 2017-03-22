<?php
/**
 * Admin module auth
 *
 * User: leo
 */

namespace Admin\Service;


use Admin\Exception\RuntimeException;
use Zend\Authentication\Result;
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


    public function __construct(AuthService $authService, SessionManager $sessionManager)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }


    /**
     * Validate administrator login
     *
     * @param string $email
     * @param string $password
     * @return Result
     * @throws RuntimeException
     */
    public function login($email, $password)
    {
        if($this->authService->hasIdentity()) {
            throw new RuntimeException('不允许重复登录!');
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
        }
    }

}