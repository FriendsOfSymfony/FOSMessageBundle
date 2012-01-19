<?php

namespace Ornicar\MessageBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Ornicar\MessageBundle\Model\Message as AbstractMessage;
use Ornicar\MessageBundle\Model\ParticipantInterface;

abstract class Message extends AbstractMessage
{
    /**
     * Message metadata
     *
     * @var Collection of MessageMetadata
     */
    protected $metadata;

    /**
     * Tells if the message is spam or flood
     * This denormalizes Thread.isSpam
     *
     * @var boolean
     */
    protected $isSpam = false;

    /**
     * The unreadForParticipants array will contain a participant's ID if the
     * message is not read by the participant and the message is not spam.
     *
     * @var array of participant ID's
     */
    protected $unreadForParticipants = array();

    /**
     * Initializes the collections
     */
    public function __construct()
    {
        parent::__construct();
        $this->metadata = new ArrayCollection();
    }

    /**
     * Tells if this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @return boolean
     */
    public function isReadByParticipant(ParticipantInterface $participant)
    {
        if ($meta = $this->getMetadataForParticipant($participant)) {
            return $meta->getIsRead();
        }

        return false;
    }

    /**
     * Sets whether or not this participant has read this message
     *
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     * @throws InvalidArgumentException if no metadata exists for the participant
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead)
    {
        if (!$meta = $this->getMetadataForParticipant($participant)) {
            throw new \InvalidArgumentException(sprintf('No metadata exists for participant with id "%s"', $participant->getId()));
        }

        $meta->setIsRead($isRead);
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
     * @param ParticipantInterface $participant
     * @return MessageMetadata
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
     * @param MessageMetadata $meta
     */
    public function addMetadata(MessageMetadata $meta)
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
        $this->doSenderIsRead();
        $this->doEnsureUnreadForParticipantsArray();
    }

    /**
     * Ensures that the sender is considered to have read this message
     */
    public function doSenderIsRead()
    {
        $this->setIsReadByParticipant($this->getSender(), true);
    }

    /**
     * Ensures that the unreadForParticipants array is updated.
     */
    protected function doEnsureUnreadForParticipantsArray()
    {
        $this->unreadForParticipants = array();

        if ($this->isSpam) {
            return;
        }

        foreach ($this->metadata as $metadata) {
            if (!$metadata->getIsRead()) {
                $this->unreadForParticipants[] = $metadata->getParticipant()->getId();
            }
        }
    }
}
