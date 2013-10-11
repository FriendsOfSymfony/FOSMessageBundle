<?php

namespace FOS\MessageBundle\Tests\Model;

use Mockery as m;

class ThreadTest extends \PHPUnit_Framework_TestCase
{
    public function testAddParticipant()
    {
        $thread = new Thread;
        $thread->addParticipant($this->getParticipant());
        $thread->addParticipant($this->getParticipant());

        $this->assertCount(2, $thread->getParticipants());
    }

    public function testAddParticipants()
    {
        $thread = new Thread;
        $thread->addParticipants(array($this->getParticipant()));

        $this->assertCount(1, $thread->getParticipants());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddParticipantsNonArray()
    {
        $thread = new Thread;
        $thread->addParticipants(new \stdClass);
    }

    public function testOtherParticipants()
    {
        $thread = new Thread;
        $thread->addParticipants(array(
            $participant = $this->getParticipant(),
            $this->getParticipant(),
            $this->getParticipant()
        ));

        $others = $thread->getOtherParticipants($participant);

        $this->assertCount(2, $others);
        $this->assertNotContains($participant, $others);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMetadata()
    {
        $thread = new Thread;
        $thread->addParticipants(array(
            $participant = $this->getParticipant(),
        ));

        $thread->setIsDeletedByParticipant($participant, true);
    }

    public function testDeletion()
    {
        $thread = new Thread;

        $message = m::mock('FOS\\MessageBundle\\Model\\MessageInterface');
        $message->shouldReceive('setThread')
            ->with($thread)
            ->once();

        $thread->addParticipants(array(
            $participant1 = $this->getParticipant(),
            $participant2 = $this->getParticipant(),
        ));
        $metadata1 = new ThreadMetadata;
        $metadata1->setParticipant($participant1);
        $thread->addMetadata($metadata1);
        $metadata2 = new ThreadMetadata;
        $metadata2->setParticipant($participant2);
        $thread->addMetadata($metadata2);

        $thread->addMessage($message);

        $message->shouldReceive('setIsReadByParticipant')
            ->with($participant1, true)
            ->andReturnUndefined()
            ->once();

        $thread->setIsDeletedByParticipant($participant1, true);

        $this->assertTrue($thread->isDeletedByParticipant($participant1));
        $this->assertFalse($thread->isDeletedByParticipant($participant2));
        $this->assertFalse($thread->isDeletedByParticipant($this->getParticipant()));

        $thread->getMessages()->clear();
        $thread->setIsDeleted(true);

        $this->assertTrue($thread->isDeletedByParticipant($participant1));
        $this->assertTrue($thread->isDeletedByParticipant($participant2));
    }

    public function testReading()
    {
        $thread = new Thread;
        $thread->addParticipants(array(
            $participant1 = $this->getParticipant(),
            $participant2 = $this->getParticipant(),
        ));
        $metadata1 = new ThreadMetadata;
        $metadata1->setParticipant($participant1);
        $thread->addMetadata($metadata1);
        $metadata2 = new ThreadMetadata;
        $metadata2->setParticipant($participant2);
        $thread->addMetadata($metadata2);

        $thread->addMessage($message = new Message);
        $mmetadata1 = new MessageMetadata;
        $mmetadata1->setParticipant($participant1);
        $message->addMetadata($mmetadata1);
        $mmetadata2 = new MessageMetadata;
        $mmetadata2->setParticipant($participant2);
        $message->addMetadata($mmetadata2);

        $thread->setIsReadByParticipant($participant2, true);

        $this->assertFalse($thread->isReadByParticipant($participant1));
        $this->assertTrue($thread->isReadByParticipant($participant2));
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
