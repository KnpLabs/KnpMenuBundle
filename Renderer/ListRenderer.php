<?php

namespace Bundle\MenuBundle\Renderer;
use Bundle\MenuBundle\MenuItem;

/**
 * Renders MenuItem tree as unordered list
 */
class ListRenderer extends Renderer implements RendererInterface
{
    /**
     * @see RendererInterface::render
     */
    public function render(MenuItem $item, $depth = null)
    {
        return $this->doRender($item, $depth);
    }

    /**
     * Renders menu tree. Internal method.
     *
     * @param MenuItem  $item        Menu item
     * @param integer $depth         The depth of children to render
     * @param boolean $renderAsChild Render with attributes on the li (true) or the ul around the children (false)
     *
     * @return string
     */
    protected function doRender(MenuItem $item, $depth = null, $renderAsChild = false)
    {
        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$item->hasChildren() || $depth === 0 || !$item->getShowChildren()) {
            return '';
        }

        if ($renderAsChild) {
            $attributes = array('class' => 'menu_level_'.$item->getLevel());
        }
        else {
            $attributes = $item->getAttributes();
        }

        // render children with a depth - 1
        $childDepth = ($depth === null) ? null : ($depth - 1);

        $html = $this->format('<ul'.$this->renderHtmlAttributes($attributes).'>', 'ul', $item->getLevel());
        $html .= $this->renderChildren($item, $childDepth);
        $html .= $this->format('</ul>', 'ul', $item->getLevel());

        return $html;
    }

    /**
     * Renders all of the children of this menu.
     *
     * This calls ->renderItem() on each menu item, which instructs each
     * menu item to render themselves as an <li> tag (with nested ul if it
     * has children).
     *
     * @param integer $depth The depth each child should render
     * @return string
     */
    public function renderChildren($item, $depth = null)
    {  
        $html = '';
        foreach ($item->getChildren() as $child) {
            $html .= $this->renderItem($child, $depth);
        }
        return $html;
    }

    /**
     * Called by the parent menu item to render this menu.
     *
     * This renders the li tag to fit into the parent ul as well as its
     * own nested ul tag if this menu item has children
     *
     * @param integer $depth The depth each child should render
     * @return string
     */
    public function renderItem($item, $depth = null)
    {
        // if we don't have access or this item is marked to not be shown
        if (!$item->shouldBeRendered()) {
            return; 
        }

        // explode the class string into an array of classes
        $class = ($item->getAttribute('class')) ? explode(' ', $item->getAttribute('class')) : array();

        if ($item->getIsCurrent()) {
            $class[] = 'current';
        }
        elseif ($item->getIsCurrentAncestor($depth)) {
            $class[] = 'current_ancestor';
        }

        if ($item->actsLikeFirst()) {
            $class[] = 'first';
        }
        if ($item->actsLikeLast()) {
            $class[] = 'last';
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $item->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        // opening li tag
        $html = $this->format('<li'.$this->renderHtmlAttributes($attributes).'>', 'li', $item->getLevel());

        // render the text/link inside the li tag
        $html .= $this->format($item->getUri() ? $item->renderLink() : $item->renderLabel(), 'link', $item->getLevel());

        // renders the embedded ul if there are visible children
        $html .= $this->doRender($item, $depth, true);

        // closing li tag
        $html .= $this->format('</li>', 'li', $item->getLevel());

        return $html;
    }

    /**
     * If $this->renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param  string $html The html to render in an (un)formatted way
     * @param  string $type The type [ul,link,li] of thing being rendered 
     * @return string
     */
    protected function format($html, $type, $level)
    {
        if ($this->renderCompressed) {
            return $html;
        }

        switch ($type) {
        case 'ul':
        case 'link':
            $spacing = $level * 4;
            break;

        case 'li':
            $spacing = $level * 4 - 2;
            break;
        }

        return str_repeat(' ', $spacing).$html."\n";
    }
}
