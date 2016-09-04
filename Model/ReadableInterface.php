<?php

namespace FOS\MessageBundle\Model;

interface ReadableInterface
{
    /**
     * Tells if this is read by this participant
     *
     * @return bool
     */
    public function isReadByParticipant(ParticipantInterface $participant);

    /**
     * Sets whether or not this participant has read this
     *
     * @param ParticipantInterface $participant
     * @param boolean $isRead
     */
    public function setIsReadByParticipant(ParticipantInterface $participant, $isRead);
}
