<?php

namespace Ornicar\MessageBundle\FormHandler;

use Ornicar\MessageBundle\FormModel\ReplyMessage;

class ReplyMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param ReplyMessage $message
     * @return MessageInterface the composed message ready to be sent
     */
    public function composeMessage(ReplyMessage $message)
    {
        return $this->composer->reply($message->getThread())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
