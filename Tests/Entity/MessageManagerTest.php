<?php

namespace FOS\MessageBundle\Tests\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
use FOS\MessageBundle\EntityManager\MessageManager;
use Mockery as m;

class MessageManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FOS\MessageBundle\EntityManager\MessageManager|\Mockery\Mock
     */
    private $manager;

    /**
     * @var \Doctrine\ORM\EntityManager|\Mockery\Mock
     */
    private $em;

    /**
     * @var \FOS\MessageBundle\EntityManager\MessageDenormalizer|\Mockery\Mock
     */
    private $denormalizer;

    public function testCreateMessage()
    {
        $message = $this->manager->createMessage();

        $this->assertInstanceOf('FOS\\MessageBundle\\Model\\MessageInterface', $message);
    }

    public function testSaveMessage()
    {
        $message = new Message;

        $this->denormalizer->shouldReceive('denormalize')
            ->with($message);
        $this->em->shouldReceive('persist')
            ->with($message);
        $this->em->shouldReceive('flush');

        $this->manager->saveMessage($message);
    }

    public function testMarkAsReadByParticipant()
    {
        $message = new Message;
        $participant = $this->getParticipant();
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant);
        $message->addMetadata($messageMetadata);

        $this->manager->markAsReadByParticipant($message, $participant);

        $this->assertTrue($message->isReadByParticipant($participant));
    }

    public function testMarkAsUnReadByParticipant()
    {
        $message = new Message;
        $participant = $this->getParticipant();
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant);
        $message->addMetadata($messageMetadata);

        $this->manager->markAsUnreadByParticipant($message, $participant);

        $this->assertFalse($message->isReadByParticipant($participant));
    }

    public function testMarkIsReadByThreadAndParticipant()
    {
        $thread = new Thread;
        $message = new Message;
        $participant = $this->getParticipant();
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant);
        $message->addMetadata($messageMetadata);
        $thread->addMessage($message);

        $this->denormalizer->shouldReceive('denormalize')
            ->with($message);
        $this->em->shouldReceive('persist')
            ->with($message);
        $this->em->shouldReceive('flush');

        $this->manager->markIsReadByThreadAndParticipant($thread, $participant, true);

        $this->assertTrue($message->isReadByParticipant($participant));
    }

    protected function setUp()
    {
        $classMetadata = new ClassMetadata('FOS\\MessageBundle\\Tests\\Entity\\Message');
        $this->em = m::mock('Doctrine\\ORM\\EntityManager');
        $this->em->shouldReceive('getRepository')
            ->with('FOS\\MessageBundle\\Tests\\Entity\\Message')
            ->andReturn(new EntityRepository($this->em, $classMetadata));
        $this->em->shouldReceive('createQueryBuilder')
            ->andReturnUsing(function () {
                return new QueryBuilder($this->em);
            });
        $this->em->shouldReceive('getClassMetadata')
            ->with('FOS\\MessageBundle\\Tests\\Entity\\Message')
            ->andReturn($classMetadata)
            ->once();

        $this->denormalizer = m::mock('FOS\\MessageBundle\\EntityManager\\MessageDenormalizer');

        $this->manager = new MessageManager(
            $this->em,
            'FOS\\MessageBundle\\Tests\\Entity\\Message',
            $this->denormalizer
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
