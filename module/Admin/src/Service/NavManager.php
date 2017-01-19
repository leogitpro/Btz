<?php
/**
 * Admin module menu manager
 *
 * User: leo
 */

namespace Admin\Service;


use Admin\Entity\Member;
use Zend\View\Helper\Url;


class NavManager
{

    /**
     * @var Url
     */
    private $urlHelper;

    /**
     * @var AuthService
     */
    private $authService;

    /**
     * @var MemberManager
     */
    private $memberManager;

    /**
     * @var AclManager
     */
    private $aclManager;

    /**
     * @var array
     */
    private $topRightItems;

    /**
     * @var array
     */
    private $sideTreeItems;

    /**
     * @var array
     */
    private $breadcrumbItems;



    private $member = null;



    public function __construct(AuthService $authService, MemberManager $memberManager, Url $url, AclManager $aclManager)
    {
        $this->authService = $authService;

        $this->memberManager = $memberManager;

        $this->aclManager = $aclManager;

        $this->urlHelper = $url;

        $this->initSideTreeItem();

        $this->initTopRightItem();

        $this->initBreadcrumbItem();

    }


    /**
     * Get current member
     *
     * @return Member|null
     */
    public function getCurrentMember()
    {
        if (null === $this->member) {
            if(!$this->authService->hasIdentity()) {
                return null;
            }
            $identity = $this->authService->getIdentity();

            $this->member = $this->memberManager->getMember($identity);
        }

        return $this->member;
    }


    /**
     * @return array
     */
    public function getTopRightItems()
    {
        return $this->topRightItems;
    }

    /**
     * @return array
     */
    public function getSideTreeItems()
    {
        return $this->sideTreeItems;
    }

    /**
     * @return array
     */
    public function getBreadcrumbItems()
    {
        return $this->breadcrumbItems;
    }



    /**
     * Quick create nav menu link item array
     *
     * @param string $id
     * @param string $icon
     * @param string $label
     * @param string $link
     * @param string $title
     * @param string $type item|divider
     * @return array
     */
    public function createNavItem($id, $icon, $label, $link = '', $title = '', $type = 'item')
    {
        return [
            'id' => $id,
            'icon' => $icon,
            'label' => $label,
            'link' => $link,
            'title' => empty($title) ? $label : $title,
            'type' => $type
        ];
    }


    /**
     * Init top right bar items
     */
    public function initTopRightItem()
    {
        $this->topRightItems = [];

        $member = $this->getCurrentMember();
        if (!($member instanceof Member)) {
            return ;
        }

        $url = $this->urlHelper;

        // Current user profile menu configuration
        $memberItem = $this->createNavItem('profile_menu', 'user', $member->getMemberName());
        $memberItem['dropdown'] = [
            $this->createNavItem('summary', 'user', 'Summary', $url('admin/profile'), $member->getMemberName()),
            $this->createNavItem('password', 'hashtag', 'Password', $url('admin/profile', ['action' => 'password'])),
            $this->createNavItem('profile_detail', 'edit', 'Profiles', $url('admin/profile', ['action' => 'update'])),
            $this->createNavItem('', '', '', '', '', 'divider'),
            $this->createNavItem('profile_logout', 'sign-out', 'Logout', $url('admin/index', ['action' => 'logout', 'suffix' => '.html'])),
        ];

        $this->addTopRightItem($memberItem);

        // Logout menu configuration
        $logoutItem = $this->createNavItem('logout', 'sign-out', 'Logout', $url('admin/index', ['action' => 'logout', 'suffix' => '.html']));
        $this->addTopRightItem($logoutItem);
    }


    /**
     * Add a top item
     *
     * @param array $item
     */
    public function addTopRightItem($item)
    {
        array_push($this->topRightItems, $item);
    }


    /**
     * Init breadcrumbs
     */
    public function initBreadcrumbItem()
    {
        $this->breadcrumbItems = [];
        $url = $this->urlHelper;
        $this->addBreadcrumbItem('Home', $url('admin'));
    }

    /**
     * Add a breadcrumb
     *
     * @param string $label
     * @param string $link
     */
    public function addBreadcrumbItem($label, $link)
    {
        $item = $this->createNavItem('', '', $label, $link);
        array_push($this->breadcrumbItems, $item);
    }


    /**
     * Test tree menu
     */
    public function initSideTreeItem()
    {
        $this->sideTreeItems = [];
        $url = $this->urlHelper;

        $dashboard = $this->createNavItem('dashboard', 'dashboard', 'Dashboard', $url('admin/dashboard'));
        $this->addSideTreeItem($dashboard);


        $member = $this->getCurrentMember();
        if (!($member instanceof Member)) {
            return ;
        }

        if (Member::LEVEL_SUPERIOR == $member->getMemberLevel()) {
            $menus = $this->aclManager->getGlobalMenus();
        } else {
            $menus = $this->aclManager->getMemberMenus($member->getMemberId());
        }

        if (empty($menus)) {
            return;
        }

        foreach ($menus as $component) {

            $item = $this->createNavItem(
                $component['class'],
                $component['icon'],
                $component['name'],
                $url($component['route'])
            );

            foreach ($component['actions'] as $action) {
                $subItem = $this->createNavItem(
                    $component['class'] . '::' . str_replace('-', '', lcfirst(ucwords($action['key'], '-'))) . 'Action',
                    $action['icon'],
                    $action['name'],
                    $url($component['route'], ['action' => $action['key']])
                );
                $item['dropdown'][] = $subItem;
            }

            $this->addSideTreeItem($item);
        }

    }

    /**
     * @param array $item
     */
    public function addSideTreeItem($item)
    {
        array_push($this->sideTreeItems, $item);
    }


}