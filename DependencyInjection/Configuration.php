<?php

namespace Ornicar\MessageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class defines the configuration information for the bundle
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_user');

        $rootNode
            ->children()
                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('thread_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('message_class')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('message_manager')->defaultValue('ornicar_message.message_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('thread_manager')->defaultValue('ornicar_message.thread_manager.default')->cannotBeEmpty()->end()
                ->scalarNode('sender')->defaultValue('ornicar_message.sender.default')->cannotBeEmpty()->end()
                ->scalarNode('composer')->defaultValue('ornicar_message.composer.default')->cannotBeEmpty()->end()
                ->scalarNode('provider')->defaultValue('ornicar_message.provider.default')->cannotBeEmpty()->end()
                ->scalarNode('authorizer')->defaultValue('ornicar_message.authorizer.default')->cannotBeEmpty()->end()
            ->end();

        $this->addFormSection($rootNode);

        return $treeBuilder;
    }

    private function addFormSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('type')->defaultValue('ornicar_message.message_form_type')->end()
                        ->scalarNode('handler')->defaultValue('ornicar_message.message_form_handler')->end()
                    ->end()
                ->end()
            ->end();
    }
}
