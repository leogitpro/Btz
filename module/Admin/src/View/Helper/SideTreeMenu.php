<?php
/**
 * Admin side menu helper
 *
 * User: leo
 */

namespace Admin\View\Helper;


use Zend\Form\View\Helper\AbstractHelper;

class SideTreeMenu extends AbstractHelper
{

    /**
     * @var array
     */
    private $items;


    /**
     * @var string
     */
    private $activeId;


    /**
     * SideTreeMenu constructor.
     * @param array $items
     */
    public function __construct($items = [])
    {
        $this->items = $items;
        $this->activeId = null;
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
     * @return string
     */
    public function getActiveId()
    {
        return $this->activeId;
    }

    /**
     * @param string $activeId
     */
    public function setActiveId($activeId)
    {
        $this->activeId = $activeId;
    }


    /**
     * Render the side tree menu
     *
     * @return string
     */
    public function render()
    {
        if (empty($this->items)) {
            return '';
        }

        if (null !== $this->activeId) {
            $atTopKey = null;
            foreach ($this->items as $key => $item) { // Top level search
                if (in_array($this->activeId, $item)) {
                    $atTopKey = $key;
                    break;
                }
            }

            if (null !== $atTopKey) {
                $this->items[$atTopKey]['active'] = true;
            } else {
                $atSubKey = null;
                foreach ($this->items as $key => $item) {
                    $atTopKey = $key;
                    if(isset($item['dropdown'])) {
                        foreach($item['dropdown'] as $subKey => $subItem) {
                            if (in_array($this->activeId, $subItem)) {
                                $atSubKey = $subKey;
                                break 2;
                            }
                        }
                    }
                }

                if (null !== $atSubKey) {
                    $this->items[$atTopKey]['active'] = true;
                    $this->items[$atTopKey]['dropdown'][$atSubKey]['active'] = true;
                } else {
                    $atSubSubKey = null;
                    foreach ($this->items as $key => $item) {
                        $atTopKey = $key;
                        if (isset($item['dropdown'])) {
                            foreach ($item['dropdown'] as $subKey => $subItem) {
                                $atSubKey = $subKey;
                                if (isset($subItem['dropdown'])) {
                                    foreach ($subItem['dropdown'] as $subSubKey => $subSubItem) {
                                        if (in_array($this->activeId, $subSubItem)) {
                                            $atSubSubKey = $subSubKey;
                                            break 3;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (null !== $atSubSubKey) {
                        $this->items[$atTopKey]['active'] = true;
                        $this->items[$atTopKey]['dropdown'][$atSubKey]['active'] = true;
                        $this->items[$atTopKey]['dropdown'][$atSubKey]['dropdown'][$atSubSubKey]['active'] = true;
                    }

                }
            }
        }


        $html = '<div class="navbar-default sidebar" role="navigation">';
        $html .= '<div class="sidebar-nav navbar-collapse">';
        $html .= '<ul id="side-menu" class="nav">';

        foreach($this->items as $item) {
            // Item constructor
            /**
            $item = [
                'id' => 'id',
                'priority' => 0,
                'icon' => 'user', // fa-user, fa-sign-out, ...
                'label' => 'Sub item',
                'link' => '#',
                'title' => 'Sub item',
                'dropdown' => [
                    // sub items
                ],
            ];
            //*/

            $label = isset($item['label']) ? $item['label'] : '';
            $icon = isset($item['icon']) ? '<i class="fa fa-' . $item['icon'] . ' fa-fw"></i>' : '';
            if (empty($label . $icon)) {
                return '';
            }

            $link = isset($item['link']) ? $item['link'] : '#';
            $title = isset($item['title']) ? ' title="' . $item['title'] . '"' : '';

            if (!empty($item['active']) && $item['active']) {
                $html .= '<li class="active">';
            } else {
                $html .= '<li>';
            }

            if (empty($item['dropdown'])) {
                $html .= '<a href="' . $link . '"' . $title . '>' . $icon . ' ' . $label . '</a>';
            } else {
                $link = '#';
                $html .= '<a href="' . $link . '"' . $title . '>' . $icon . ' ' . $label . ' <span class="fa arrow"></span></a>';
                $html .= '<ul class="nav nav-second-level">';

                foreach($item['dropdown'] as $subItem) {
                    $html .= $this->renderItem($subItem);
                }

                $html .= '</ul>';
            }

            $html .= '</li>';
        }

        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }


    /**
     * Render item, second level menu
     *
     * @param array $item
     * @return string
     */
    private function renderItem($item)
    {
        // Item constructor
        /**
        $item = [
            'id' => 'id',
            'icon' => 'user', // fa-user, fa-sign-out, ...
            'label' => 'Sub item',
            'link' => '#',
            'title' => 'Sub item',
            'dropdown' => [
                // sub items
            ],
        ];
        //*/

        $label = isset($item['label']) ? $item['label'] : '';
        $icon = isset($item['icon']) ? '<i class="fa fa-' . $item['icon'] . ' fa-fw"></i>' : '';
        if (empty($label . $icon)) {
            return '';
        }
        $link = isset($item['link']) ? $item['link'] : '#';
        $title = isset($item['title']) ? ' title="' . $item['title'] . '"' : '';

        $html = '<li';
        if(empty($item['active'])) {
            $html .= '>';
        } else {
            $html .= ' class="active">';
        }

        if (empty($item['dropdown'])) {
            $html .= '<a href="' . $link . '"' . $title . '>' . $icon . ' ' . $label . '</a>';
        } else {
            $link = '#';
            $html .= '<a href="' . $link . '"' . $title . '>' . $icon . ' ' . $label . ' <span class="fa arrow"></span></a>';
            $html .= '<ul class="nav nav-third-level">';

            foreach($item['dropdown'] as $subItem) {
                $html .= $this->renderSubItem($subItem);
            }

            $html .= '</ul>';
        }

        $html .= '</li>';

        return $html;
    }


    /**
     * Render sub item, third level menu
     *
     * @param array $item
     * @return string
     */
    private function renderSubItem($item)
    {
        // Item constructor
        /**
        $item = [
            'id' => 'id',
            'icon' => 'user', // fa-user, fa-sign-out, ...
            'label' => 'Sub item',
            'link' => '#',
            'title' => 'Sub item',
        ];
        //*/

        $label = isset($item['label']) ? $item['label'] : '';
        $icon = isset($item['icon']) ? '<i class="fa fa-' . $item['icon'] . ' fa-fw"></i>' : '';
        if (empty($label . $icon)) {
            return '';
        }
        $link = isset($item['link']) ? $item['link'] : '#';
        $title = isset($item['title']) ? ' title="' . $item['title'] . '"' : '';
        $active = isset($item['active']) ? ' class="active"' : '';

        return '<li'. $active .'><a href="' . $link . '"' . $title . '>' . $icon . ' ' . $label . '</a></li>';
    }

}