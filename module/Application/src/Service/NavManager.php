<?php
/**
 * Navgation service
 *
 * User: leo
 */

namespace Application\Service;


use Zend\Authentication\AuthenticationService;
use Zend\View\Helper\Url;

class NavManager
{

    /**
     * @var AuthenticationService
     */
    private $authService;


    /**
     * @var Url
     */
    private $urlHelper;


    /**
     * NavManager constructor.
     *
     * @param AuthenticationService $authService
     * @param Url $urlHelper
     */
    public function __construct(AuthenticationService $authService, Url $urlHelper)
    {

        $this->authService = $authService;
        $this->urlHelper = $urlHelper;

    }


    public function getMenuItems()
    {
        $url = $this->urlHelper;
        $items = [];

        $items[] = [
            'id' => 'home',
            'label' => 'Home',
            'link'  => $url('home'),
        ];

        //**
        $items[] = [
            'id' => 'test',
            'label' => 'Test',
            'link'  => $url('app/index_actions', ['action' => 'test', 'suffix' => '.html']),
        ];
        // */

        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users.
        //**
        if (!$this->authService->hasIdentity()) {
            $items[] = [
                'id' => 'guest',
                'label' => 'Guest',
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'login',
                        'label' => 'Sign in',
                        'link' => $url('user_auth_actions', ['action'=>'login', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'sign-up',
                        'label' => 'Sign up',
                        'link' => $url('user_auth_actions', ['action'=>'sign-up', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'forgot-password',
                        'label' => 'Forgot password',
                        'link' => $url('user_auth_actions', ['action' => 'forgot-password', 'suffix' => '.html'])
                    ],
                ]
            ];
        } else {

            $items[] = [
                'id' => 'logout',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'profile',
                        'label' => 'My Profile',
                        'link' => $url('user_profile')
                    ],
                    [
                        'id' => 'logout',
                        'label' => 'Sign out',
                        'link' => $url('user_auth_actions', ['action'=>'logout'])
                    ],
                ]
            ];
        }
        //*/
        return $items;
    }

}