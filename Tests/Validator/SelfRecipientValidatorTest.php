<?php

namespace FOS\MessageBundle\Tests\Validator;

use FOS\MessageBundle\Validator\SelfRecipient;
use FOS\MessageBundle\Validator\SelfRecipientValidator;
use Mockery as m;

class SelfRecipientValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Validator\ExecutionContext|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \FOS\MessageBundle\Validator\SelfRecipientValidator
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

    public function testSameParticipant()
    {
        $constraint = new SelfRecipient;
        $this->context->shouldReceive('addViolation')
            ->with($constraint->message);

        $this->validator->validate($this->participant, $constraint);
    }

    public function testDifferentParticipant()
    {
        $participant = m::mock('FOS\MessageBundle\Model\ParticipantInterface');

        $this->validator->validate($participant, new SelfRecipient);
    }

    protected function setUp()
    {
        $this->participant = m::mock('FOS\\MessageBundle\\Model\\ParticipantInterface');
        $this->participantProvider = m::mock('FOS\\MessageBundle\\Security\\ParticipantProviderInterface');
        $this->participantProvider->shouldReceive('getAuthenticatedParticipant')
            ->andReturn($this->participant);

        $this->context = m::mock('Symfony\\Component\\Validator\\ExecutionContext');

        $this->validator = new SelfRecipientValidator($this->participantProvider);
        $this->validator->initialize($this->context);
    }
}
