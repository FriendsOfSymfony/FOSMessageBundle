<?php

namespace FOS\MessageBundle\Form\Handler;

use FOS\MessageBundle\Form\Model\AbstractMessage;
use FOS\MessageBundle\Form\Model\ReplyMessage;

class ReplyMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param  AbstractMessage          $message
     * @return \FOS\MessageBundle\Model\MessageInterface         the composed message ready to be sent
     * @throws \InvalidArgumentException if the message is not a ReplyMessage
     */
    public function composeMessage(AbstractMessage $message)
    {
        if (!$message instanceof ReplyMessage) {
            throw new \InvalidArgumentException(sprintf('Message must be a ReplyMessage instance, "%s" given', get_class($message)));
        }

        return $this->composer->reply($message->getThread())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
