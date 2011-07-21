<?php

namespace Ornicar\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class SelfSubject extends Constraint
{
    public $message = 'You cannot send a message to yourself';

    public function validatedBy()
    {
        return 'ornicar_message.validator.self_subject';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
