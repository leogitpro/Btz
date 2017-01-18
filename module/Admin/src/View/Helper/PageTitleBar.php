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

    /**
     * @var string
     */
    private $subTitle;


    public  function __invoke($title = null, $subTitle = null)
    {
        if (null !== $title) {
            $this->setTitle($title);
        }

        if (null != $subTitle) {
            $this->setSubTitle($subTitle);
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
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @param string $subTitle
     */
    public function setSubTitle($subTitle)
    {
        $this->subTitle = $subTitle;
    }




    /**
     * @return string
     */
    public function render()
    {
        if (empty($this->getTitle())) {
            return '<div class="row"></div>';
        }

        $subTitle = '';
        if (!empty($this->getSubTitle())) {
            $subTitle = '<small>' . $this->getSubTitle() . '</small>';
        }

        $html = '<div class="row">';
        $html .= '<div class="col-lg-12">';
        $html .= '<h3 class="page-header">' . $this->getTitle() . ' ' . $subTitle . '</h3>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

}