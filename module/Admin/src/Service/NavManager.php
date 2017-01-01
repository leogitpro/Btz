<?php
/**
 * Admin module menu manager
 *
 * User: leo
 */

namespace Admin\Service;


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


    public function __construct(AuthService $authService, MemberManager $memberManager, Url $url)
    {
        $this->authService = $authService;
        $this->memberManager = $memberManager;
        $this->urlHelper = $url;

        $this->initTopRightItem();

        $this->initBreadcrumbItem();

        $this->initSideTreeItem();

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

        if(!$this->authService->hasIdentity()) {
            return ;
        }

        $identity = $this->authService->getIdentity();
        $member = $this->memberManager->getMember($identity);
        if (null == $member) {
            return ;
        }

        $url = $this->urlHelper;

        // Current user profile menu configuration
        $memberItem = $this->createNavItem('profile_menu', 'user', $member->getMemberName());
        $memberItem['dropdown'] = [
            $this->createNavItem('summary', 'user', 'Summary', $url('admin/profile'), $member->getMemberName()),
            $this->createNavItem('password', 'key', 'Password', $url('admin/profile', ['action' => 'password'])),
            $this->createNavItem('profile_detail', 'cog', 'Profile detail', $url('admin/profile', ['action' => 'update'])),
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
     *
     * @param $id
     * @param $label
     * @return array
     */
    public function createItem($id, $label)
    {
        $url = $this->urlHelper;
        return [
            'id' => $id,
            'icon' => 'user',
            'label' => $label,
            'link' => $url('admin/dashboard_detail', ['key' => rand(1001, 9999), 'suffix' => '.html']),
            'title' => $label,
        ];
    }

    /**
     * Test tree menu
     */
    public function initSideTreeItem()
    {
        $this->sideTreeItems = [];

        $url = $this->urlHelper;

        $dept = $this->createNavItem('dept', 'users', 'Department');
        $dept['dropdown'] = [
            $this->createNavItem('dept_list', 'bars', 'Departments', $url('admin/dept')),
            $this->createNavItem('dept_add', 'plus', 'Create Dept', $url('admin/dept', ['action' => 'add'])),
        ];
        $this->addSideTreeItem($dept);





        $topItem1 = $this->createItem('id100', 'Top Menu 1');
        $topItem2 = $this->createItem('id200', 'Top Menu 2');
        $topItem3 = $this->createItem('id300', 'Top Menu 3');


        $topItem2['dropdown'] = [
            $this->createItem('id210', 'Sub Menu 1'),
            $this->createItem('id220', 'Sub Menu 2'),
            $this->createItem('id230', 'Sub Menu 3')
        ];

        $subItem31 = $this->createItem('id310', 'Sub Menu 1');
        $subItem32 = $this->createItem('id320', 'Sub Menu 2');
        $subItem33 = $this->createItem('id330', 'Sub Menu 3');
        //$topItem3['dropdown'] = [$subItem31, $subItem32, $subItem33];

        //**
        $subItem31['dropdown'] = [
            $this->createItem('id311', 'Sub Sub Menu 1'),
            $this->createItem('id312', 'Sub Sub Menu 2'),
            $this->createItem('id313', 'Sub Sub Menu 3'),
        ];
        $subItem32['dropdown'] = [
            $this->createItem('id321', 'Sub Sub Menu 1'),
            $this->createItem('id322', 'Sub Sub Menu 2'),
            $this->createItem('id323', 'Sub Sub Menu 3'),
        ];
        $subItem33['dropdown'] = [
            $this->createItem('id331', 'Sub Sub Menu 1'),
            $this->createItem('id332', 'Sub Sub Menu 2'),
            $this->createItem('id333', 'Sub Sub Menu 3'),
        ];
        $topItem3['dropdown'] = [$subItem31, $subItem32, $subItem33];
        //*/

        $this->addSideTreeItem($topItem1);
        $this->addSideTreeItem($topItem2);
        $this->addSideTreeItem($topItem3);

    }

    /**
     * @param array $item
     */
    public function addSideTreeItem($item)
    {
        array_push($this->sideTreeItems, $item);
    }


}