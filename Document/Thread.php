<?php

namespace Ornicar\MessageBundle\Document;

use Ornicar\MessageBundle\Model\Thread as AbstractThread;
use Ornicar\MessageBundle\Model\MessageInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserInterface;

abstract class Thread extends AbstractThread
{
    /**
     * Messages contained in this thread
     *
     * @var Collection of MessageInterface
     */
    protected $messages;

    /**
     * Users participating in this conversation
     *
     * @var Collection of UserInterface
     */
    protected $participants;

    /**
     * Date the last messages were created at.
     * To each user id is associated the date
     * of the last message he did not write.
     *
     * This allows fast sorting of threads in inbox
     *
     * @var array of int timestamps indexed by user id
     */
    protected $datesOfLastMessageWrittenByOtherUser = array();

    /**
     * Date the last messages were created at.
     * To each user id is associated the date
     * of the last message he wrote.
     *
     * This allows fast sorting of threads in sentbox
     *
     * @var array of int timestamps indexed by user id
     */
    protected $datesOfLastMessageWrittenByUser = array();

    /**
     * Initializes the collections
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    /**
     * Gets the messages contained in the thread
     *
     * @return array of MessageInterface
     */
    public function getMessages()
    {
        return $this->messages->toArray();
    }

    /**
     * Adds a new message to the thread
     *
     * @param MessageInterface $message
     */
    public function addMessage(MessageInterface $message)
    {
        $this->messages->add($message);
        $this->denormalize($message);
    }

    /**
     * Gets the users participating in this conversation
     *
     * @return array of UserInterface
     */
    public function getParticipants()
    {
        return $this->participants->toArray();
    }

    /**
     * Performs denormalization tricks
     * based on a message belonging to this thread.
     * Updates participants and last message dates.
     *
     * Take it easy, this code is tested in Tests\Document\ThreadTest ;)
     *
     * @param MessageInterface $message
     */
    protected function denormalize(MessageInterface $message)
    {
        $sender = $message->getSender();
        $recipient = $message->getRecipient();

        // Make sure the participants are registered
        foreach (array($sender, $recipient) as $participant) {
            if (!$this->participants->contains($participant)) {
                $this->participants->add($participant);
            }
        }

        // Update the last message dates if needed
        $messageTs = $message->getCreatedAt()->getTimestamp();
        $senderId = $sender->getId();
        foreach ($this->participants as $participant) {
            $participantId = $participant->getId();
            if ($participantId != $senderId) {
                if (!isset($this->datesOfLastMessageWrittenByOtherUser[$participantId]) || $this->datesOfLastMessageWrittenByOtherUser[$participantId] < $messageTs) {
                    $this->datesOfLastMessageWrittenByOtherUser[$participantId] = $messageTs;
                }
            } elseif (!isset($this->datesOfLastMessageWrittenByUser[$participantId]) || $this->datesOfLastMessageWrittenByUser[$participantId] < $messageTs) {
                $this->datesOfLastMessageWrittenByUser[$participantId] = $messageTs;
            }
        }
        // having theses sorted by user does not harm, and it makes unit testing easier
        ksort($this->datesOfLastMessageWrittenByUser);
        ksort($this->datesOfLastMessageWrittenByOtherUser);
    }
}
