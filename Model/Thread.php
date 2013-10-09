<?php

namespace FOS\MessageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Abstract thread model
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Thread implements ThreadInterface
{
    /**
     * Unique id of the thread
     *
     * @var mixed
     */
    protected $id;

    /**
     * Text subject of the thread
     *
     * @var string
     */
    protected $subject;

    /**
     * Tells if the thread is spam or flood
     *
     * @var Boolean
     */
    protected $isSpam = false;

    /**
     * Messages contained in this thread
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $messages;

    /**
     * Thread metadata
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $metadata;

    /**
     * Users participating in this conversation
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $participants;

    /**
     * Date this thread was created at
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Participant that created the thread
     *
     * @var ParticipantInterface
     */
    protected $createdBy;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->metadata = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return ParticipantInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param ParticipantInterface $participant
     */
    public function setCreatedBy(ParticipantInterface $participant)
    {
        $this->createdBy = $participant;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return Boolean
     */
    public function getIsSpam()
    {
        return $this->isSpam;
    }

    /**
     * @param Boolean
     */
    public function setIsSpam($isSpam)
    {
        $this->isSpam = (boolean) $isSpam;
    }

    /**
     * @param MessageInterface $message
     */
    public function addMessage(MessageInterface $message)
    {
        $this->messages->add($message);
        $message->setThread($this);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return MessageInterface
     */
    public function getFirstMessage()
    {
        return $this->getMessages()->first();
    }

    /**
     * @return MessageInterface
     */
    public function getLastMessage()
    {
        return $this->getMessages()->last();
    }

    /**
     * @param ParticipantInterface $participant
     * @return Boolean
     */
    public function isDeletedByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getIsDeleted();
        }

        return false;
    }

    /**
     * @param ParticipantInterface $participant
     * @param Boolean $isDeleted
     * @throws \InvalidArgumentException
     */
    public function setIsDeletedByParticipant(ParticipantInterface $participant, $isDeleted)
    {
        if (!$meta = $this->getMetadataForParticipant($participant)) {
            throw new \InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsDeleted($isDeleted);

        if ($isDeleted) {
            // also mark all thread messages as read
            foreach ($this->getMessages() as $message) {
                $message->setIsReadByParticipant($participant, true);
            }
        }
    }

    /**
     * @param Boolean $isDeleted
     */
    public function setIsDeleted($isDeleted)
    {
        foreach ($this->getParticipants() as $participant) {
            $this->setIsDeletedByParticipant($participant, $isDeleted);
        }
    }

    /**
     * @param ParticipantInterface $participant
     * @return Boolean
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        foreach ($this->getMessages() as $message) {
            if (!$message->isReadByParticipant($participant)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param ParticipantInterface $participant
     * @param bool $isRead
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        foreach ($this->getMessages() as $message) {
            $message->setIsReadByParticipant($participant, $isRead);
        }
    }

    /**
     * Adds ThreadMetadata to the metadata collection.
     *
     * @param ThreadMetadata $meta
     */
    public function addMetadata(ThreadMetadata $meta)
    {
        $this->metadata->add($meta);
    }

    /**
     * Gets the ThreadMetadata for a participant.
     *
     * @param  ParticipantInterface $participant
     * @return ThreadMetadata
     */
    public function getMetadataForParticipant(ParticipantInterface $participant)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant() === $participant) {
                return $meta;
            }
        }

        return null;
    }

    /**
     * @param ParticipantInterface $participant
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOtherParticipants(ParticipantInterface $participant)
    {
        $otherParticipants = $this->getParticipants()->filter(function (ParticipantInterface $search) use ($participant) {
            return $search !== $participant;
        });

        return $otherParticipants;
    }

    /**
     * Gets the users participating in this conversation
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * Tells if the user participates to the conversation
     *
     * @param  ParticipantInterface $participant
     * @return boolean
     */
    public function isParticipant(ParticipantInterface $participant)
    {
        return $this->getParticipants()->contains($participant);
    }

    /**
     * Adds a participant to the thread
     * If it already exists, nothing is done.
     *
     * @param  ParticipantInterface $participant
     * @return null
     */
    public function addParticipant(ParticipantInterface $participant)
    {
        if (!$this->isParticipant($participant)) {
            $this->getParticipants()->add($participant);
        }
    }

    /**
     * Adds many participants to the thread
     *
     * @param array|\Traversable
     * @throws \InvalidArgumentException
     * @return Thread
     */
    public function addParticipants($participants)
    {
        if (!is_array($participants) && !$participants instanceof \Traversable) {
            throw new \InvalidArgumentException("Participants must be an array or instanceof Traversable");
        }

        foreach ($participants as $participant) {
            $this->addParticipant($participant);
        }
    }
}
