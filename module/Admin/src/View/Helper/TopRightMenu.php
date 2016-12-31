<?php
/**
 * SB Admin v2 template top nav bar helper
 *
 * User: leo
 */

namespace Admin\View\Helper;


use Zend\View\Helper\AbstractHelper;

class TopRightMenu extends AbstractHelper
{

    /**
     * Top nav links
     *
     * @var array
     */
    private $items;


    /**
     * TopMenu constructor.
     *
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->setItems($items);
    }


    /**
     * @param array $items
     */
    public function setItems($items = [])
    {
        $this->items = $items;
    }


    /**
     * Render the right top navigation items
     *
     * @return string
     */
    public function render()
    {
        if (empty($this->items)) {
            return '';
        }

        $html = '<ul class="nav navbar-top-links navbar-right">';
        foreach($this->items as $item) {
            $html .= $this->renderItem($item);
        }
        $html .= '</ul>';

        return $html;
    }


    /**
     * Render a top item
     * Example item constructor
     * $item = [
     *     'icon' => 'user',
     *     'label' => 'Label',
     *     'link' => '#',
     *     'title' => '',
     *     'dropdown' => [
     *         [
     *             'type' => 'divider|item',
     *             'icon' => 'fa-user',
     *             'label' => 'Label',
     *             'link' => '#',
     *             'title' => '',
     *         ],
     *     ],
     * ];
     *
     *
     * @param array $item
     * @return string
     */
    public function renderItem($item)
    {
        $label = isset($item['label']) ? $item['label'] : '';
        $icon = isset($item['icon']) ? '<i class="fa fa-' . $item['icon'] . ' fa-fw"></i>' : '';
        if (empty($label . $icon)) {
            return '';
        }
        $link = isset($item['link']) ? $item['link'] : '#';
        $title = isset($item['title']) ? ' title="' . $item['title'] . '"' : '';

        if (!isset($item['dropdown'])) {
            return '<li><a href="' . $link . '"' . $title . '>' . $icon . ' ' . $label . '</a></li>';
        } else {
            $html = '<li class="dropdown">';
            $html .= '<a class="dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="true">';
            $html .= $icon . ' ' . $label . ' <i class="fa fa-caret-down"></i></a>';
            $html .= '<ul class="dropdown-menu">';

            foreach($item['dropdown'] as $subItem) {
                if (!isset($subItem['type']) || !in_array($subItem['type'], ['divider', 'item'])) {
                    continue;
                }
                if('divider' == $subItem['type']) {
                    $html .= '<li class="divider"></li>';
                } else {
                    $subLabel = isset($subItem['label']) ? $subItem['label'] : '';
                    $subIcon = isset($subItem['icon']) ? '<i class="fa fa-' . $subItem['icon'] . ' fa-fw"></i> ' : '';
                    if (empty($subLabel . $subIcon)) {
                        continue;
                    }
                    $subLink = isset($subItem['link']) ? $subItem['link'] : '#';
                    $subTitle = isset($subItem['title']) ? ' title="' . $subItem['title'] . '"' : '';

                    $html .= '<li><a href="' . $subLink . '"' . $subTitle . '>' . $subIcon . ' ' . $subLabel . '</a></li>';
                }
            }

            $html .= '</ul>';
            $html .= '</li>';

            return $html;
        }
    }

}