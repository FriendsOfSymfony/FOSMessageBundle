<?php

namespace FOS\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class Spam extends Constraint
{
    public $message = 'fos_user.body.spam';

    public function validatedBy()
    {
        return 'fos_message.validator.spam';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
