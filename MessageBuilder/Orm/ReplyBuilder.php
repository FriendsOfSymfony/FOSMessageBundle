<?php

namespace FOS\MessageBundle\MessageBuilder\Orm;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * The reply builder is responsible for building a new thread object with the reply message
 *
 * @author Michiel Boeckaert <boeckaert@gmail.com>
 */
class ReplyBuilder extends AbstractMessageBuilder
{
    /**
     * The thread we reply to
     * 
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * The recipients of the reply
     * 
     * @var ParticipantInterface[] array with recipients of this thread
     */
    protected $recipients;

    /**
     * Sets the thread where we will reply to
     * 
     * @param ThreadInterface $thread
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

   /**
     * Builds a new thread with the reply added
     *
     * @return ThreadInterface the new build thread
     */
    public function build()
    {
        $this->guardNewReplyRequirements();

        $this->recipients = $this->thread->getOtherParticipants($this->sender);
        $this->buildNewMessage($this->thread);
        $senderThreadMeta = $this->thread->getMetadataForParticipant($this->sender);
        $this->updateThreadMetaForSender($senderThreadMeta);

        foreach ($this->recipients as $recipient) {
            $recipientThreadMeta = $this->thread->getMetadataForParticipant($recipient);
            $this->updateThreadMetaForRecipient($recipientThreadMeta);
        }

        return $this->thread;
    }

    /**
     * Guards the requirements of a new reply and throws errors when those are not met
     */
    protected function guardNewReplyRequirements()
    {
        $this->expectsSenderSet();
        $this->expectsSenderIsParticipantOfThread();
        $this->expectsBodySet();
        $this->expectsCreationDate();
    }

    protected function expectsSenderIsParticipantOfThread()
    {
        if (!$this->thread->isParticipant($this->sender)) {
            throw new AccessDeniedException('You are no participant of this thread');
        }
    }
}
