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
    public function __construct(Url $urlHelper)
    {
        $this->urlHelper = $urlHelper;
        $this->items = [];

        $this->addItem(['id' => 'home', 'label' => 'Home', 'link' => $urlHelper('home')]);
        //$this->addItem(['id' => 'contact', 'label' => 'Contact Us', 'link' => $urlHelper('contact')]);
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


        return $this->items;
    }

}