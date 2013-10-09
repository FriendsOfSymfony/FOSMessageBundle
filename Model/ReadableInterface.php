<?php

namespace FOS\MessageBundle\Model;

/**
 * An interface that indicates the object supports marking itself as read
 * by participants.
 */
interface ReadableInterface
{
    /**
     * Tells if this is read by this participant
     *
     * @param ParticipantInterface $participant
     * @return Boolean
     */
    public function isReadByParticipant(ParticipantInterface $participant);

    /**
     * Sets whether or not this participant has read this
     *
     * @param ParticipantInterface $participant
     * @param Boolean $isRead
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead);
}
