<?php

namespace Fonsecas72\GiffyExtension\ServiceContainer\Driver;

use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Symfony\Component\DependencyInjection\Definition;
use Behat\MinkExtension\ServiceContainer\Driver\Selenium2Factory;

final class GiffyFactory extends Selenium2Factory implements DriverFactory
{
    public function getDriverName()
    {
        return 'giffy';
    }

    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\Selenium2Driver')) {
            throw new \RuntimeException(sprintf(
                'Install MinkSelenium2Driver in order to use %s driver.',
                $this->getDriverName()
            ));
        }

        $extraCapabilities = $config['capabilities']['extra_capabilities'];
        unset($config['capabilities']['extra_capabilities']);

        $guessedCapabilities = array(
            'tags' => array(php_uname('n'), 'PHP ' . phpversion()),
        );

        return new Definition('Fonsecas72\GiffyExtension\Driver\GiffyDriver', array(
            '%giffy.screenshot_path%',
            $config['browser'],
            array_replace($extraCapabilities, $guessedCapabilities, $config['capabilities']),
            $config['wd_host'],
        ));
    }
}
