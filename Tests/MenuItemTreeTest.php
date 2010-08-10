<?php

namespace Bundle\MenuBundle\Tests;
use Bundle\MenuBundle\MenuItem;

class MenuItemTreeTest extends \PHPUnit_Framework_TestCase
{
        
    /**
     * Create a new MenuItem 
     * 
     * @param string $name 
     * @param string $route 
     * @param array $attributes 
     * @return MenuItem
     */
    protected function createMenu($name = 'test_menu', $route = 'homepage', array $attributes = array())
    {
        return new MenuItem($name, $route, $attributes);
    }
}
