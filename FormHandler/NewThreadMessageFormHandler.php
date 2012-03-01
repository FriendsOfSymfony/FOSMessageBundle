<?php

namespace Ornicar\MessageBundle\FormHandler;

use Ornicar\MessageBundle\FormModel\NewThreadMessage;

class NewThreadMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param NewThreadMessage $message
     * @return MessageInterface the composed message ready to be sent
     */
    public function composeMessage(NewThreadMessage $message)
    {
        return $this->composer->newThread()
            ->setSubject($message->getSubject())
            ->addRecipient($message->getRecipient())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
