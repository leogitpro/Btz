<?php
/**
 * Pagination.php
 *
 * @author: Leo <camworkster@gmail.com>
 * @version: 1.0
 */


namespace Admin\View\Helper;


use Zend\Form\View\Helper\AbstractHelper;

class Pagination extends AbstractHelper
{

    /**
     * @var integer
     */
    private $count;

    /**
     * @var integer
     */
    private $size;

    /**
     * @var string
     */
    private $urlTpl;

    /**
     * @var integer
     */
    private $page;


    public function __construct()
    {
        $this->count = 0;
        $this->page = 1;
        $this->size = 0;
        $this->urlTpl = '';
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getUrlTpl()
    {
        return $this->urlTpl;
    }

    /**
     * @param string $urlTpl
     */
    public function setUrlTpl($urlTpl)
    {
        $this->urlTpl = urldecode($urlTpl);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }




    public function render()
    {
        $pages = ceil($this->count / $this->size);
        if ($pages <= 1) {
            return '';
        }

        $html = '<div class="row">';
        $html .= '<div class="col-sm-6">';

        $start = (($this->page - 1) * $this->size + 1);
        $html .= '<ul class="pagination"><li>';
        $html .= 'Showing <strong>' . $start . '</strong> to <strong>' . ($start + $this->size - 1) . '</strong> of <strong>' . $this->count . '</strong> records';
        $html .= '</li></ul>';

        $html .= '</div>';
        $html .= '<div class="col-sm-6 text-right">';
        $html .= '<ul class="pagination">';

        for ($i = 1; $i <= $pages; $i++) {
            $active = '';
            if ($i == $this->page) {
                $active = ' class="active"';
            }
            $html .= '<li' . $active . '><a href="' . sprintf($this->urlTpl, $i) . '">' . $i . '</a></li>';
        }


        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }


}