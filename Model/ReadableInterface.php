<?php

namespace Ornicar\MessageBundle\Model;

use FOS\UserBundle\Model\UserInterface;

interface ReadableInterface
{
    /**
     * Tells if this is read by this participant
     *
     * @return bool
     */
    function isReadByParticipant(UserInterface $participant);

    /**
     * Sets whether or not this participant has read this message
     *
     * @param UserInterface $participant
     * @param boolean $isRead
     */
    function setIsReadByParticipant(UserInterface $participant, $isRead);
}
