<?php

namespace Bundle\Ornicar\MessageBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MessageExtension extends Extension
{
    public function configLoad(array $configs, ContainerBuilder $container)
    {
        foreach ($configs as $config) {
            $this->doConfigLoad($config, $container);
        }
    }

    public function doConfigLoad(array $config, ContainerBuilder $container)
    {
        if(!$container->hasDefinition('ornicar_message.repository.message')) {
            // ensure the db_driver is configured
            if (!isset($config['db_driver'])) {
                throw new \InvalidArgumentException('The db_driver parameter must be defined');
            } elseif (!in_array($config['db_driver'], array('orm', 'odm'))) {
                throw new \InvalidArgumentException(sprintf('The db_driver "%s" is not supported (choose either "odm" or "orm")', $config['db_driver']));
            }
            $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
            // load all service configuration files (the db_driver first)
            foreach (array($config['db_driver'], 'model', 'controller', 'form', 'twig', 'messenger', 'paginator') as $basename) {
                $loader->load(sprintf('%s.xml', $basename));
            }
        }

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

    /**
     * Returns the namespace to be used for this extension (XML namespace).
     *
     * @return string The XML namespace
     */
    public function getNamespace()
    {
        return 'http://www.symfony-project.org/shemas/dic/symfony';
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    public function getAlias()
    {
        return 'ornicar_message';
    }
}
