<?php


function buildSidebarMenu($menus, $parent = null)
{
    $html = '';

    foreach ($menus as $menu) {

        if ($menu['parent_id'] == $parent) {

            $children = buildSidebarMenu($menus, $menu['menu_id']);

            $hasChildren = !empty($children);

            $treeClass = $hasChildren ? 'has-treeview' : '';

            $url = !empty($menu['url']) ? $menu['url'] : '#';

            $icon = !empty($menu['icon'])
                ? $menu['icon']
                : 'fa fa-circle-o';

            $html .= '<li class="nav-item ' . $treeClass . '">';

            $html .= '<a href="' . $url . '" class="nav-link">';

            $html .= '<i class="nav-icon ' . $icon . '"></i>';

            $html .= '<p>';

            $html .= $menu['title'];

            if ($hasChildren) {

                $html .= '<i class="right fa fa-angle-left"></i>';
            }

            $html .= '</p>';

            $html .= '</a>';

            if ($hasChildren) {

                $html .= '<ul class="nav nav-treeview">';

                $html .= $children;

                $html .= '</ul>';
            }

            $html .= '</li>';
        }
    }

    return $html;
}
