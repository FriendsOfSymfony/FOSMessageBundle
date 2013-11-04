<?php

namespace FOS\MessageBundle\Tests\Form\Handler;

use FOS\MessageBundle\Form\Handler\NewThreadMultipleMessageFormHandler;
use FOS\MessageBundle\Form\Model\NewThreadMultipleMessage;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class NewThreadMultipleMessageFormHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewThreadMultipleMessageFormHandler
     */
    private $handler;

    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var \FOS\MessageBundle\Composer\ComposerInterface|\Mockery\Mock
     */
    private $composer;

    /**
     * @var \FOS\MessageBundle\Sender\SenderInterface|\Mockery\Mock
     */
    private $sender;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface
     */
    private $participant;

    /**
     * @var \FOS\MessageBundle\Security\ParticipantProviderInterface|\Mockery\MockInterface
     */
    private $participantProvider;

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidMessageType()
    {
        $message = m::mock('FOS\MessageBundle\Form\Model\ReplyMessage');

        $this->handler->composeMessage($message);
    }

    public function testComposeMessage()
    {
        $message = new NewThreadMultipleMessage;
        $message->setSubject('Test Subject');
        $message->setBody('Test Body');
        $message->addRecipient(m::mock('FOS\MessageBundle\Model\ParticipantInterface'));

        $this->composer->shouldReceive('newThread')
            ->andReturn($builder = m::mock('FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder'));

        $builder->shouldReceive('setSubject')
            ->with($message->getSubject())
            ->andReturn($builder);
        $builder->shouldReceive('addRecipients')
            ->with($message->getRecipients())
            ->andReturn($builder);
        $builder->shouldReceive('setSender')
            ->with($this->participant)
            ->andReturn($builder);
        $builder->shouldReceive('setBody')
            ->with($message->getBody())
            ->andReturn($builder);
        $builder->shouldReceive('getMessage');

        $this->handler->composeMessage($message);
    }

    protected function setUp()
    {
        $this->request = new Request();
        $this->composer = m::mock('FOS\MessageBundle\Composer\ComposerInterface');
        $this->sender = m::mock('FOS\MessageBundle\Sender\SenderInterface');

        $this->participant = m::mock('FOS\MessageBundle\Model\ParticipantInterface');
        $this->participantProvider = m::mock('FOS\MessageBundle\Security\ParticipantProviderInterface');
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($this->participant);

        $this->handler = new NewThreadMultipleMessageFormHandler(
            $this->request,
            $this->composer,
            $this->sender,
            $this->participantProvider
        );
    }
}
