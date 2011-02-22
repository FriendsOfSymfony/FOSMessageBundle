<?php

namespace Bundle\Ornicar\MessageBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class OrnicarMessageExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        foreach (array('model', 'controller', 'form', 'twig', 'messenger', 'paginator') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        $config = array();
        foreach ($configs as $c) {
            $config = array_merge($config, $c);
        }

        // ensure the db_driver is configured
        if (!isset($config['db_driver'])) {
            throw new \InvalidArgumentException('The db_driver parameter must be defined');
        } elseif (!in_array($config['db_driver'], array('orm', 'odm'))) {
            throw new \InvalidArgumentException(sprintf('The db_driver "%s" is not supported (choose either "odm" or "orm")', $config['db_driver']));
        }
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        // ensure the user model class is configured
        if (!isset($config['class']['model']['message'])) {
            throw new \InvalidArgumentException('The message model class must be defined');
        }

        $this->remapParametersNamespaces($config, $container, array(
            'form_name' => 'ornicar_message.form.%s.name',
            'paginator' => 'ornicar_message.paginator.%s'
        ));

        $this->remapParametersNamespaces($config['class'], $container, array(
            'model'         => 'ornicar_message.model.%s.class',
            'form'          => 'ornicar_message.form.%s.class',
            'controller'    => 'ornicar_message.controller.%s.class'
        ));
    }

    protected function remapParameters(array $config, ContainerBuilder $container, array $map)
    {
        foreach ($map as $name => $paramName) {
            if (isset($config[$name])) {
                $container->setParameter($paramName, $config[$name]);
            }
        }
    }

    protected function remapParametersNamespaces(array $config, ContainerBuilder $container, array $namespaces)
    {
        foreach ($namespaces as $ns => $map) {
            if ($ns) {
                if (!isset($config[$ns])) {
                    continue;
                }
                $namespaceConfig = $config[$ns];
            } else {
                $namespaceConfig = $config;
            }
            if (is_array($map)) {
                $this->remapParameters($namespaceConfig, $container, $map);
            } else {
                foreach ($namespaceConfig as $name => $value) {
                    if (null !== $value) {
                        $container->setParameter(sprintf($map, $name), $value);
                    }
                }
            }
        }
    }
}
