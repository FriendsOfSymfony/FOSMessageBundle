<?php

namespace Ornicar\MessageBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ornicar\MessageBundle\Model\ParticipantInterface;

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
     * @var boolean
     */
    protected $isSpam = false;

    /**
     * Messages contained in this thread
     *
     * @var Collection of MessageInterface
     */
    protected $messages;

    /**
     * Thread metadata
     *
     * @var Collection of ThreadMetadata
     */
    protected $metadata;

    /**
     * Users participating in this conversation
     *
     * @var Collection of ParticipantInterface
     */
    protected $participants;

    /**
     * Date this thread was created at
     *
     * @var DateTime
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
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getId()
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getCreatedAt()
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::setCreatedAt()
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getCreatedBy()
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::setCreatedBy()
     */
    public function setCreatedBy(ParticipantInterface $participant)
    {
        $this->createdBy = $participant;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getSubject()
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::setSubject()
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return boolean
     */
    public function getIsSpam()
    {
        return $this->isSpam;
    }

    /**
     * @param  boolean
     * @return null
     */
    public function setIsSpam($isSpam)
    {
        $this->isSpam = (boolean) $isSpam;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::addMessage()
     */
    public function addMessage(MessageInterface $message)
    {
        $this->messages->add($message);
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getMessages()
     */
    public function getMessages()
    {
        return $this->messages->toArray();
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getFirstMessage()
     */
    public function getFirstMessage()
    {
        $messages = $this->getMessages();

        return empty($messages) ? null : reset($messages);
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getLastMessage()
     */
    public function getLastMessage()
    {
        $messages = $this->getMessages();

        return empty($messages) ? null : end($messages);
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::isDeletedByParticipant()
     */
    public function isDeletedByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getIsDeleted();
        }

        return false;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::setIsDeletedByParticipant()
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
     * @see Ornicar\MessageBundle\Model\ThreadInterface::setIsDeleted()
     */
    public function setIsDeleted($isDeleted)
    {
        foreach($this->getParticipants() as $participant) {
            $this->setIsDeletedByParticipant($participant, $isDeleted);
        }
    }

    /**
     * @see Ornicar\MessageBundle\Model\ReadableInterface::isReadByParticipant()
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
     * @see Ornicar\MessageBundle\Model\ReadableInterface::setIsReadByParticipant()
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
     * @param ParticipantInterface $participant
     * @return ThreadMetadata
     */
    public function getMetadataForParticipant(ParticipantInterface $participant)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $participant->getId()) {
                return $meta;
            }
        }

        return null;
    }

    /**
     * @see Ornicar\MessageBundle\Model\ThreadInterface::getOtherParticipants()
     */
    public function getOtherParticipants(ParticipantInterface $participant)
    {
        $otherParticipants = $this->getParticipants();

        unset($otherParticipants[array_search($participant, $otherParticipants, true)]);

        // we want to reset the array indexes
        return array_values($otherParticipants);
    }
}
