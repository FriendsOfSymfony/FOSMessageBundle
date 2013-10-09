<?php

namespace FOS\MessageBundle\Tests\Model;

use Mockery as m;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMetadataForParticipantNonExistant()
    {
        $message = new Message;
        $participant = $this->getParticipant();

        $this->assertNull($message->getMetadataForParticipant($participant));
    }

    public function testGetMetadataForParticipant()
    {
        $message = new Message;
        $participant = $this->getParticipant();
        $metadata = new MessageMetadata;
        $metadata->setParticipant($participant);
        $message->addMetadata($metadata);

        $this->assertInstanceOf('FOS\\MessageBundle\\Model\\MessageMetadata', $message->getMetadataForParticipant($participant));
    }

    public function testReadByParticipant()
    {
        $message = new Message;
        $participant = $this->getParticipant();

        $this->assertFalse($message->isReadByParticipant($participant));

        $metadata = new MessageMetadata;
        $metadata->setParticipant($participant);
        $message->addMetadata($metadata);

        $this->assertFalse($message->isReadByParticipant($participant));

        $message->setIsReadByParticipant($participant, true);

        $this->assertTrue($message->isReadByParticipant($participant));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testReadByParticipantNonExistant()
    {
        $message = new Message;
        $participant = $this->getParticipant();
        $message->setIsReadByParticipant($participant, true);
    }

    /**
     * @return \FOS\MessageBundle\Model\ParticipantInterface|\Mockery\MockInterface
     */
    protected function getParticipant()
    {
        $participant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');
        $participant->shouldReceive('getId')
            ->andReturn(1);

        return $participant;
    }
}
