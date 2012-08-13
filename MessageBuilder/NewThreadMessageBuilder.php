<?php

namespace Ornicar\MessageBundle\MessageBuilder;

use Ornicar\MessageBundle\Model\MessageInterface;
use Ornicar\MessageBundle\Model\ParticipantInterface;
use Ornicar\MessageBundle\Sender\SenderInterface;
use Doctrine\Common\Collections\Collection;

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

    /**
     * @param \Doctrine\Common\Collections\Collection $recipients
     *
     * @return NewThreadMessageBuilder
     */
    public function addRecipients(Collection $recipients) {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }

        return $this;
    }

}