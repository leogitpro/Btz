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
     * @var array
     */
    private $items;


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

        $this->items = [];

        $this->addItem(['id' => 'home', 'label' => 'Home', 'link' => $urlHelper('home')]);
        $this->addItem(['id' => 'contact', 'label' => 'Contact Us', 'link' => $urlHelper('contact')]);
        //$this->addItem(['id' => 'about', 'label' => 'About', 'link' => $this->urlHelper('about')]);
    }


    /**
     * Add a item menu
     *
     * @param array $item
     */
    public function addItem($item) {
        array_push($this->items, $item);
    }


    /**
     * Get menu items
     *
     * @return array
     */
    public function getMenuItems()
    {
        $url = $this->urlHelper;
        if (!$this->authService->hasIdentity()) {
            $this->items[] = [
                'id' => 'guest',
                'label' => 'Hi: Guest!',
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'login',
                        'label' => '<i class="fa fa-sign-in" aria-hidden="true"></i> Sign in',
                        'title' => 'Sign in',
                        'link' => $url('user/auth', ['action'=>'login', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'sign-up',
                        'label' => '<i class="fa fa-user-plus" aria-hidden="true"></i> Sign up',
                        'title' => 'Sign up',
                        'link' => $url('user/auth', ['action'=>'sign-up', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'forgot-password',
                        'label' => '<i class="fa fa-support" aria-hidden="true"></i> Password',
                        'title' => 'Forgot password',
                        'link' => $url('user/auth', ['action' => 'forgot-password', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'admin',
                        'label' => '<i class="fa fa-support" aria-hidden="true"></i> CPanel',
                        'title' => 'Control Panel',
                        'link' => $url('admin', ['suffix' => '.html'])
                    ],
                ]
            ];
        } else {
            $this->items[] = [
                'id' => 'profile',
                'label' => $this->authService->getIdentity(),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'profile',
                        'label' => '<i class="fa fa-home" aria-hidden="true"></i> Preface',
                        'title' => 'My Preface',
                        'link' => $url('user/profile', ['suffix' => '.html'])
                    ],
                    [
                        'id' => 'update',
                        'label' => '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Profile',
                        'title' => 'Update profile',
                        'link' => $url('user/profile', ['action' => 'update', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'email',
                        'label' => '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> E-mail',
                        'title' => 'Update E-mail address',
                        'link' => $url('user/profile', ['action' => 'email', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'password',
                        'label' => '<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Password',
                        'title' => 'Update password',
                        'link' => $url('user/profile', ['action' => 'password', 'suffix' => '.html'])
                    ],
                    [
                        'id' => 'logout',
                        'label' => '<i class="fa fa-sign-out" aria-hidden="true"></i> Sign out',
                        'title' => 'Sign out',
                        'link' => $url('user/auth', ['action'=>'logout', 'suffix' => '.html'])
                    ],
                ]
            ];
        }

        return $this->items;
    }

}