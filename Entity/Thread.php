<?php

namespace Ornicar\MessageBundle\Entity;

use Ornicar\MessageBundle\Model\Thread as BaseThread;
use Ornicar\MessageBundle\Model\MessageInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Ornicar\MessageBundle\Model\ParticipantInterface;

abstract class Thread extends BaseThread
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
     * @var Collection of ParticipantInterface
     */
    protected $participants;

    /**
     * Thread metadata
     *
     * @var Collection of ThreadMetadata
     */
    protected $metadata;

    /**
     * All text contained in the thread messages
     * Used for the full text search
     *
     * @var string
     */
    protected $keywords = '';

    /**
     * Participant that created the thread
     *
     * @var ParticipantInterface
     */
    protected $createdBy;

    /**
     * Date this thread was created at
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Initializes the collections
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->metadata = new ArrayCollection();
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
    }

    /**
     * Gets the participant that created the thread
     * Generally the sender of the first message
     *
     * @return ParticipantInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Sets the participant that created the thread
     * Generally the sender of the first message
     *
     * @param ParticipantInterface
     */
    public function setCreatedBy(ParticipantInterface $participant)
    {
        $this->createdBy = $participant;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param  \DateTime
     * @return null
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Gets the users participating in this conversation
     *
     * @return array of ParticipantInterface
     */
    public function getParticipants()
    {
        return $this->getParticipantsCollection()->toArray();
    }

    /**
     * Gets the users participating in this conversation
     *
     * @return ArrayCollection
     */
    protected function getParticipantsCollection()
    {
        if ($this->participants == null) {
            $this->participants = new ArrayCollection();

            foreach ($this->metadata as $data) {
                $this->participants->add($data->getParticipant());
            }
        }

        return $this->participants;
    }

    /**
     * Adds a participant to the thread
     * If it already exists, nothing is done.
     *
     * @param ParticipantInterface $participant
     * @return null
     */
    public function addParticipant(ParticipantInterface $participant)
    {
        if (!$this->isParticipant($participant)) {
            $this->getParticipantsCollection()->add($participant);
        }
    }

    /**
     * Tells if the user participates to the conversation
     *
     * @param ParticipantInterface $participant
     * @return boolean
     */
    public function isParticipant(ParticipantInterface $participant)
    {
        return $this->getParticipantsCollection()->contains($participant);
    }

    /**
     * Tells if this thread is deleted by this participant
     *
     * @return bool
     */
    public function isDeletedByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getThreadDeleted();
        }

        return false;
    }

    /**
     * Sets whether or not this participant has deleted this thread
     *
     * @param ParticipantInterface $participant
     * @param boolean $isDeleted
     */
    public function setIsDeletedByParticipant(ParticipantInterface $participant, $isDeleted)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            $meta->setThreadDeleted($isDeleted);

            // also mark all thread messages as read
            foreach ($this->getMessages() as $message) {
                $message->setIsReadByParticipant($participant, true);
            }
        }
    }

    public function getAllMetadata()
    {
        return $this->metadata;
    }

    public function getMetadataForParticipant($participant)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $participant->getId()) {
                return $meta;
            }
        }

        return null;
    }

    public function addMetadata(ThreadMetadata $meta)
    {
        $meta->setThread($this);
        $this->metadata->add($meta);
    }
}
