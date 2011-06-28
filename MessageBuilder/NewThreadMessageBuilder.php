<?php

namespace Ornicar\MessageBundle\MessageBuilder;

use Ornicar\MessageBundle\Model\MessageInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Sender\SenderInterface;

/**
 * Fluent interface message builder for new thread messages
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NewThreadMessageBuilder extends AbstractMessageBuilder
{
    /**
     * The thread subject
     *
     * @param  string
     * @return NewThreadMessageBuilder (fluent interface)
     */
    public function setSubject($subject)
    {
        $this->thread->setSubject($subject);

        return $this;
    }

    /**
     * @param  ParticipantInterface
     * @return NewThreadMessageBuilder (fluent interface)
     */
    public function addRecipient(ParticipantInterface $recipient)
    {
        $this->thread->addParticipant($recipient);

        return $this;
    }
}
