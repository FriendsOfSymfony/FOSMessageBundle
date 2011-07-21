<?php

namespace Ornicar\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Authorization extends Constraint
{
    public $message = 'You are not allowed to send this message';

    public function validatedBy()
    {
        return 'ornicar_message.validator.authorization';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
