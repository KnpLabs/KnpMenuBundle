<?php

namespace Knp\Bundle\MenuBundle\Tests\Extension;

use Knp\Bundle\MenuBundle\Extension\DefaultOptionsExtension;

class DefaultOptionsExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testSetsDefaultOptions()
    {
        $extension = new DefaultOptionsExtension(array(
            'attributes' => array('class' => 'menu-item'),
            'displayChildren' => false,
        ));

        $this->assertEquals(array(
            'attributes' => array('class' => 'menu-item'),
            'displayChildren' => false,
        ), $extension->buildOptions());
    }

    public function testExplicitelyPassedOptionsOverwriteConfiguredDefaults()
    {
        $extension = new DefaultOptionsExtension(array(
            'attributes' => array('class' => 'menu-item'),
            'displayChildren' => false,
        ));

        $this->assertEquals(array(
            'attributes' => array('class' => 'menu-item'),
            'displayChildren' => true,
        ), $extension->buildOptions(array('displayChildren' => true)));
    }
}
