<?php

namespace FOS\MessageBundle\Tests\Functional;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use FOS\MessageBundle\Tests\Functional\Entity\Message;
use FOS\MessageBundle\Tests\Functional\Entity\Thread;
use PHPUnit\Framework\TestCase;

class FunctionalTest extends WebTestCase
{
    private $client;

    public function setUp()
    {
        $this->client = self::createClient();
        $container = static::$kernel->getContainer();

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $builder = $this->getMockBuilder(EntityRepository::class)->disableOriginalConstructor();
        $messageRepository = $builder->getMock();
        $threadRepository = $builder->getMock();

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValueMap(
                array(
                    array(Message::class, $messageRepository),
                    array(Thread::class, $threadRepository),
                )
            ));
        $em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnCallback(function ($class) {
                return (object) ['name' => $class];
            }));

        $container->set('doctrine.orm.entity_manager', $em);
    }

    public function testController()
    {
        $crawler = $this->client->request('GET', '/new');
    }
}
