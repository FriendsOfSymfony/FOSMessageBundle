<?php

namespace FOS\MessageBundle\Tests\Entity;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Mapping\ClassMetadata;
use FOS\MessageBundle\EntityManager\MessageDenormalizer;
use Mockery as m;

class MessageDenormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \FOS\MessageBundle\EntityManager\MessageDenormalizer
     */
    private $denormalizer;

    public function testDenormaliseEmptyMessage()
    {
        $thread = new Thread;
        $message = new Message;
        $thread->addMessage($message);

        $this->denormalizer->denormalize($message);
    }

    public function testDenormaliseThreadWithMetadataSetsMessageMetadata()
    {
        $thread = new Thread;
        $thread->addMetadata($threadMetadata = new ThreadMetadata);
        $threadMetadata->setParticipant($participant = $this->getParticipant());

        $message = new Message;
        $thread->addMessage($message);

        $this->denormalizer->denormalize($message);

        $this->assertCount(1, $message->getAllMetadata());
        $this->assertNotNull($message->getMetadataForParticipant($participant));
    }

    protected function setUp()
    {
        $em = m::mock('Doctrine\\ORM\\EntityManager');
        $em->shouldReceive('getClassMetadata')
            ->with('FOS\\MessageBundle\\Tests\\Entity\\MessageMetadata')
            ->andReturn(new ClassMetadata('FOS\\MessageBundle\\Tests\\Entity\\MessageMetadata'))
            ->once();

        $this->denormalizer = new MessageDenormalizer(
            $em,
            'FOS\\MessageBundle\\Tests\\Entity\\MessageMetadata'
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
