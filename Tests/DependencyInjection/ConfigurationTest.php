<?php

namespace Knp\Bundle\MenuBundle\Tests\DependencyInjection;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getConfigs
     */
    public function testConfigurationMatchesXsd($config)
    {
        $configDom = new \DOMDocument();
        $configDom->loadXML($config);

        $previousErrorSetting = libxml_use_internal_errors(true);

        $configIsValid = $configDom->schemaValidate(__DIR__.'/../../Resources/config/schema/menu-1.0.xsd');
        $errors = array_map(function ($error) {
            return sprintf('Line %d: %s', $error->line, trim($error->message));
        }, libxml_get_errors());

        libxml_use_internal_errors($previousErrorSetting);

        $this->assertTrue($configIsValid, implode(PHP_EOL, $errors));
    }

    public function getConfigs()
    {
        return array(
            array('<config xmlns="http://knplabs.com/schema/dic/menu"/>'),
            array(<<<EOC
<config xmlns="http://knplabs.com/schema/dic/menu" templating="true" default-renderer="templating">
    <providers builder-alias="false" container-aware="false" builder-service="false"/>
    <twig template="custom.html.twig"/>
</config>
EOC
            ),
        );
    }
}
