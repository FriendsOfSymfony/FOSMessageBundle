<?php

namespace FOS\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class ReplyAuthorization extends Constraint
{
    public $message = 'fos_message.reply_not_authorized';

    public function validatedBy()
    {
        return 'fos_message.validator.reply_authorization';
    }

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
