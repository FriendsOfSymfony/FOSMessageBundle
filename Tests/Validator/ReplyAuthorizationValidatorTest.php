<?php

namespace FOS\MessageBundle\Tests\Validator;

use FOS\MessageBundle\Tests\Model\Message;
use FOS\MessageBundle\Validator\ReplyAuthorization;
use FOS\MessageBundle\Validator\ReplyAuthorizationValidator;
use Mockery as m;

class ReplyAuthorizationValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Validator\ExecutionContext|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \FOS\MessageBundle\Validator\ReplyAuthorizationValidator
     */
    protected $validator;

    /**
     * @var \FOS\MessageBundle\Security\ParticipantProviderInterface|\Mockery\MockInterface
     */
    protected $participantProvider;

    /**
     * @var \FOS\MessageBundle\Model\ParticipantInterface|\Mockery\MockInterface
     */
    protected $participant;

    /**
     * @var \FOS\MessageBundle\Security\AuthorizerInterface|\Mockery\MockInterface
     */
    protected $authorizer;

    public function testCantSendToParticipant()
    {
        $constraint = new ReplyAuthorization;

        $thread = m::mock('FOS\\MessageBundle\\Model\\ThreadInterface');
        $message = new Message;
        $message->setThread($thread);

        $thread->shouldReceive('getOtherParticipants')
            ->with($this->participant)
            ->andReturn(array($altParticipant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface')));

        $this->authorizer->shouldReceive('canMessageParticipant')
            ->with($altParticipant)
            ->andReturn(false);

        $this->context->shouldReceive('addViolation')
            ->with($constraint->message);

        $this->validator->validate($message, $constraint);
    }

    public function testCanSendToParticipant()
    {
        $constraint = new ReplyAuthorization;

        $thread = m::mock('FOS\\MessageBundle\\Model\\ThreadInterface');
        $message = new Message;
        $message->setThread($thread);

        $thread->shouldReceive('getOtherParticipants')
            ->with($this->participant)
            ->andReturn(array($altParticipant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface')));

        $this->authorizer->shouldReceive('canMessageParticipant')
            ->with($altParticipant)
            ->andReturn(true);

        $this->validator->validate($message, $constraint);
    }

    protected function setUp()
    {
        $this->participant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');
        $this->participantProvider = m::mock('FOS\\MessageBundle\\Security\\ParticipantProviderInterface');
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($this->participant);

        $this->authorizer = m::mock('FOS\\MessageBundle\\Security\\AuthorizerInterface');
        $this->context = m::mock('Symfony\\Component\\Validator\\ExecutionContext');

        $this->validator = new ReplyAuthorizationValidator($this->authorizer, $this->participantProvider);
        $this->validator->initialize($this->context);
    }
}
