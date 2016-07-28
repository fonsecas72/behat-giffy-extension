<?php

namespace Fonsecas72\GiffyExtension\ServiceContainer;

use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Behat\Testwork\ServiceContainer\Extension;
use Fonsecas72\GiffyExtension\ServiceContainer\Driver\GiffyFactory;
use Symfony\Component\DependencyInjection\Reference;
use Behat\MinkExtension\ServiceContainer\MinkExtension;

class GiffyExtension implements Extension
{
    public function initialize(ExtensionManager $extensionManager)
    {
        if (null !== $minkExtension = $extensionManager->getExtension('mink')) {
            $minkExtension->registerDriverFactory(new GiffyFactory());
        }
    }
    
    public function load(ContainerBuilder $container, array $config)
    {
        $definition = new Definition('Fonsecas72\GiffyExtension\GiffyListener', array(
            new Reference(MinkExtension::MINK_ID),
            '%giffy.use_scenario_folder%'
        ));

        $definition->addTag(EventDispatcherExtension::SUBSCRIBER_TAG, array('priority' => 0));
        $container->setDefinition('mink.listener.giffy', $definition);
        $container->setParameter('giffy.screenshot_path', $config['screenshot_path']);
        $container->setParameter('giffy.use_scenario_folder', $config['use_scenario_folder']);
    }

    public function getConfigKey()
    {
        return 'giffy';
    }

    public function configure(ArrayNodeDefinition $builder)
    {
        $builder->children()
                    ->scalarNode('screenshot_path')
                    ->isRequired()
                    ->end()
                ->end()
                ->children()
                    ->booleanNode('use_scenario_folder')
                ->end();
    }
    public function process(ContainerBuilder $container){}
}
