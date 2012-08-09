<?php

namespace Ornicar\MessageBundle\FormHandler;

use Ornicar\MessageBundle\FormModel\AbstractMessage;
use Ornicar\MessageBundle\FormModel\NewThreadMessage;

class NewThreadMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param AbstractMessage $message
     * @return MessageInterface the composed message ready to be sent
     * @throws InvalidArgumentException if the message is not a NewThreadMessage
     */
    public function composeMessage(AbstractMessage $message)
    {
        if (!$message instanceof NewThreadMessage) {
            throw new \InvalidArgumentException(sprintf('Message must be a NewThreadMessage instance, "%s" given', get_class($message)));
        }

        return $this->composer->newThread()
            ->setSubject($message->getSubject())
            ->setRecipients($message->getRecipients())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
