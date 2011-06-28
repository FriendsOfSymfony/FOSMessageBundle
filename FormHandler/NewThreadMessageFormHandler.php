<?php

namespace Ornicar\MessageBundle\FormHandler;

use Ornicar\MessageBundle\FormModel\AbstractMessage;

class NewThreadMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param AbstractMessage $message
     * @return MessageInterface the composed message ready to be sent
     */
    public function composeMessage(AbstractMessage $message)
    {
        return $this->composer->newThread()
            ->setSubject($message->getSubject())
            ->addRecipient($message->getRecipient())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
