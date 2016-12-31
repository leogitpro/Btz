<?php
/**
 * Page breadcrumb bar helper
 *
 * User: leo
 */

namespace Admin\View\Helper;


use Zend\View\Helper\AbstractHelper;

class PageBreadcrumbBar extends AbstractHelper
{

    /**
     * @var array
     */
    private $items;


    public function __construct($items = [])
    {
        $this->items = $items;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * Add new item
     *
     * @param string $label
     * @param string $link
     * @return PageBreadcrumbBar
     */
    public function addItem($label, $link = '#') {
        array_push($this->items, ['label' => $label, 'link' => $link]);
        return $this;
    }


    /**
     * Render breadcrumb bar
     *
     * @return string
     */
    public function render()
    {
        if (empty($this->items)) {
            return '';
        }

        $html = '<div class="row">';
        $html .= '<div class="col-lg-12">';
        $html .= '<ol class="breadcrumb">';

        $itemCount = count($this->items);
        $itemNum = 1;
        foreach ($this->items as $item) {
            $isActive = ($itemNum == $itemCount ? true : false);
            $html .= $this->renderItem($item, $isActive);
            $itemNum++;
        }

        $html .= '</ol>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }


    /**
     * Create breadcrumb item
     *
     * @param array $item
     * @param bool $isActive
     * @return string
     */
    public function renderItem($item, $isActive)
    {
        $label = isset($item['label']) ? $item['label'] : '';
        if (empty($label)) {
            return '';
        }

        $html = $isActive ? '<li class="active">' : '<li>';

        $title = isset($item['title']) ? $item['title'] : $label;
        $link = isset($item['link']) ? $item['link'] : '';
        if ($link && '#' != $link) {
            $html .= '<a href="' . $link . '" title="' . $title . '">' . $label . '</a>';
        } else {
            $html .= $label;
        }

        $html .= '</li>';

        return $html;
    }


}