<?php

namespace FOS\MessageBundle\Tests\Validator;

use FOS\MessageBundle\Validator\Authorization;
use FOS\MessageBundle\Validator\AuthorizationValidator;
use Mockery as m;

class AuthorizationValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Validator\ExecutionContext|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \FOS\MessageBundle\Validator\AuthorizationValidator
     */
    protected $validator;

    /**
     * @var \FOS\MessageBundle\Security\AuthorizerInterface|\Mockery\MockInterface
     */
    protected $authorizer;

    public function testCantSendToParticipant()
    {
        $constraint = new Authorization;
        $recipient = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');

        $this->authorizer->shouldReceive('canMessageParticipant')
            ->with($recipient)
            ->andReturn(false);

        $this->context->shouldReceive('addViolation')
            ->with($constraint->message);

        $this->validator->validate($recipient, $constraint);
    }

    public function testCanSendToParticipant()
    {
        $constraint = new Authorization;
        $recipient = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');

        $this->authorizer->shouldReceive('canMessageParticipant')
            ->with($recipient)
            ->andReturn(true);

        $this->validator->validate($recipient, $constraint);
    }

    protected function setUp()
    {
        $this->authorizer = m::mock('FOS\\MessageBundle\\Security\\AuthorizerInterface');
        $this->context = m::mock('Symfony\\Component\\Validator\\ExecutionContext');

        $this->validator = new AuthorizationValidator($this->authorizer);
        $this->validator->initialize($this->context);
    }
}
