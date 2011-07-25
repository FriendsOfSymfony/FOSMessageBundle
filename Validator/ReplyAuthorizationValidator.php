<?php

namespace Ornicar\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Ornicar\MessageBundle\Security\AuthorizerInterface;
use Ornicar\MessageBundle\Security\ParticipantProviderInterface;

class ReplyAuthorizationValidator extends ConstraintValidator
{
    /**
     * @var AuthorizerInterface
     */
    protected $authorizer;

    /**
     * Constructor
     *
     * @param AuthorizerInterface $authorizer
     * @param ParticipantProviderInterface $participantProvider
     */
    public function __construct(AuthorizerInterface $authorizer, ParticipantProviderInterface $participantProvider)
    {
        $this->authorizer = $authorizer;
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
        $sender = $this->participantProvider->getAuthenticatedParticipant();
        $recipients = $value->getThread()->getOtherParticipants($sender);
        foreach ($recipients as $recipient) {
            if (!$this->authorizer->canMessageParticipant($recipient)) {
                $this->setMessage($constraint->message);
                return false;
            }
        }

        return true;
    }
}
