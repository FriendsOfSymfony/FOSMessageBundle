<?php

namespace FOS\MessageBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class FOSMessageExtension extends Extension
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
        $loader->load('form.xml');
        $loader->load('validator.xml');
        $loader->load('spam_detection.xml');

        $container->setParameter('fos_message.message_class', $config['message_class']);
        $container->setParameter('fos_message.thread_class', $config['thread_class']);

        $container->setParameter('fos_message.new_thread_form.model', $config['new_thread_form']['model']);
        $container->setParameter('fos_message.new_thread_form.name', $config['new_thread_form']['name']);
        $container->setParameter('fos_message.reply_form.model', $config['reply_form']['model']);
        $container->setParameter('fos_message.reply_form.name', $config['reply_form']['name']);

        $container->setAlias('fos_message.message_manager', new Alias($config['message_manager'], true));
        $container->setAlias('fos_message.thread_manager', new Alias($config['thread_manager'], true));

        $container->setAlias('fos_message.sender', new Alias($config['sender'], true));
        $container->setAlias('fos_message.composer', new Alias($config['composer'], true));
        $container->setAlias('fos_message.provider', new Alias($config['provider'], true));
        $container->setAlias('fos_message.participant_provider', new Alias($config['participant_provider'], true));
        $container->setAlias('fos_message.authorizer', new Alias($config['authorizer'], true));
        $container->setAlias('fos_message.message_reader', new Alias($config['message_reader'], true));
        $container->setAlias('fos_message.thread_reader', new Alias($config['thread_reader'], true));
        $container->setAlias('fos_message.deleter', new Alias($config['deleter'], true));
        $container->setAlias('fos_message.spam_detector', new Alias($config['spam_detector'], true));
        $container->setAlias('fos_message.twig_extension', new Alias($config['twig_extension'], true));

        // BC management
        $newThreadFormType = $config['new_thread_form']['type'];
        $replyFormType = $config['reply_form']['type'];

        if (!class_exists($newThreadFormType)) {
            @trigger_error('Using a service reference in configuration key "fos_message.new_thread_form.type" is deprecated since version 1.2 and will be removed in 2.0. Use the class name of your form type instead.', E_USER_DEPRECATED);

            // Old syntax (service reference)
            $container->setAlias('fos_message.new_thread_form.type', new Alias($newThreadFormType, true));
        } else {
            // New syntax (class name)
            $container->getDefinition('fos_message.new_thread_form.factory.default')
                ->replaceArgument(1, $newThreadFormType);
        }

        if (!class_exists($replyFormType)) {
            @trigger_error('Using a service reference in configuration key "fos_message.reply_form.type" is deprecated since version 1.2 and will be removed in 2.0. Use the class name of your form type instead.', E_USER_DEPRECATED);

            // Old syntax (service reference)
            $container->setAlias('fos_message.reply_form.type', new Alias($replyFormType, true));
        } else {
            // New syntax (class name)
            $container->getDefinition('fos_message.reply_form.factory.default')
                ->replaceArgument(1, $replyFormType);
        }

        $container->setAlias('fos_message.new_thread_form.factory', new Alias($config['new_thread_form']['factory'], true));
        $container->setAlias('fos_message.new_thread_form.handler', new Alias($config['new_thread_form']['handler'], true));
        $container->setAlias('fos_message.reply_form.factory', new Alias($config['reply_form']['factory'], true));
        $container->setAlias('fos_message.reply_form.handler', new Alias($config['reply_form']['handler'], true));

        $container->setAlias('fos_message.search_query_factory', new Alias($config['search']['query_factory'], true));
        $container->setAlias('fos_message.search_finder', new Alias($config['search']['finder'], true));
        $container->getDefinition('fos_message.search_query_factory.default')
            ->replaceArgument(1, $config['search']['query_parameter']);

        $container->getDefinition('fos_message.recipients_data_transformer')
            ->replaceArgument(0, new Reference($config['user_transformer']));
    }
}
