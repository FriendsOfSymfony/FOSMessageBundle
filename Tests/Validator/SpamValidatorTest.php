<?php

namespace FOS\MessageBundle\Tests\Validator;

use FOS\MessageBundle\Form\Model\NewThreadMessage;
use FOS\MessageBundle\Validator\Spam;
use FOS\MessageBundle\Validator\SpamValidator;
use Mockery as m;

class SpamValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\Validator\ExecutionContext|\Mockery\MockInterface
     */
    protected $context;

    /**
     * @var \FOS\MessageBundle\Validator\SpamValidator
     */
    protected $validator;

    /**
     * @var \FOS\MessageBundle\SpamDetection\SpamDetectorInterface|\Mockery\MockInterface
     */
    protected $spamDetector;

    public function testNotSpam()
    {
        $message = new NewThreadMessage;

        $this->spamDetector->shouldReceive('isSpam')
            ->with($message)
            ->andReturn(false);

        $this->validator->validate($message, new Spam);
    }

    public function testSpam()
    {
        $message = new NewThreadMessage;
        $constraint =  new Spam;

        $this->spamDetector->shouldReceive('isSpam')
            ->with($message)
            ->andReturn(true);
        $this->context->shouldReceive('addViolation')
            ->with($constraint->message);

        $this->validator->validate($message, $constraint);
    }

    protected function setUp()
    {
        $this->spamDetector = m::mock('FOS\\MessageBundle\\SpamDetection\\SpamDetectorInterface');
        $this->context = m::mock('Symfony\\Component\\Validator\\ExecutionContext');

        $this->validator = new SpamValidator($this->spamDetector);
        $this->validator->initialize($this->context);
    }
}
