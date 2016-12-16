<?php
/**
 * View Helper for Breadcrumbs
 *
 * User: leo
 */


namespace Application\View\Helper;



use Zend\Form\View\Helper\AbstractHelper;

class Breadcrumbs extends AbstractHelper
{

    /**
     * Example:
     * $items = [
     *     'Home' => '/',
     *     'About' => '/about.html',
     * ];
     *
     * @var array
     */
    private $items = [];


    /**
     * Breadcrumbs constructor.
     *
     * @param array $items
     */
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
     * Render breadcrumbs component
     *
     * @return string
     */
    public function render()
    {
        if (0 == count($this->items)) {
            return '';
        }

        $result = '<ol class="breadcrumb">';

        $itemCount = count($this->items);
        $itemNum = 1;

        foreach ($this->items as $label => $link) {

            $isActive = ($itemNum == $itemCount ? true : false);
            $result .= $this->renderItem($label, $link, $isActive);
            $itemNum++;
        }

        $result .= '</ol>';

        return $result;
    }


    /**
     * @param $label link label
     * @param $link link url
     * @param $isActive current link
     *
     * @return string
     */
    public function renderItem($label, $link, $isActive)
    {
        $escapeHtml = $this->getView()->plugin('escapeHtml');

        $result = $isActive ? '<li class="active">':'<li>';

        if (!$isActive) {
            $result .= '<a href="' . $escapeHtml($link) . '">' . $escapeHtml($label) . '</a>';
        } else {
            $result .= $escapeHtml($label);
        }

        $result .= '</li>';

        return $result;
    }

}