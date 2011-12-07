<?php

namespace Ornicar\MessageBundle\Document;

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
     * Ensures that each participant is considered to have read this message
     *
     * @param array $participants list of ParticipantInterface
     */
    public function ensureIsReadByParticipant(array $participants)
    {
        $participantsById = array();

        foreach ($participants as $participant) {
            $participantsById[$participant->getId()] = $participant;
        }

        // Set metadata.isRead for all existing participants in this message
        foreach ($this->metadata as $meta) {
            if (isset($participantsById[$meta->getParticipant()->getId()])) {
                $meta->setIsRead(true);
                unset($participantsById[$meta->getParticipant()->getId()]);
            }
        }

        // Set metadata.isRead for all unrecognized participants in this message 
        foreach ($participantsById as $participant) {
            $meta = new MessageMetadata();
            $meta->setParticipant($participant);
            $meta->setIsRead(true);
            $this->metadata->add($meta);
        }
    }

    /**
     * @param  boolean
     * @return null
     */
    public function setIsSpam($isSpam)
    {
        $this->isSpam = (boolean) $isSpam;
    }

    protected function getMetadataForParticipant($participant)
    {
        foreach ($this->metadata as $meta) {
            if ($meta->getParticipant()->getId() == $participant->getId()) {
                return $meta;
            }
        }

        return null;
    }
}
