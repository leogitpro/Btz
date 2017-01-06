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
        if ($pages <= 1 || $this->page > $pages) {
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


        if (1 == $this->page) {
            $html .= '<li class="disabled"><span>First</span></li>';
            $html .= '<li class="disabled"><span>&lt;</span></li>';
        } else {
            $html .= '<li><a href="' . sprintf($this->urlTpl, 1) . '">First</a></li>';
            $html .= '<li><a href="' . sprintf($this->urlTpl, ($this->page - 1)) . '">&lt;</a></li>';
        }

        $showLinksCount = 7;
        if ($showLinksCount >= $pages) {
            $start = 1;
            $end = $pages;
        } else {
            $sideLength = intval($showLinksCount / 2);

            $start = $this->page - $sideLength;
            $end = $this->page + $sideLength;
            if ($start <= 0) {
                $end += (1 - $start);
                $start = 1;
            }
            if ($end > $pages) {
                $start -= ($end - $pages);
                $end = $pages;
            }
        }


        for ($i = $start; $i <= $end; $i++) {
            if ($i == $this->page) {
                $html .= '<li class="active"><a href="' . sprintf($this->urlTpl, $i) . '">' . $i . '</a></li>';
            } else {
                $html .= '<li><a href="' . sprintf($this->urlTpl, $i) . '">' . $i . '</a></li>';
            }
        }

        if ($pages == $this->page) {
            $html .= '<li class="disabled"><span>&gt;</span></li>';
            $html .= '<li class="disabled"><span>Last</span></li>';
        } else {
            $html .= '<li><a href="' . sprintf($this->urlTpl, ($this->page + 1)) . '">&gt;</a></li>';
            $html .= '<li><a href="' . sprintf($this->urlTpl, $pages) . '">Last</a></li>';
        }


        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }


}