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
        $rootNode = $treeBuilder->root('ornicar_message');

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
                ->scalarNode('message_reader')->defaultValue('ornicar_message.message_reader.default')->cannotBeEmpty()->end()
                ->scalarNode('thread_reader')->defaultValue('ornicar_message.thread_reader.default')->cannotBeEmpty()->end()
                ->scalarNode('deleter')->defaultValue('ornicar_message.deleter.default')->cannotBeEmpty()->end()
                ->arrayNode('new_thread_form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('factory')->defaultValue('ornicar_message.new_thread_form.factory.default')->cannotBeEmpty()->end()
                        ->scalarNode('type')->defaultValue('ornicar_message.new_thread_form.type.default')->cannotBeEmpty()->end()
                        ->scalarNode('handler')->defaultValue('ornicar_message.new_thread_form.handler.default')->cannotBeEmpty()->end()
                        ->scalarNode('name')->defaultValue('message')->cannotBeEmpty()->end()
                        ->scalarNode('model')->defaultValue('Ornicar\MessageBundle\FormModel\NewThreadMessage')->end()
                    ->end()
                ->end()
                ->arrayNode('reply_form')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('factory')->defaultValue('ornicar_message.reply_form.factory.default')->cannotBeEmpty()->end()
                        ->scalarNode('type')->defaultValue('ornicar_message.reply_form.type.default')->cannotBeEmpty()->end()
                        ->scalarNode('handler')->defaultValue('ornicar_message.reply_form.handler.default')->cannotBeEmpty()->end()
                        ->scalarNode('name')->defaultValue('message')->cannotBeEmpty()->end()
                        ->scalarNode('model')->defaultValue('Ornicar\MessageBundle\FormModel\ReplyMessage')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
