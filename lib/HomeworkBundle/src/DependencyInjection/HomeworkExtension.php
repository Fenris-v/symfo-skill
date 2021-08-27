<?php

namespace SymfonySkillbox\HomeworkBundle\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class HomeworkExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__) . '/Resources/config')
        );

        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);

        $config = $this->processConfiguration($configuration, $configs);

        $definition = $container->getDefinition('symfony_skillbox_homework.unit_factory');

        if (null !== $config['strategy']) {
            $container->setAlias('symfony_skillbox_homework.strategy', $config['strategy']);
        }

        $definition->setArgument(1, $config['enableSolder']);
        $definition->setArgument(2, $config['enableArcher']);
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return 'unit_factory';
    }
}
