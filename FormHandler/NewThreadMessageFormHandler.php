<?php

namespace Ornicar\MessageBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ornicar\MessageBundle\Sender\SenderInterface;
use Ornicar\MessageBundle\FormModel\AbstractMessage;

class NewThreadMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param AbstractMessage $message
     * @return MessageBuilder $messageBuilder
     */
    public function composeMessage(AbstractMessage $message)
    {
        return $this->composer->compose()
            ->setSubject($message->getSubject())
            ->setRecipient($message->getRecipient())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody());
    }
}
