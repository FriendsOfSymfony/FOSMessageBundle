<?php

namespace FOS\MessageBundle\Validator;

use FOS\MessageBundle\SpamDetection\SpamDetectorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class SpamValidator extends ConstraintValidator
{
    /**
     * @var SpamDetectorInterface
     */
    protected $spamDetector;

    public function __construct(SpamDetectorInterface $spamDetector)
    {
        $this->spamDetector = $spamDetector;
    }

    /**
     * Indicates whether the constraint is valid.
     *
     * @param object     $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if ($this->spamDetector->isSpam($value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}
