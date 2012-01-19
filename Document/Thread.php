<?php

namespace Ornicar\MessageBundle\Document;

use Ornicar\MessageBundle\Model\Thread as AbstractThread;
use Ornicar\MessageBundle\Model\MessageInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Ornicar\MessageBundle\Model\ParticipantInterface;

abstract class Thread extends AbstractThread
{
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
     * Date that the last message in this thread was created at
     *
     * This denormalization field is used for sorting threads in the inbox and
     * sent list.
     *
     * @var \DateTime
     */
    protected $lastMessageDate;

    /**
     * All text contained in the thread messages
     * Used for the full text search
     *
     * @var string
     */
    protected $keywords = '';

    /**
     * The activeParticipants array is a union of the activeRecipients and
     * activeSenders arrays.
     *
     * @var array of participant ID's
     */
    protected $activeParticipants = array();

    /**
     * The activeRecipients array will contain a participant's ID if the thread
     * is not deleted for the participant, the thread is not spam and at least
     * one message in the thread is not created by the participant.
     *
     * @var array of participant ID's
     */
    protected $activeRecipients = array();

    /**
     * The activeSenders array will contain a participant's ID if the thread is
     * not deleted for the participant and at least one message in the thread
     * is created by the participant.
     *
     * @var array of participant ID's
     */
    protected $activeSenders = array();

    /**
     * Initializes the collections
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->metadata = new ArrayCollection();
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
        return $this->participants->toArray();
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
            $this->participants->add($participant);
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
        return $this->participants->contains($participant);
    }

    /**
     * Tells if this thread is deleted by this participant
     *
     * @return bool
     */
    public function isDeletedByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getIsDeleted();
        }

        return false;
    }

    /**
     * Sets whether or not this participant has deleted this thread
     *
     * @param ParticipantInterface $participant
     * @param boolean $isDeleted
     * @throws InvalidArgumentException if no metadata exists for the participant
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
     * @param ThreadMetadata $meta
     */
    public function addMetadata(ThreadMetadata $meta)
    {
        $this->metadata->add($meta);
    }

    /**
     * DENORMALIZATION
     *
     * All following methods are relative to denormalization
     */

    /**
     * Performs denormalization tricks
     */
    public function denormalize()
    {
        $this->doCreatedByAndAt();
        $this->doLastMessageDate();
        $this->doKeywords();
        $this->doSpam();
        $this->doMetadataLastMessageDates();
        $this->doEnsureActiveParticipantArrays();
    }

    /**
     * Ensures that the createdBy & createdAt properties are set
     */
    protected function doCreatedByAndAt()
    {
        if (null !== $this->getCreatedBy()) {
            return;
        }

        if (!$message = $this->getFirstMessage()) {
            return;
        }

        $this->setCreatedBy($message->getSender());
        $this->setCreatedAt($message->getCreatedAt());
    }

    /**
     * Ensures that the lastMessageDate property is up to date
     */
    protected function doLastMessageDate()
    {
        if (!$message = $this->getLastMessage()) {
            return;
        }

        $this->lastMessageDate = $message->getCreatedAt();
    }

    /**
     * Adds all messages contents to the keywords property
     */
    protected function doKeywords()
    {
        $keywords = $this->getSubject();

        foreach ($this->getMessages() as $message) {
            $keywords .= ' '.$message->getBody();
        }

        // we only need each word once
        $this->keywords = implode(' ', array_unique(str_word_count(mb_strtolower($keywords, 'UTF-8'), 1)));
    }

    /**
     * Denormalizes the value of isSpam to messages
     */
    protected function doSpam()
    {
        foreach ($this->getMessages() as $message) {
            $message->setIsSpam($this->getIsSpam());
        }
    }

    /**
     * Ensures that metadata last message dates are up to date
     *
     * Precondition: metadata exists for all thread participants
     */
    protected function doMetadataLastMessageDates()
    {
        foreach ($this->metadata as $meta) {
            foreach ($this->getMessages() as $message) {
                if ($meta->getParticipant()->getId() !== $message->getSender()->getId()) {
                    if (null === $meta->getLastMessageDate() || $meta->getLastMessageDate()->getTimestamp() < $message->getTimestamp()) {
                        $meta->setLastMessageDate($message->getCreatedAt());
                    }
                } else {
                    if (null === $meta->getLastParticipantMessageDate() || $meta->getLastParticipantMessageDate()->getTimestamp() < $message->getTimestamp()) {
                        $meta->setLastParticipantMessageDate($message->getCreatedAt());
                    }
                }
            }
        }
    }

    /**
     * Ensures that active participant, recipient and sender arrays are updated.
     */
    protected function doEnsureActiveParticipantArrays()
    {
        $this->activeParticipants = array();
        $this->activeRecipients = array();
        $this->activeSenders = array();

        foreach ($this->getParticipants() as $participant) {
            if ($this->isDeletedByParticipant($participant)) {
                continue;
            }

            $participantIsActiveRecipient = $participantIsActiveSender = false;

            foreach ($this->getMessages() as $message) {
                if ($message->getSender()->getId() == $participant->getId()) {
                    $participantIsActiveSender = true;
                } elseif (!$this->getIsSpam()) {
                    $participantIsActiveRecipient = true;
                }

                if ($participantIsActiveRecipient && $participantIsActiveSender) {
                    break;
                }
            }

            if ($participantIsActiveSender) {
                $this->activeSenders[] = $participant->getId();
            }

            if ($participantIsActiveRecipient) {
                $this->activeRecipients[] = $participant->getId();
            }

            if ($participantIsActiveSender || $participantIsActiveRecipient) {
                $this->activeParticipants[] = $participant->getId();
            }
        }
    }
}
