<?php

namespace Ornicar\MessageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class OrnicarMessageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!in_array(strtolower($config['db_driver']), array('orm', 'mongodb'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load(sprintf('%s.xml', $config['db_driver']));
        $loader->load('config.xml');

        $container->setParameter('ornicar_message.message_class', $config['message_class']);
        $container->setParameter('ornicar_message.thread_class', $config['thread_class']);

        $container->setAlias('ornicar_message.message_manager', $config['message_manager']);
        $container->setAlias('ornicar_message.thread_manager', $config['thread_manager']);

        $container->setAlias('ornicar_message.sender', $config['sender']);
        $container->setAlias('ornicar_message.composer', $config['composer']);
        $container->setAlias('ornicar_message.provider', $config['provider']);
        $container->setAlias('ornicar_message.authorizer', $config['authorizer']);
    }
}
