<?php

namespace Ornicar\MessageBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ornicar\MessageBundle\Sender\SenderInterface;
use Ornicar\MessageBundle\FormModel\AbstractMessage;

class ReplyMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Sends the message
     *
     * @param AbstractMessage $message
     */
    public function composeAndSend(AbstractMessage $message)
    {
        $this->sender->compose()
            //->inReplyTo()
            ->setRecipient($message->getRecipient())
            ->setSender($this->getAuthenticatedUser())
            ->setBody($message->getBody())
            ->send();
    }
}
