<?php
/**
 * View helper menu component.
 *
 * User: leo
 */

namespace Application\View\Helper;


use Zend\View\Helper\AbstractHelper;

class Menu extends AbstractHelper
{

    protected $items = [];

    protected $activeItemId = '';


    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @param string $activeItemId
     */
    public function setActiveItemId($activeItemId)
    {
        $this->activeItemId = $activeItemId;
    }


    /**
     * Renders menu
     *
     * @return string
     */
    public function render()
    {
        if (count($this->items) == 0) {
            return '';
        }

        $result = '<nav class="navbar navbar-default" role="navigation">';
        $result .= '<div class="navbar-header">';
        $result .= '<button type="button" class="navbar-toggle" data-toggle="collapse" ';
        $result .= 'data-target=".navbar-ex1-collapse">';
        $result .= '<span class="sr-only">Toggle navigation</span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '<span class="icon-bar"></span>';
        $result .= '</button>';
        $result .= '</div>';

        $result .= '<div class="collapse navbar-collapse navbar-ex1-collapse">';
        $result .= '<ul class="nav navbar-nav">';

        // Render items
        foreach ($this->items as $item) {
            if(!isset($item['float']) || $item['float']=='left')
                $result .= $this->renderItem($item);
        }

        $result .= '</ul>';
        $result .= '<ul class="nav navbar-nav navbar-right">';

        // Render items
        foreach ($this->items as $item) {
            if(isset($item['float']) && $item['float']=='right')
                $result .= $this->renderItem($item);
        }

        $result .= '</ul>';
        $result .= '</div>';
        $result .= '</nav>';

        return $result;

    }


    /**
     * Render a menu item
     *
     * Item constractor:
     * $item = [
     *     'id' => 'id string',
     *     'float' => 'left|right|null',
     *     'label' => 'Label string',
     *     'link' => 'Link string',
     * ];
     *
     * Or dropdown item
     *
     * $item = [
     *     'id' => 'id string',
     *     'float' => 'left|right|null',
     *     'dropdown' => [
     *         [
     *             'link' => 'Link string',
     *             'label' => 'Label string',
     *         ],
     *         ...
     *     ],
     * ];
     *
     * @param $item
     * @return string
     */
    public function renderItem($item)
    {

        $id = isset($item['id']) ? $item['id'] : '';
        $isActive = ($id == $this->activeItemId);
        $label = isset($item['label']) ? $item['label'] : '';
        $title = isset($item['title']) ? $item['title'] : '';

        $result = '';

        $escapeHtml = $this->getView()->plugin('escapeHtml');

        if (isset($item['dropdown'])) {

            $dropdownItems = $item['dropdown'];

            $result .= '<li class="dropdown ' . ($isActive ? 'active' : '') . '">';
            $result .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">';
            $result .= $escapeHtml($label) . ' <b class="caret"></b>';
            $result .= '</a>';

            $result .= '<ul class="dropdown-menu">';
            foreach ($dropdownItems as $item) {
                $link = isset($item['link']) ? $item['link'] : '#';
                $label = isset($item['label']) ? $item['label'] : '';
                $title = isset($item['title']) ? $item['title'] : '';

                $result .= '<li>';
                $result .= '<a href="'.$escapeHtml($link).'" title="'.$title.'">'. $label .'</a>';
                $result .= '</li>';
            }
            $result .= '</ul>';
            $result .= '</li>';

        } else {
            $link = isset($item['link']) ? $item['link'] : '#';

            $result .= $isActive ? '<li class="active">' : '<li>';
            $result .= '<a href="'.$escapeHtml($link).'" title="'.$title.'">' . $escapeHtml($label) . '</a>';
            $result .= '</li>';
        }

        return $result;
    }

}