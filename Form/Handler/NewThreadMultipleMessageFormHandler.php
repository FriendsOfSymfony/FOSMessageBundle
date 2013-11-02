<?php
namespace FOS\MessageBundle\Form\Handler;

use FOS\MessageBundle\Form\Model\AbstractMessage;
use FOS\MessageBundle\Form\Model\NewThreadMultipleMessage;
/**
 * Form handler for multiple recipients support
 *
 * @author Łukasz Pospiech <zocimek@gmail.com>
 */
class NewThreadMultipleMessageFormHandler extends AbstractMessageFormHandler
{
    /**
     * Composes a message from the form data
     *
     * @param AbstractMessage $message
     *
     * @return \FOS\MessageBundle\Model\MessageInterface          the composed message ready to be sent
     * @throws \InvalidArgumentException if the message is not a NewThreadMessage
     */
    public function composeMessage(AbstractMessage $message)
    {
        if (!$message instanceof NewThreadMultipleMessage) {
            throw new \InvalidArgumentException(sprintf('Message must be a NewThreadMultipleMessage instance, "%s" given', get_class($message)));
        }

        return $this->composer->newThread()
            ->setSubject($message->getSubject())
            ->addRecipients($message->getRecipients())
            ->setSender($this->getAuthenticatedParticipant())
            ->setBody($message->getBody())
            ->getMessage();
    }
}
