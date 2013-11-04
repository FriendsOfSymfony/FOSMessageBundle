<?php

namespace FOS\MessageBundle\Tests\Form\Handler;

use FOS\MessageBundle\Form\Handler\NewThreadMessageFormHandler;
use FOS\MessageBundle\Form\Model\NewThreadMessage;
use FOS\MessageBundle\Model\MessageInterface;
use Mockery as m;
use Symfony\Component\HttpFoundation\Request;

class NewThreadMessageFormHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewThreadMessageFormHandler
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
        $message = new NewThreadMessage;
        $message->setSubject('Test Subject');
        $message->setBody('Test Body');
        $message->setRecipient(m::mock('FOS\MessageBundle\Model\ParticipantInterface'));

        $this->setBuilder($message);

        $this->handler->composeMessage($message);
    }

    public function testProcessNonPost()
    {
        $this->request->setMethod('GET');

        $form = m::mock('Symfony\Component\Form\FormInterface');

        $result = $this->handler->process($form);

        $this->assertFalse($result);
    }

    public function testProcessInvalid()
    {
        $this->request->setMethod('POST');

        $form = m::mock('Symfony\Component\Form\FormInterface');
        $form->shouldReceive('handleRequest')
            ->with($this->request);
        $form->shouldReceive('isValid')
            ->andReturn(false);

        $result = $this->handler->process($form);

        $this->assertFalse($result);
    }

    public function testProcess()
    {
        $this->request->setMethod('POST');

        $form = m::mock('Symfony\Component\Form\FormInterface');
        $form->shouldReceive('handleRequest')
            ->with($this->request);
        $form->shouldReceive('isValid')
            ->andReturn(true);

        $message = new NewThreadMessage;
        $message->setSubject('Test Subject');
        $message->setBody('Test Body');
        $message->setRecipient(m::mock('FOS\MessageBundle\Model\ParticipantInterface'));

        $form->shouldReceive('getData')
            ->andReturn($message);

        $this->setBuilder($message, $return = m::mock('FOS\MessageBundle\Model\MessageInterface'));

        $this->sender->shouldReceive('send')
            ->with($return);

        $result = $this->handler->process($form);

        $this->assertSame($return, $result);
    }

    protected function setBuilder($message, $finalReturn = null)
    {
        $this->composer->shouldReceive('newThread')
            ->andReturn($builder = m::mock('FOS\MessageBundle\MessageBuilder\NewThreadMessageBuilder'));

        $builder->shouldReceive('setSubject')
            ->with($message->getSubject())
            ->andReturn($builder);
        $builder->shouldReceive('addRecipient')
            ->with($message->getRecipient())
            ->andReturn($builder);
        $builder->shouldReceive('setSender')
            ->with($this->participant)
            ->andReturn($builder);
        $builder->shouldReceive('setBody')
            ->with($message->getBody())
            ->andReturn($builder);
        $builder->shouldReceive('getMessage')
            ->andReturn($finalReturn);
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

        $this->handler = new NewThreadMessageFormHandler(
            $this->request,
            $this->composer,
            $this->sender,
            $this->participantProvider
        );
    }
}
