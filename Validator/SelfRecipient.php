<?php

namespace Ornicar\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class SelfRecipient extends Constraint
{
    public $message = 'You cannot send a message to yourself';

    public function validatedBy()
    {
        return 'ornicar_message.validator.self_recipient';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
