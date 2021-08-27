<?php

namespace SymfonySkillbox\HomeworkBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('unit_factory');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->children()
            ->scalarNode('strategy')
            ->defaultNull()
            ->info('Factory strategy')
            ->end()
            ->scalarNode('unit_provider')
            ->defaultNull()
            ->info('Units list');

        return $treeBuilder;
    }
}
