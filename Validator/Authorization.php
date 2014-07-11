<?php

namespace FOS\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Authorization extends Constraint
{
    public $message = 'fos_message.not_authorized';

    public function validatedBy()
    {
        return 'fos_message.validator.authorization';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
