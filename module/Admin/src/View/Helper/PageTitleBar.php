<?php
/**
 * SB Admin V2 page title bar helper
 *
 * User: leo
 */

namespace Admin\View\Helper;


use Zend\View\Helper\AbstractHelper;

class PageTitleBar extends AbstractHelper
{
    /**
     * @var string
     */
    private $title;


    public  function __invoke($title = null)
    {
        if (null !== $title) {
            $this->setTitle($title);
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }


    /**
     * @return string
     */
    public function render()
    {
        $html = '<div class="row">';
        $html .= '<div class="col-lg-12">';
        $html .= '<h1 class="page-header">' . $this->getTitle() . '</h1>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

}