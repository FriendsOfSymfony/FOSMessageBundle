<?php

namespace Ornicar\MessageBundle\Validator;

use Symfony\Component\Validator\Constraint;

class ReplyAuthorization extends Constraint
{
    public $message = 'You are not allowed to reply to this message';

    public function validatedBy()
    {
        return 'ornicar_message.validator.reply_authorization';
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
