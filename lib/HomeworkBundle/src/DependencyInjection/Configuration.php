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
            ->booleanNode('enableSolder')
            ->defaultTrue()
            ->info('Enable solder')
            ->end()
            ->booleanNode('enableArcher')
            ->defaultTrue()
            ->info('Enable archer');

        return $treeBuilder;
    }
}
