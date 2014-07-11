<?php

namespace FOS\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class SelfRecipient extends Constraint
{
    public $message = 'fos_message.self_recipient';

    public function validatedBy()
    {
        return 'fos_message.validator.self_recipient';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
