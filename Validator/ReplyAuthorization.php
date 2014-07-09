<?php

namespace FOS\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class ReplyAuthorization extends Constraint
{
    public $message = 'fos_message.not_authorized';

    public function validatedBy()
    {
        return 'fos_message.validator.reply_authorization';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
