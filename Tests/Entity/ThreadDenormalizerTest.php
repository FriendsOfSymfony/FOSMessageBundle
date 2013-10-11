<?php

namespace FOS\MessageBundle\Tests\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use FOS\MessageBundle\EntityManager\ThreadDenormalizer;
use Mockery as m;

class ThreadDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FOS\MessageBundle\EntityManager\ThreadDenormalizer
     */
    private $denormalizer;

    public function testDenormaliseEmptyThread()
    {
        $thread = new Thread;

        $this->denormalizer->denormalize($thread);
    }

    public function testDenormaliseParticipants()
    {
        $thread = new Thread;
        $thread->addParticipants(array(
            $this->getParticipant(),
            $this->getParticipant()
        ));

        $this->denormalizer->denormalize($thread);

        $this->assertCount(2, $thread->getAllMetadata());
    }

    public function testDenormaliseMessageParticipants()
    {
        $thread = new Thread;
        $thread->addParticipants(array(
            $participant1 = $this->getParticipant(),
            $this->getParticipant()
        ));
        $message = new Message;
        $message->setSender($participant1);
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant1);
        $message->addMetadata($messageMetadata);
        $thread->addMessage($message);

        $this->denormalizer->denormalize($thread);

        $this->assertCount(2, $thread->getAllMetadata());
    }

    public function testDenormaliseMessageParticipantsWithNewParticipant()
    {
        $thread = new Thread;
        $participant1 = $this->getParticipant();

        $message = new Message;
        $message->setSender($participant1);
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant1);
        $message->addMetadata($messageMetadata);
        $thread->addMessage($message);

        $this->denormalizer->denormalize($thread);

        $this->assertCount(1, $thread->getAllMetadata());
        $this->assertEquals(
            $thread->getMetadataForParticipant($participant1)->getLastParticipantMessageDate(),
            $message->getCreatedAt()
        );
    }

    public function testDenormaliseThreadSetsCreatedAtAndBy()
    {
        $thread = new Thread;
        $message = new Message;
        $message->setSender($participant = $this->getParticipant());
        $thread->addMessage($message);

        $this->denormalizer->denormalize($thread);

        $this->assertEquals($participant, $thread->getCreatedBy());
        $this->assertEquals($message->getCreatedAt(), $thread->getCreatedAt());
    }

    public function testDenormaliseThreadSetsLastMessageByOtherParticipants()
    {
        $thread = new Thread;
        $thread->addParticipants(array(
            $participant1 = $this->getParticipant(),
            $participant2 = $this->getParticipant()
        ));

        // Participant1 sends the first message. Participant2 should have a date of 1998.
        $message0 = new Message;
        $message0->setSender($participant1);
        $message0->setCreatedAt(new \DateTime('1998-01-01'));
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant1);
        $message0->addMetadata($messageMetadata);
        $thread->addMessage($message0);

        // Participant1 sends another message. Participant2 should have a date of 2000.
        $message1 = new Message;
        $message1->setSender($participant1);
        $message1->setCreatedAt(new \DateTime('2000-01-01'));
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant1);
        $message1->addMetadata($messageMetadata);
        $thread->addMessage($message1);

        // Participant2 replies. Participant1 should have a date of 2013.
        $message2 = new Message;
        $message2->setSender($participant2);
        $message2->setCreatedAt(new \DateTime('2013-01-01'));
        $messageMetadata = new MessageMetadata;
        $messageMetadata->setParticipant($participant2);
        $message2->addMetadata($messageMetadata);
        $thread->addMessage($message2);

        $this->denormalizer->denormalize($thread);

        $metadata1 = $thread->getMetadataForParticipant($participant1);
        $this->assertEquals($message2->getCreatedAt(), $metadata1->getLastMessageDate());
        $this->assertEquals($message1->getCreatedAt(), $metadata1->getLastParticipantMessageDate());

        $metadata2 = $thread->getMetadataForParticipant($participant2);
        $this->assertEquals($message1->getCreatedAt(), $metadata2->getLastMessageDate());
        $this->assertEquals($message2->getCreatedAt(), $metadata2->getLastParticipantMessageDate());
    }

    protected function setUp()
    {
        $em = m::mock('Doctrine\\ORM\\EntityManager');
        $em->shouldReceive('getClassMetadata')
            ->with('FOS\\MessageBundle\\Tests\\Entity\\ThreadMetadata')
            ->andReturn(new ClassMetadata('FOS\\MessageBundle\\Tests\\Entity\\ThreadMetadata'))
            ->once();

        $this->denormalizer = new ThreadDenormalizer(
            $em,
            'FOS\\MessageBundle\\Tests\\Entity\\ThreadMetadata'
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
