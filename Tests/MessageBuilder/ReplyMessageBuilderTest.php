<?php

namespace FOS\MessageBundle\Tests\Composer;


use FOS\MessageBundle\MessageBuilder\ReplyMessageBuilder;
use FOS\MessageBundle\Tests\Model\Message;
use FOS\MessageBundle\Tests\Model\Thread;
use Mockery as m;

class ReplyMessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReplyMessageBuilder
     */
    private $builder;

    /**
     * @var \FOS\MessageBundle\Model\ThreadInterface

     */
    private $thread;

    /**
     * @var \FOS\MessageBundle\Model\MessageInterface
     */
    private $message;

    public function testSetBody()
    {
        $this->builder->setBody('Body of message');

        $this->assertEquals('Body of message', $this->message->getBody());
    }

    public function testSetSender()
    {
        $participant = $this->getParticipant();

        $this->builder->setSender($participant);

        $this->assertSame($participant, $this->message->getSender());
        $this->assertContains($participant, $this->thread->getParticipants());
    }

    protected function setUp()
    {
        $this->message = new Message;
        $this->thread = new Thread;

        $this->builder = new ReplyMessageBuilder($this->message, $this->thread);
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
