<?php

namespace Ornicar\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ornicar\MessageBundle\Security\ParticipantProviderInterface;

class SelfSubjectValidator extends ConstraintValidator
{
    /**
     * @var ParticipantProviderInterface
     */
    protected $participantProvider;

    /**
     * Constructor
     *
     * @param ParticipantProviderInterface $participantProvider
     */
    public function __construct(ParticipantProviderInterface $participantProvider)
    {
        $this->participantProvider = $participantProvider;
    }

    /**
     * Indicates whether the constraint is valid
     *
     * @param object     $value
     * @param Constraint $constraint
     */
    public function isValid($value, Constraint $constraint)
    {
        $recipient = $value->getRecipient();
        if (!$recipient) {
            return true;
        }
        if ($recipient === $this->participantProvider->getAuthenticatedParticipant()) {
            $this->setMessage($constraint->message);
            return false;
        }

        return true;
    }
}
