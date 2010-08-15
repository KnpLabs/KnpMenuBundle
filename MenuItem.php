<?php

namespace Bundle\MenuBundle;

/**
 * This is your base menu item. It roughly represents a single <li> tag
 * and is what you should interact with most of the time by default.
 * 
 * Originally taken from ioMenuPlugin (http://github.com/weaverryan/ioMenuPlugin)
 */
class MenuItem implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Whether or not to render menus with pretty spacing, or fully compressed.
     */
    protected static $renderCompressed = false;
    protected static $charset = 'UTF-8';

    /**
     * Properties on this menu item
     */
    protected
        $name             = null,    // the name of this menu item (used for id by parent menu)
        $label            = null,    // the label to output, name is used by default
        $uri              = null,    // the uri to use in the anchor tag
        $attributes       = array(); // an array of attributes for the li

    /**
     * Options related to rendering
     */
    protected
        $show             = true,    // boolean to render this menu
        $showChildren     = true;    // boolean to render the children of this menu

    /**
     * Metadata on this menu item
     */
    protected
        $children         = array(), // an array of MenuItem children
        $num              = null,    // the order number this menu is in its parent
        $parent           = null,    // parent MenuItem
        $isCurrent        = null,    // whether or not this menu item is current
        $currentUri       = null;    // the current uri to use for selecting current menu

    /**
     * Class constructor
     * 
     * @param string $name    The name of this menu, which is how its parent will
     *                        reference it. Also used as label if label not specified
     * @param string $uri   The uri/url for this menu to use. If not specified,
     *                        text will be shown without a link
     * @param array $attributes Attributes to place on the li tag of this menu item
     */
    public function __construct($name, $uri = null, $attributes = array())
    {
        $this->name = $name;
        $this->uri = $uri;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param  string $name
     * @return MenuItem
     */
    public function setName($name)
    {
        if ($this->name == $name) {
            return $this;
        }

        if ($this->getParent() && $this->getParent()->getChild($name)) {
            throw new \InvalidArgumentException('Cannot rename item, name is already used by sibling.');
        }

        $oldName = $this->name;
        $this->name = $name;

        if ($this->getParent()) {
            $this->getParent()->updateChildId($this, $oldName);
        }

        return $this;
    }

    /**
     * Updates id for child based on new name.
     *
     * Used internally after renaming item which has parent.
     *
     * @param MenuItem $child Item whose name has been changed.
     * @param string $oldName Old (previous) name of item.
     *
     */
    protected function updateChildId(MenuItem $child, $oldName)
    {
        $names = array_keys($this->getChildren());
        $items = array_values($this->getChildren());

        $offset = array_search($oldName, $names);
        $names[$offset] = $child->getName();

        $children = array_combine($names, $items);
        $this->setChildren($children);
    }

    /**
     * Get the uri for a menu item
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }


    /**
     * Set the uri for a menu item
     *
     * @param  string $uri The uri to set on this menu item
     * @return MenuItem
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Returns the label that will be used to render this menu item
     *
     * Defaults to the name of no label was specified
     *
     * @return string
     */
    public function getLabel()
    {
        return ($this->label !== null) ? $this->label : $this->name;
    }

    /**
     * @param  string $label    The text to use when rendering this menu item
     * @return MenuItem
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param  array $attributes 
     * @return MenuItem
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * @param  string $name     The name of the attribute to return
     * @param  mixed  $default  The value to return if the attribute doesn't exist
     * 
     * @return mixed
     */
    public function getAttribute($name, $default = null)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }

        return $default;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * @return bool Whether or not this menu item should show its children.
     */
    public function getShowChildren()
    {
        return $this->showChildren;
    }

    /**
     * Set whether or not this menu item should show its children 
     * 
     * @param bool $bool 
     * @return MenuItem
     */
    public function setShowChildren($bool)
    {
        $this->showChildren = (bool) $bool;

        return $this;
    }

    /**
     * @return bool Whether or not to show this menu item
     */
    public function getShow()
    {
        return $this->show;
    }

    /**
     * Set whether or not this menu to show this menu item
     * 
     * @param bool $bool 
     * @return MenuItem
     */
    public function setShow($bool)
    {
        $this->show = (bool) $bool;

        return $this;
    }

    /**
     * Whether or not this menu item should be rendered or not based on all the available factors
     *
     * @return boolean
     */
    public function shouldBeRendered()
    {
        return $this->getShow();
    }

    /**
     * Add a child menu item to this menu
     *
     * @param mixed   $child    An MenuItem object or the name of a new menu to create
     * @param string  $uri    If creating a new menu, the uri for that menu
     * @param string  $attributes  If creating a new menu, the attributes for that menu
     * @param string  $class    The class for menu item, if it needs to be created
     *
     * @return MenuItem The child menu item
     */
    public function addChild($child, $uri = null, $attributes = array(), $class = null)
    {
        if (!$child instanceof MenuItem) {
            $child = $this->createChild($child, $uri, $attributes, $class);
        }
        elseif ($child->getParent()) {
            throw new \InvalidArgumentException('Cannot add menu item as child, it already belongs to another menu (e.g. has a parent).');
        }

        $child->setParent($this);
        $child->setShowChildren($this->getShowChildren());
        $child->setCurrentUri($this->getCurrentUri());
        $child->setNum($this->count());

        $this->children[$child->getName()] = $child;

        return $child;
    }

    /**
     * Returns the child menu identified by the given name
     *
     * @param  string $name  Then name of the child menu to return
     * @return MenuItem|null
     */
    public function getChild($name)
    {
        return isset($this->children[$name]) ? $this->children[$name] : null;
    }

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * @param numeric $position Position to move child to.
     *
     */
    public function moveToPosition($position)
    {
        $this->getParent()->moveChildToPosition($this, $position);
    }

    /**
     * Moves child to specified position. Rearange other children accordingly.
     *
     * @param MenuItem $child Child to move.
     * @param numeric $position Position to move child to.
     */
    public function moveChildToPosition(MenuItem $child, $position)
    {
        $name = $child->getName();
        $order = array_keys($this->children);

        $oldPosition = array_search($name, $order);
        unset($order[$oldPosition]);

        $order = array_values($order);

        array_splice($order, $position, 0, $name);
        $this->reorderChildren($order);
    }

    /**
     * Moves child to first position. Rearange other children accordingly.
     */
    public function moveToFirstPosition()
    {
        $this->moveToPosition(0);
    }

    /**
     * Moves child to last position. Rearange other children accordingly.
     */
    public function moveToLastPosition()
    {
        $this->moveToPosition($this->getParent()->count());
    }

    /**
     * Reorder children.
     *
     * @param array $order New order of children.
     */
    public function reorderChildren($order)
    {
        if (count($order) != $this->count()) {
            throw new \InvalidArgumentException('Cannot reorder children, order does not contain all children.');
        }

        $newChildren = array();

        foreach($order as $name) {
            if (!isset($this->children[$name])) {
                throw new \InvalidArgumentException('Cannot find children named '.$name);
            }

            $child = $this->children[$name];
            $newChildren[$name] = $child;
        }

        $this->children = $newChildren;
        $this->resetChildrenNum();
    }

    /**
     * Makes a deep copy of menu tree. Every item is copied as another object.
     *
     * @return MenuItem
     */
    public function copy()
    {
        $newMenu = clone $this;
        $newMenu->children = array();
        $newMenu->setParent(null);
        foreach($this->getChildren() as $child) {
            $newMenu->addChild($child->copy());
        }

        return $newMenu;
    }

    /**
     * Get slice of menu as another menu.
     *
     * If offset and/or length are numeric, it works like in array_slice function:
     *
     *   If offset is non-negative, slice will start at the offset.
     *   If offset is negative, slice will start that far from the end.
     *
     *   If length is zero, slice will have all elements.
     *   If length is positive, slice will have that many elements.
     *   If length is negative, slice will stop that far from the end.
     *
     * It's possible to mix names/object/numeric, for example:
     *   slice("child1", 2);
     *   slice(3, $child5);
     *
     * @param mixed $offset Name of child, child object, or numeric offset.
     * @param mixed $length Name of child, child object, or numeric length.
     * @return MenuItem Slice of menu.
     */
    public function slice($offset, $length = 0)
    {
        $count = $this->count();

        $names = array_keys($this->getChildren());
        if (is_numeric($offset)) {
            $offset = ($offset >= 0) ? $offset : $count + $offset;
            $from = (isset($names[$offset])) ? $names[$offset] : "";
        }
        else {
            $child = ($offset instanceof MenuItem) ? $offset : $this->getChild($offset);
            $offset = ($child) ? $child->getNum() : 0;
            $from = ($child) ? $child->getName() : "";
        }

        if (is_numeric($length)) {
            if ($length == 0) {
                $offset2 = $count - 1;
            }
            else {
                $offset2 = ($length > 0) ? $offset + $length - 1 : $count - 1 + $length;
            }
            $to = (isset($names[$offset2])) ? $names[$offset2] : "";
        }
        else {
            $to = ($length instanceof MenuItem) ? $length->getName() : $length;
        }

        return $this->sliceFromTo($from, $to);
    }

    /**
     * Get slice of menu as another menu.
     *
     * Internal method.
     *
     * @param string $offset Name of child.
     * @param string $length Name of child.
     * @return MenuItem
     */
    private function sliceFromTo($from, $to)
    {
        $newMenu = $this->copy();
        $newChildren = array();

        $copy = false;
        foreach($newMenu->getChildren() as $child) {
            if ($child->getName() == $from) {
                $copy = true;
            }

            if ($copy == true) {
                $newChildren[$child->getName()] = $child;
            }

            if ($child->getName() == $to) {
                break;
            }
        }

        $newMenu->setChildren($newChildren);
        $newMenu->resetChildrenNum();

        return $newMenu;
    }

    /**
     * Split menu into two distinct menus.
     * 
     * @param mixed $length Name of child, child object, or numeric length.
     * @return array Array with two menus, with "primary" and "secondary" key
     */
    public function split($length)
    {
        $count = $this->count();

        if (!is_numeric ($length)) {
            if (!($length instanceof MenuItem)) {
                $length = $this->getChild($length);
            }

            $length = ($length != null) ? $length->getNum() + 1 : $count;
        }

        $ret = array();
        $ret['primary'] = $this->slice(0, $length);
        $ret['secondary'] = $this->slice($length);

        return $ret;
    }

    /**
     * Returns the level of this menu item
     *
     * The root menu item is 0, followed by 1, 2, etc
     *
     * @return integer
     */
    public function getLevel()
    {
        $count = -1;
        $obj = $this;

        do {
            $count++;
        }
        while ($obj = $obj->getParent());

        return $count;
    }

    /**
     * Returns the root MenuItem of this menu tree
     *
     * @return MenuItem
     */
    public function getRoot()
    {
        $obj = $this;
        do {
            $found = $obj;
        }
        while ($obj = $obj->getParent());

        return $found;
    }

    /**
     * Returns whether or not this menu item is the root menu item
     *
     * @return bool
     */
    public function isRoot()
    {
        return (bool) !$this->getParent();
    }

    /**
     * @return MenuItem|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Used internally when adding and removing children
     *
     * @param MenuItem $parent
     * @return MenuItem
     */
    public function setParent(MenuItem $parent = null)
    {
        return $this->parent = $parent;
    }

    /**
     * @return array of MenuItem objects
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param  array $children An array of MenuItem objects
     * @return MenuItem
     */
    public function setChildren(array $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Sets the array of options to use when running url_for()
     *
     * @param  array $options The array of options to set
     * @return void
     */
    public function setUrlOptions(array $options)
    {
        $this->urlOptions = $options;
    }

    /**
     * @return array
     */
    public function getUrlOptions()
    {
        return $this->urlOptions;
    }

    /**
     * @return array
     */
    public function getLinkOptions()
    {
        return $this->linkOptions;
    }

    /**
     * The options that will be used in the link_to() function for this menu item.
     *
     * @param  $linkOptions The options to set
     * @return void
     */
    public function setLinkOptions($linkOptions)
    {
        $this->linkOptions = $linkOptions;
    }


    /**
     * Returns the index that this child is within its parent.
     *
     * Primarily used internally to calculate first and last
     *
     * @return integer
     */
    public function getNum()
    {
        return $this->num;
    }

    /**
     * Sets the index that this child is within its parent.
     *
     * Primarily used internally to calculate first and last
     *
     * @return void
     */
    public function setNum($num)
    {
        $this->num = $num;
    }

    /**
     * Reset children nums.
     *
     * Primarily called after changes to children (removing, reordering, etc)
     * 
     * @return void
     */
    protected function resetChildrenNum()
    {
        $i = 0;
        foreach ($this->children as $child) {
            $child->setNum($i++);
        }
    }

    /**
     * Creates a new MenuItem to be the child of this menu
     * 
     * @param string  $name
     * @param string  $uri
     * @param array   $attributes
     * 
     * @return MenuItem
     */
    protected function createChild($name, $uri = null, $attributes = array(), $class = null)
    {
        if ($class === null) {
            $class = get_class($this);
        }

        return new $class($name, $uri, $attributes);
    }

    /**
     * Removes a child from this menu item
     * 
     * @param mixed $name The name of MenuItem instance to remove
     */
    public function removeChild($name)
    {
        $name = ($name instanceof MenuItem) ? $name->getName() : $name;

        if (isset($this->children[$name])) {
            // unset the child and reset it so it looks independent
            $this->children[$name]->setParent(null);
            $this->children[$name]->setNum(null);
            unset($this->children[$name]);

            $this->resetChildrenNum();
        }
    }

    /**
     * @return MenuItem
     */
    public function getFirstChild()
    {
        return reset($this->children);
    }

    /**
     * @return MenuItem
     */
    public function getLastChild()
    {
        return end($this->children);
    }

    /**
     * Returns whether or not this menu items has viewable children
     *
     * This menu MAY have children, but this will return false if the current
     * user does not have access to vew any of those items
     *
     * @return boolean;
     */
    public function hasChildren()
    {
        foreach ($this->children as $child) {
            if ($child->shouldBeRendered()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Renders a ul tag and any children inside li tags.
     *
     * Depth values corresppond to:
     *   * 0 - no children displayed at all (would return a blank string)
     *   * 1 - directly children only
     *   * 2 - children and grandchildren
     *
     * @param integer $depth         The depth of children to render 
     * @param boolean $renderAsChild Used internally to render with attributes on the write element
     * 
     * @return string
     */
    public function render($depth = null, $renderAsChild = false)
    {
        /**
         * Return an empty string if any of the following are true:
         *   a) The menu has no children eligible to be displayed
         *   b) The depth is 0
         *   c) This menu item has been explicitly set to hide its children
         */
        if (!$this->hasChildren() || $depth === 0 || !$this->getShowChildren()) {
            return '';
        }

        if ($renderAsChild) {
            $attributes = array('class' => 'menu_level_'.$this->getLevel());
        }
        else
        {
            $attributes = $this->getAttributes();
            // give the top ul a class of "menu" of none specified
            if (!isset($attributes['class'])) {
                $attributes['class'] = 'menu';
            }
        }

        // render children with a depth - 1
        $childDepth = ($depth === null) ? null : ($depth - 1);

        $html = $this->format('<ul'.$this->renderHtmlAttributes($attributes).'>', 'ul');
        $html .= $this->renderChildren($childDepth);
        $html .= $this->format('</ul>', 'ul');

        return $html;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Renders all of the children of this menu.
     *
     * This calls ->renderChild() on each menu item, which instructs each
     * menu item to render themselves as an <li> tag (with nested ul if it
     * has children).
     *
     * @param integer $depth The depth each child should render
     * @return string
     */
    public function renderChildren($depth = null)
    {  
        $html = '';
        foreach ($this->children as $child) {
            $html .= $child->renderChild($depth);
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
    public function renderChild($depth = null)
    {
        // if we don't have access or this item is marked to not be shown
        if (!$this->shouldBeRendered()) {
            return; 
        }

        // explode the class string into an array of classes
        $class = ($this->getAttribute('class')) ? explode(' ', $this->getAttribute('class')) : array();

        if ($this->getIsCurrent()) {
            $class[] = 'current';
        }
        elseif ($this->getIsCurrentAncestor($depth)) {
            $class[] = 'current_ancestor';
        }

        if ($this->actsLikeFirst()) {
            $class[] = 'first';
        }
        if ($this->actsLikeLast()) {
            $class[] = 'last';
        }

        // retrieve the attributes and put the final class string back on it
        $attributes = $this->getAttributes();
        if (!empty($class)) {
            $attributes['class'] = implode(' ', $class);
        }

        // opening li tag
        $html = $this->format('<li'.$this->renderHtmlAttributes($attributes).'>', 'li');

        // render the text/link inside the li tag
        $html .= $this->format($this->uri ? $this->renderLink() : $this->renderLabel(), 'link');

        // renders the embedded ul if there are visible children
        $html .= $this->render($depth, true);

        // closing li tag
        $html .= $this->format('</li>', 'li');

        return $html;
    }

    /**
     * If self::$renderCompressed is on, this will apply the necessary
     * spacing and line-breaking so that the particular thing being rendered
     * makes up its part in a fully-rendered and spaced menu.
     *
     * @param  string $html The html to render in an (un)formatted way
     * @param  string $type The type [ul,link,li] of thing being rendered 
     * @return string
     */
    protected function format($html, $type)
    {
        if (self::$renderCompressed) {
            return $html;
        }

        switch ($type) {
        case 'ul':
        case 'link':
            $spacing = $this->getLevel() * 4;
            break;

        case 'li':
            $spacing = $this->getLevel() * 4 - 2;
            break;
        }

        return str_repeat(' ', $spacing).$html."\n";
    }

    /**
     * Render a HTML attribute
     */
    public function renderHtmlAttribute($name, $value)
    {
        if (true === $value) {
            return sprintf('%s="%s"', $name, $this->escape($name));
        } else {
            return sprintf('%s="%s"', $name, $this->escape($value));
        }
    }

    /**
     * Render HTML atributes
     */
    public function renderHTMLAttributes(array $attributes)
    {
        return implode('', array_map(array($this, 'htmlAttributesCallback'), array_keys($attributes), array_values($attributes)));
    }

    /**
     * Prepares an attribute key and value for HTML representation.
     *
     * It removes empty attributes.
     *
     * @param  string $name   The attribute name
     * @param  string $value  The attribute value
     *
     * @return string The HTML representation of the HTML key attribute pair.
     */
    private function htmlAttributesCallback($name, $value)
    {
        if (false === $value || null === $value) {
            return '';
        } else {
            return ' '.$this->renderHtmlAttribute($name, $value);
        }
    }

    /**
     * Escape an HTML value
     */
    public function escape($value)
    {
        return $this->fixDoubleEscape(htmlspecialchars((string) $value, ENT_QUOTES, self::$charset));
    }

    /**
     * Fixes double escaped strings.
     *
     * @param  string $escaped  string to fix
     * @return string A single escaped string
     */
    protected function fixDoubleEscape($escaped)
    {
        return preg_replace('/&amp;([a-z]+|(#\d+)|(#x[\da-f]+));/i', '&$1;', $escaped);
    }

    /**
     * Renders the anchor tag for this menu item.
     *
     * If no uri is specified, or if the uri fails to generate, the
     * label will be output.
     *
     * @return string
     */
    public function renderLink()
    {
        if (!$uri = $this->getUri()) {
            return $this->renderLabel();
        }

        // Handling of the url options and link options varies depending on the url format
        if ($this->isOldUriMethod()) {
            // old-school link_to('link text', '@uri_name', $options);
            return link_to($this->renderLabel(), $this->getUri(), array_merge($this->getUrlOptions(), $this->getLinkOptions()));
        }
        else
        {
            // new-school link_to('link text', 'uri_name', $params, $options)
            $params = $this->getUrlOptions();
            $options = $this->getLinkOptions();
            if (isset($params['absolute']))
            {
                $options['absolute'] = $params['absolute'];
                unset($params['absolute']);
            }

            return link_to($this->renderLabel(), $this->getUri(), $params, $options);
        }
    }

    /**
     * Renders the label of this menu
     *
     * @return string
     */
    public function renderLabel()
    {
        return $this->getLabel();
    }

    /**
     * A string representation of this menu item
     *
     * e.g. Top Level > Second Level > This menu
     *
     * @param string $separator
     * @return string
     */
    public function getPathAsString($separator = ' > ')
    {
        $children = array();
        $obj = $this;

        do {
            $children[] = $obj->renderLabel();
        }
        while ($obj = $obj->getParent());

        return implode($separator, array_reverse($children));
    }

    /**
     * Renders an array of label => uri pairs ready to be used for breadcrumbs.
     *
     * The subItem can be one of the following forms
     *   * 'subItem'
     *   * array('subItem' => '@homepage')
     *   * array('subItem1', 'subItem2')
     *
     * @example
     * // drill down to the Documentation menu item, then add "Chapter 1" to the breadcrumb
     * $arr = $menu['Documentation']->getBreadcrumbsArray('Chapter 1');
     * foreach ($arr as $name => $url)
     * {
     *
     * }
     *
     * @param  mixed $subItem A string or array to append onto the end of the array
     * @return array
     */
    public function getBreadcrumbsArray($subItem = null)
    {
        $breadcrumbs = array();
        $obj = $this;

        if ($subItem) {
            if (!is_array($subItem)) {
                $subItem = array((string) $subItem => null);
            }
            $subItem = array_reverse($subItem);
            foreach ($subItem as $key => $value) {
                if (is_numeric($key)) {
                    $key = $value;
                    $value = null;
                }
                $breadcrumbs[(string) $key] = $value;
            }
        }

        do {
            $label = $obj->renderLabel();
            $breadcrumbs[$label] = $obj->getUri();
        }
        while ($obj = $obj->getParent());

        return array_reverse($breadcrumbs);
    }

    /**
     * Returns the current menu item if it is a child of this menu item
     *
     * @return bool|MenuItem
     */
    public function getCurrent()
    {
        if ($this->getIsCurrent()) {
            return $this;
        }

        foreach ($this->children as $child) {
            if ($current = $child->getCurrent()) {
                return $current;
            }
        }

        return false;
    }

    /**
     * Set whether or not this menu item is "current"
     *
     * @param boolean $bool Specify that this menu item is current
     * @return boolean
     */
    public function setIsCurrent($bool)
    {
        $this->isCurrent = (bool) $bool;

        return $this;
    }

    /**
     * Get whether or not this menu item is "current" 
     * 
     * @return bool
     */
    public function getIsCurrent()
    {
        if (null === $this->isCurrent) {
            $currentUri = $this->getCurrentUri();
            $this->isCurrent = null !== $currentUri && ($this->getUri() === $currentUri);
        }

        return $this->isCurrent;
    }

    /**
     * Returns whether or not this menu is an ancestor of the current menu item
     *
     * @return boolean
     */
    public function getIsCurrentAncestor($depth = null)
    {
        // if children not shown, then we're definitely not a visible ancestor
        if (!$this->getShowChildren() || $depth === 0) {
            return false;
        }

        foreach ($this->getChildren() as $child) {
            if ($child->getIsCurrent() || $child->getIsCurrentAncestor($depth - 1)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool Whether or not this menu item is last in its parent
     */
    public function isLast()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return $this->getNum() == $this->getParent()->count() - 1 ? true : false;
    }

    /**
     * @return bool Whether or not this menu item is first in its parent 
     */
    public function isFirst()
    {
        // if this is root, then return false
        if ($this->isRoot()) {
            return false;
        }

        return ($this->getNum() == 0);
    }

    /**
     * Whereas isFirst() returns if this is the first child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the first child that would be rendered
     * for the current user 
     *
     * @return boolean
     */
    public function actsLikeFirst()
    {
        // root items are never "marked" as first 
        if ($this->isRoot()) {
            return false;
        }

        // if we're first and visible, we're first, period.
        if ($this->shouldBeRendered() && $this->isFirst()) {
            return true;
        }

        $children = $this->getParent()->getChildren();
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->shouldBeRendered()) {
                return $child->getName() == $this->getName();
            }
        }

        return false;
    }

    /**
     * Whereas isLast() returns if this is the last child of the parent
     * menu item, this function takes into consideration whether children are rendered or not.
     *
     * This returns true if this is the last child that would be rendered
     * for the current user
     *
     * @return boolean
     */
    public function actsLikeLast()
    {
        // root items are never "marked" as last
        if ($this->isRoot()) {
            return false;
        }

        // if we're last and visible, we're last, period.
        if ($this->shouldBeRendered() && $this->isLast()) {
            return true;
        }

        $children = array_reverse($this->getParent()->getChildren());
        foreach ($children as $child) {
            // loop until we find a visible menu. If its this menu, we're first
            if ($child->shouldBeRendered()) {
                return $child->getName() == $this->getName();
            }
        }

        return false;
    }

    /**
     * Returns the current uri, which is used for determining the current
     * menu item.
     *
     * If the uri isn't set, this asks the parent menu for its current uri.
     * This would recurse up the tree until the root is hit. Once the root
     * is hit, if it still doesn't know the currentUri, it gets it from the
     * request object.
     *
     * @return string
     */
    public function getCurrentUri()
    {
        if ($this->currentUri === null) {
            if ($this->getParent() && ($currentUri = $this->getParent()->getCurrentUri())) {
                /**
                 * This should look strange. But, if we ask our parent for the
                 * current uri, and it returns it successfully, then one of two
                 * different things just happened:
                 * 
                 *   1) The parent already had the currentUri calculated, but it
                 *      hadn't been passed down to the child yet. This technically
                 *      should not happen, but we allow for the possibility. In
                 *      that case, currentUri is still blank and we set it here.
                 *   2) The parent did not have the currentUri calculated, and upon
                 *      calculating it, it set it on itself and all of its children.
                 *      In that case, this menu item and all of its children will
                 *      now have the currentUri just by asking the parent.
                 */
                if ($this->currentUri === null) {
                    $this->setCurrentUri($currentUri);
                }
            }
        }

        return $this->currentUri;
    }

    /**
     * Sets the current uri, used when determining the current menu item
     *
     * This will set the current uri on the root menu item, which all other
     * menu items will use
     *
     * @return void
     */
    public function setCurrentUri($uri)
    {
        $this->currentUri = $uri;

        foreach ($this->getChildren() as $child) {
            $child->setCurrentUri($uri);
        }
    }

    /**
     * Calls a method recursively on all of the children of this item
     *
     * @example
     * $menu->callRecursively('setShowChildren', false);
     *
     * @return MenuItem
     */
    public function callRecursively()
    {
        $args = func_get_args();
        $arguments = $args;
        unset($arguments[0]);

        call_user_func_array(array($this, $args[0]), $arguments);

        foreach ($this->children as $child) {
            call_user_func_array(array($child, 'callRecursively'), $args);
        }

        return $this;
    }

    /**
     * Exports this menu item to an array
     *
     * @param boolean $withChildren Whether to
     * @return array
     */
    public function toArray($withChildren = true)
    {
        $fields = array(
            'name'           => 'name',
            'label'          => 'label',
            'uri'          => 'uri',
            'attributes'     => 'attributes'
        );

        $array = array();

        foreach ($fields as $propName => $field) {
            $array[$field] = $this->$propName;
        }

        // record this class name so this item can be recreated with the same class
        $array['class'] = get_class($this);

        // export the children as well, unless explicitly disabled
        if ($withChildren) {
            $array['children'] = array();
            foreach ($this->children as $key => $child) {
                $array['children'][$key] = $child->toArray();
            }
        }

        return $array;
    }

    /**
     * Imports a menu item array into this menu item
     *
     * @param  array $array The menu item array
     * @return MenuItem
     */
    public function fromArray($array)
    {
        if (isset($array['name'])) {
            $this->setName($array['name']);
        }

        if (isset($array['label'])) {
            $this->label = $array['label'];
        }

        if (isset($array['uri'])) {
            $this->setUri($array['uri']);
        }

        if (isset($array['attributes'])) {
            $this->setAttributes($array['attributes']);
        }

        if (isset($array['children'])) {
            foreach ($array['children'] as $name => $child) {
                $class = isset($child['class']) ? $child['class'] : get_class($this);
                // create the child with the correct class
                $this->addChild($name, null, array(), $class)->fromArray($child);
            }
        }

        return $this;
    }

    /**
     * Creates a new menu item (and tree if $data['children'] is set).
     *
     * The source is an array of data that should match the output from ->toArray().
     *
     * @param  array $data The array of data to use as a source for the menu tree 
     * @return MenuItem
     */
    public static function createFromArray(array $data)
    {
        $class = isset($data['class']) ? $data['class'] : 'MenuItem';

        $name = isset($data['name']) ? $data['name'] : null;
        $menu = new $class($name);
        $menu->fromArray($data);

        return $menu;
    }

    /**
     * Returns whether or not the uri method used is in the old format
     * or the new format.
     *
     * This affects how we generate urls and links
     *
     * @return bool
     */
    protected function isOldUriMethod()
    {
        return ('@' == substr($this->getUri(), 0, 1) || false !== strpos($this->getUri(), '/'));
    }

    /**
     * Implements Countable
     */
    public function count()
    {
        return count($this->children);
    }

    /**
     * Implements IteratorAggregate
     */
    public function getIterator()
    {
        return new \ArrayObject($this->children);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetExists($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetGet($name)
    {
        return $this->getChild($name);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetSet($name, $value)
    {
        return $this->addChild($name)->setLabel($value);
    }

    /**
     * Implements ArrayAccess
     */
    public function offsetUnset($name)
    {
        $this->removeChild($name);
    }

    /**
     * Get whether to render compressed HTML or not
     * 
     * @return bool
     */
    public static function getRenderCompressed()
    {
        return self::$renderCompressed;
    }

    /**
     * Set whether to render compressed HTML or not
     */
    public static function setRenderCompressed($bool)
    {
        self::$renderCompressed = (bool) $bool;
    }

    /**
     * Get the HTML charset
     * 
     * @return string
     */
    public static function getCharset()
    {
        return self::$charset;
    }

    /**
     * Set the HTML charset
     */
    public static function setCharset($string)
    {
        self::$renderCompressed = (string) $bool;
    }
}
