<?php

namespace Ornicar\MessageBundle\FormHandler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Ornicar\MessageBundle\Sender\SenderInterface;
use Ornicar\MessageBundle\FormModel\AbstractMessage;

class NewThreadMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Sends the message
     *
     * @param AbstractMessage $message
     */
    public function composeAndSend(AbstractMessage $message)
    {
        return $this->composer->compose()
            ->setSubject($message->getSubject())
            ->setRecipient($message->getRecipient())
            ->setSender($this->authorizer->getAuthenticatedUser())
            ->setBody($message->getBody())
            ->send();
    }
}
