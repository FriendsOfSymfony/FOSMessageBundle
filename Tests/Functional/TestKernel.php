<?php

namespace FOS\MessageBundle\Tests\Functional;

use FOS\MessageBundle\Tests\Functional\Form\UserToUsernameTransformer;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use FOS\MessageBundle\FOSMessageBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use FOS\MessageBundle\Tests\Functional\Entity\Thread;
use FOS\MessageBundle\Tests\Functional\Entity\Message;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * @author Guilhem N. <guilhem.niot@gmail.com>
 */
class TestKernel extends Kernel
{
    use MicroKernelTrait;

    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new TwigBundle(),
            new FOSMessageBundle(),
        ];

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
        $routes->import('@FOSMessageBundle/Resources/config/routing.xml');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader)
    {
        $c->loadFromExtension('framework', [
            'secret' => 'MySecretKey',
            'test' => null,
            'templating' => [
                'engines' => ['twig'],
            ],
        ]);

        $c->loadFromExtension('security', [
            'providers' => ['in_memory' => ['memory' => null]],
            'firewalls' => ['main' => ['security' => false]],
        ]);

        $c->loadFromExtension('twig', [
            'strict_variables' => '%kernel.debug%',
        ]);

        $c->loadFromExtension('fos_message', [
            'db_driver' => 'orm',
            'thread_class' => Thread::class,
            'message_class' => Message::class,
        ]);

        $c->register('doctrine.orm.entity_manager', EntityManager::class)->setSynthetic(true);
        $c->register('fos_user.user_to_username_transformer', UserToUsernameTransformer::class);
    }
}
