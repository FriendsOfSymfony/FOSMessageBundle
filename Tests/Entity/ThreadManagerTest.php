<?php

namespace FOS\MessageBundle\Tests\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use FOS\MessageBundle\EntityManager\ThreadManager;
use Mockery as m;

class ThreadManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FOS\MessageBundle\EntityManager\ThreadManager|\Mockery\Mock
     */
    private $manager;

    /**
     * @var \Doctrine\ORM\EntityManager|\Mockery\Mock
     */
    private $em;

    /**
     * @var \FOS\MessageBundle\EntityManager\ThreadDenormalizer|\Mockery\Mock
     */
    private $denormalizer;

    /**
     * @var \FOS\MessageBundle\EntityManager\MessageManager|\Mockery\Mock
     */
    private $messageManager;

    public function testCreateThread()
    {
        $thread = $this->manager->createThread();

        $this->assertInstanceOf('FOS\\MessageBundle\\Model\\ThreadInterface', $thread);
    }

    public function testSaveThread()
    {
        $thread = new Thread;

        $this->denormalizer->shouldReceive('denormalize')
            ->with($thread);
        $this->em->shouldReceive('persist')
            ->with($thread);
        $this->em->shouldReceive('flush');

        $this->manager->saveThread($thread);
    }

    public function testDeleteThread()
    {
        $thread = new Thread;

        $this->em->shouldReceive('remove')
            ->with($thread);
        $this->em->shouldReceive('flush');

        $this->manager->deleteThread($thread);
    }

    public function testMarkAsReadByParticipant()
    {
        $thread = new Thread;
        $participant = $this->getParticipant();

        $this->messageManager->shouldReceive('markIsReadByThreadAndParticipant')
            ->with($thread, $participant, true);

        $this->manager->markAsReadByParticipant($thread, $participant);
    }

    public function testMarkAsUnReadByParticipant()
    {
        $thread = new Thread;
        $participant = $this->getParticipant();

        $this->messageManager->shouldReceive('markIsReadByThreadAndParticipant')
            ->with($thread, $participant, false);

        $this->manager->markAsUnreadByParticipant($thread, $participant);
    }

    protected function setUp()
    {
        $classMetadata = new ClassMetadata('FOS\\MessageBundle\\Tests\\Entity\\Thread');
        $this->em = m::mock('Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('getRepository')
            ->with('FOS\\MessageBundle\\Tests\\Entity\\Thread')
            ->andReturn(new EntityRepository($this->em, $classMetadata));
        $this->em->shouldReceive('createQueryBuilder')
            ->andReturnUsing(function () {
                return new QueryBuilder($this->em);
            });
        $this->em->shouldReceive('getClassMetadata')
            ->with('FOS\\MessageBundle\\Tests\\Entity\\Thread')
            ->andReturn($classMetadata)
            ->once();

        $this->denormalizer = m::mock('FOS\\MessageBundle\\EntityManager\\ThreadDenormalizer');
        $this->messageManager = m::mock('FOS\\MessageBundle\\EntityManager\\MessageManager');

        $this->manager = new ThreadManager(
            $this->em,
            'FOS\\MessageBundle\\Tests\\Entity\\Thread',
            $this->denormalizer,
            $this->messageManager
        );
    }

    /**
     * @return \FOS\MessageBundle\Model\ParticipantInterface|\Mockery\MockInterface
     */
    protected function getParticipant()
    {
        $participant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');

        return $participant;
    }
}
