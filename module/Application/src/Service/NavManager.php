<?php
/**
 * Navgation service
 *
 * User: leo
 */

namespace Application\Service;


use Zend\View\Helper\Url;


class NavManager
{

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
     * @param Url $urlHelper
     */
    public function __construct(Url $urlHelper, $wxApis)
    {
        $this->urlHelper = $urlHelper;
        $this->items = [];


        $testItem = [
            'id' => 'test',
            'label' => '接口验证',
            'dropdown' => [],
        ];
        foreach ((array)$wxApis as $k => $v) {
            if ('oauth' == $k) {
                continue;
            }
            $item = [
                'id' => $k,
                'label' => '验证 ' . $v,
                'title' => '验证 ' . $v,
                'link' => $urlHelper('app/test', ['action' => 'index', 'key' => $k, 'suffix' => '.html']),
            ];
            $testItem['dropdown'][] = $item;
        }


        //$this->addItem(['id' => 'home', 'label' => '首页', 'link' => $urlHelper('home')]);
        $this->addItem(['id' => 'service', 'label' => '服务&产品', 'link' => $urlHelper('service')]);
        $this->addItem(['id' => 'contact', 'label' => '联络我们', 'link' => $urlHelper('contact')]);
        $this->addItem(['id' => 'apidoc', 'label' => '接口文档', 'link' => $urlHelper('app/index', ['action' => 'apidoc', 'suffix' => '.html'])]);
        //$this->addItem($testItem);
        $this->addItem(['id' => 'x', 'label' => '<i class="fa fa-user-circle-o fa-fw fa-2x" aria-hidden="true"></i>', 'float' => 'right', 'link' => $urlHelper('admin')]);
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
        /**
        $url = $this->urlHelper;
        $this->items[] = [
            'id' => 'guest',
            'label' => 'Hi: Guest!',
            'float' => 'right',
            'dropdown' => [
                [
                    'id' => 'admin',
                    'label' => '<i class="fa fa-support" aria-hidden="true"></i> CPanel',
                    'title' => 'Control Panel',
                    'link' => $url('admin', ['suffix' => '.html'])
                ],
            ]
        ];
        //*/


        return $this->items;
    }

}