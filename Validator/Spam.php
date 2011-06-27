<?php

namespace Ornicar\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Spam extends Constraint
{
    public $message = 'Sorry, your message looks like spam';

    public function validatedBy()
    {
        return 'ornicar_message.validator.spam';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
