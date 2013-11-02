<?php

namespace FOS\MessageBundle\Tests\Composer;


use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder;
use FOS\MessageBundle\Tests\Model\Message;
use FOS\MessageBundle\Tests\Model\Thread;
use Mockery as m;

class NewThreadMessageBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewThreadMessageBuilder
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

    public function testSetSubject()
    {
        $this->builder->setSubject('Hello');

        $this->assertEquals('Hello', $this->thread->getSubject());
    }

    public function testAddRecipient()
    {
        $recipient = $this->getParticipant();
        $this->builder->addRecipient($recipient);

        $this->assertContains($recipient, $this->thread->getParticipants());
    }

    public function testAddRecipientsArray()
    {
        $this->builder->addRecipients(array(
            $this->getParticipant(),
            $this->getParticipant(),
            $this->getParticipant(),
        ));

        $this->assertCount(3, $this->thread->getParticipants());
    }

    public function testAddRecipientsCollection()
    {
        $this->builder->addRecipients(new ArrayCollection(array(
            $this->getParticipant(),
            $this->getParticipant(),
            $this->getParticipant(),
        )));

        $this->assertCount(3, $this->thread->getParticipants());
    }

    protected function setUp()
    {
        $this->message = new Message;
        $this->thread = new Thread;

        $this->builder = new NewThreadMessageBuilder($this->message, $this->thread);
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
